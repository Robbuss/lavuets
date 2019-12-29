<template>
  <v-col sm="12" :class="{'pa-0': noPadding}">
    <v-toolbar flat color="primary" dark v-if="!hidetoolbar">
      <v-toolbar-title>Facturen</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-text-field
        class="white--text pt-0"
        v-model="search"
        append-icon="search"
        label="Zoeken"
        single-line
        hide-details
      ></v-text-field>
      <template v-if="contract && contract.tenant_id">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on" @click="$router.push('/invoices')">
              <v-icon>info</v-icon>
            </v-btn>
          </template>
          <span>Alle facturen van alle klanten bekijken</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on" @click="dialog = true">
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <span>Nieuwe factuur maken</span>
        </v-tooltip>
      </template>
    </v-toolbar>
    <v-data-table
      :search="search"
      :headers="headers"
      :items="invoices"
      class="elevation-1"
      :class="{'pointer' : !contract}"
      :loading="loading"
      multi-sort
      :footer-props="options"
      :sort-by="['ref_number']"
      :sort-desc="[true]"
      @click:row="!contract ? $router.push('/contracts/' + $event.contract_id): null"
    >
      <template v-slot:actions-prepend v-if="contract && contract.tenant_id">
        <div style="position:absolute; left:0px; bottom:7px;">
          <v-btn small flat class="primary" @click="dialog = true">Nieuwe factuur</v-btn>
        </div>
      </template>
      <template v-slot:item.payment="{ item }">
        <PaymentStatusChip :payment="item.payments[0]" />
      </template>
      <template v-slot:item.units="{ item }">
        {{ getUnits(item.units) }}
      </template>
      <template v-slot:item.price="{ item }">
        €{{ formatMoney(item.price) }}
      </template>
      <template v-slot:item.start_date="{ item }">
        {{ formatDate(item.start_date, 'LL') }}
      </template>
      <template v-slot:item.end_date="{ item }">
        {{ formatDate(item.end_date, 'LL') }}
      </template>
      <template v-slot:item.sent="{ item }">
        <template v-if="item.sent">{{ formatDate(item.sent, 'LL') }}</template>
        <template v-else>Niet verzonden</template>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-row align-center>
          <v-btn class="primary--text" small icon @click.stop="editItem(item)">
            <v-icon small>edit</v-icon>
          </v-btn>
          <v-btn class="primary--text" small icon @click.stop="deleteItem(item)">
            <v-icon small>delete</v-icon>
          </v-btn>
          <v-btn class="primary--text" small icon @click.stop="download(item)">
            <v-icon small>attach_file</v-icon>
          </v-btn>
          <v-btn
            class="primary--text"
            :disabled="item.sent ? true : false"
            small
            icon
            @click.stop="confirmSendingInvoice(item)"
          >
            <v-icon small>mail</v-icon>
          </v-btn>
        </v-row>
      </template>
      <template v-slot:no-data>
        <td colspan="100%" v-if="loading">Facturen laden...</td>
        <td colspan="100%" @click="$emit('generate')" v-else>Geen facturen gevonden</td>
      </template>
    </v-data-table>

    <v-dialog v-model="dialog" max-width="80%" persistent>
      <EditCreateInvoice
        v-if="dialog"
        @saved="createdItem"
        @canceled="close"
        :creating="createMode"
        :contract="contract"
        :units="units"
        :invoice="editedItem"
      />
    </v-dialog>
    <v-dialog v-model="confirmSend" max-width="80%" persistent v-if="contract">
      <v-toolbar class="primary white--text">
        <v-toolbar-title>Factuur handmatig versturen</v-toolbar-title>
      </v-toolbar>
      <v-card>
        Wil je de factuur versturen naar {{ contract.tenant.email }}?
        <v-btn color="primary" dark @click="sendInvoice">
          <v-icon left>mail</v-icon>Ja, versturen
        </v-btn>
        <v-btn @click="close">Nee, laat maar</v-btn>
      </v-card>
    </v-dialog>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import { base64StringToBlob } from "blob-util";
const saveAs: any = require("file-saver");
import EditCreateInvoice from "./EditCreate.vue";
import * as moment from "moment";
import PaymentStatusChip from "js/components/PaymentStatusChip.vue";
import store from "js/store";

@Component({
  components: {
    EditCreateInvoice,
    PaymentStatusChip
  }
})
export default class Invoices extends Vue {
  @Prop()
  contract: any;

  @Prop({ default: false })
  hidetoolbar: boolean;

  @Prop()
  units: any;

  @Prop({ default: false })
  noPadding: boolean

  private search = "";
  private response = "";
  private invoices: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;

  private createMode: boolean = true;
  private confirmSend: boolean = false;
  private editedItem: any = null;

  private options: any = {
    itemsPerPageText: "Facturen per pagina",
    itemsPerPageAllText: "Allemaal"
  };

  private headers: any = [
    { text: "Factuur nr", value: "ref_number" },
    { text: "Betaling", value: "payment" },
    { text: "Naam", value: "tenant.name" },
    { text: "Producten", value: "units" },
    { text: "Bedrag", value: "price" },
    { text: "Van", value: "start_date" },
    { text: "Tot", value: "end_date" },
    { text: "Verzonden", value: "sent" },
    { text: "Acties", value: "actions", sortable: false }
  ];

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  async mounted() {
    await this.getData();
  }

  formatDate(date: any, format: string) {
    return moment(date).format(format);
  }

  formatMoney(number: any) {
    return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
  }

  getUnits(unit: any) {
    return unit.map((x: any) => x.name).join(", ");
  }

  confirmSendingInvoice(invoice: any) {
    this.confirmSend = true;
    this.editedItem = invoice;
  }

  async saveInvoice(invoice: any) {
    await axios.post("/api/invoices/" + invoice.id, {
      ref_number: invoice.ref_number
    });
    store.commit("snackbar", {
      type: "success",
      message: "Aanpassing opgeslagen"
    });
  }

  async sendInvoice() {
    this.confirmSend = false;
    try {
      await axios.post("/api/invoices/" + this.editedItem.id + "/send");
      store.commit("snackbar", {
        type: "success",
        message: "Factuur verzonden"
      });
    } catch (e) {
      store.commit("snackbar", {
        type: "error",
        message: "Er ging iets mis tijdens het verzenden"
      });
    }
    this.getData();
    this.editedItem = null;
  }

  async getData() {
    this.loading = true;
    try {
      this.invoices = (await axios.post("/api/invoices", {
        contract_id: this.contract ? this.contract.id : null
      })).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  deleteItem(item: any) {
    if (!confirm("Are you sure you want to delete this item?")) return;
    const index = this.invoices.indexOf(item);
    this.invoices.splice(index, 1);
    axios.post("/api/invoices/" + item.id + "/delete");

    store.commit("snackbar", {
      type: "success",
      message: "Invoice verwijderd!"
    });
  }

  createItem() {
    this.createMode = true;
    this.dialog = true;
  }

  async createdItem() {
    this.dialog = false;
    await this.getData();
  }

  editItem(invoice: any) {
    this.createMode = false;
    this.editedItem = invoice;
    this.dialog = true;
  }

  close() {
    this.editedItem = null;
    this.confirmSend = false;
    this.dialog = false;
  }

  async download(invoice: any) {
    const pdf = ((await axios.get(
      "/api/invoices/" + invoice.id + "/pdf"
    )) as any).data;
    var blob = base64StringToBlob(pdf.content, pdf.mime);
    saveAs(blob, invoice.ref + "." + pdf.extension);
  }
}
</script>
