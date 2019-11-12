<template>
  <v-flex sm12>
    <div>
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
        <v-tooltip bottom v-if="contract && contract.tenant_id">
          <v-btn icon slot="activator" @click="$router.push('/invoices')">
            <v-icon>info</v-icon>
          </v-btn>
          <span>Alle facturen van alle klanten bekijken</span>
        </v-tooltip>
        <v-tooltip bottom v-if="contract && contract.tenant_id">
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Nieuwe factuur maken</span>
        </v-tooltip>
      </v-toolbar>
      <v-data-table
        :search="search"
        :headers="headers"
        :items="invoices"
        class="elevation-1"
        :loading="loading"
        rows-per-page-text="Facturen per pagina"
        :pagination.sync="pagination"
      >
        <template v-slot:actions-prepend v-if="contract && contract.tenant_id">
          <div style="position:absolute; left:0px; bottom:7px;">
            <v-btn small flat class="primary" @click="dialog = true">Nieuwe factuur</v-btn>
          </div>
        </template>
        <template v-slot:items="props">
          <tr>
            <td>{{ props.item.ref_number }}</td>
            <td>
              <PaymentStatusChip :payment="props.item.payments[0]" />
            </td>
            <td>{{ props.item.tenant.name }}</td>
            <td
              v-if="!contract"
              @click="$router.push('/contracts/' + props.item.contract_id)"
              class="pointer"
            >{{ getUnits(props.item.units) }}</td>
            <td v-else>{{ getUnits(props.item.units) }}</td>
            <td>€{{ formatMoney(props.item.price) }}</td>
            <td>{{ formatDate(props.item.start_date, 'LL') }}</td>
            <td>{{ formatDate(props.item.end_date, 'LL') }}</td>
            <td v-if="props.item.sent">{{ formatDate(props.item.sent, 'LL') }}</td>
            <td v-else>Niet verzonden</td>
            <td>
              <v-layout align-center>
                <v-btn class="primary--text" small icon @click="editItem(props.item)">
                  <v-icon small>edit</v-icon>
                </v-btn>
                <v-btn class="primary--text" small icon @click="deleteItem(props.item)">
                  <v-icon small>delete</v-icon>
                </v-btn>
                <v-btn class="primary--text" small icon @click="download(props.item)">
                  <v-icon small>attach_file</v-icon>
                </v-btn>
                <v-btn
                  class="primary--text"
                  :disabled="props.item.sent ? true : false"
                  small
                  icon
                  @click="confirmSendingInvoice(props.item)"
                >
                  <v-icon small>mail</v-icon>
                </v-btn>
              </v-layout>
            </td>
          </tr>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Facturen laden...</td>
          <td colspan="100%" @click="$emit('generate')" v-else>Geen facturen gevonden</td>
        </template>
      </v-data-table>
    </div>

    <v-dialog v-model="dialog" max-width="80%" persistent>
      <edit-create-invoice
        v-if="dialog"
        @saved="createdItem"
        @canceled="close"
        :creating="createMode"
        :contract="contract"
        :units="units"
        :invoice="editedItem"
      ></edit-create-invoice>
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
  </v-flex>
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
    editCreateInvoice: EditCreateInvoice,
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

  private search = "";
  private response = "";
  private invoices: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;

  private createMode: boolean = true;
  private confirmSend: boolean = false;
  private editedItem: any = null;
  private pagination: any = {
    rowsPerPage: 25,
    descending: true,
    sortBy: "ref_number"
  };
  private headers: any = [
    { text: "Factuur nr", value: "invoice.ref_number" },
    { text: "Betaling", value: "payment.payment_id" },
    { text: "Naam", value: "invoice.name" },
    { text: "Producten", value: "units" },
    { text: "Bedrag", value: "price" },
    { text: "Van", value: "start_date" },
    { text: "Tot", value: "end_date" },
    { text: "Verzonden", value: "sent" },
    { text: "Acties", sortable: false }
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
    const index = this.invoices.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.invoices.splice(index, 1) &&
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
