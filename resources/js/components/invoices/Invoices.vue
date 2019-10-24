<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
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
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="$router.push('/invoices')">
            <v-icon>info</v-icon>
          </v-btn>
          <span>Alle facturen van alle klanten bekijken</span>
        </v-tooltip>
        <v-tooltip bottom v-if="contract && contract.customer_id">
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Nieuwe factuur maken</span>
        </v-tooltip>
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
            Wil je de factuur versturen naar {{ contract.customer.email }}?
            <v-btn color="primary" dark @click="sendInvoice">
              <v-icon left>mail</v-icon>Ja, versturen
            </v-btn>
            <v-btn @click="close">Nee, laat maar</v-btn>
          </v-card>
        </v-dialog>
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
        <template v-slot:items="props">
          <tr @mouseover="showQuickEdit = props.item.id" @mouseleave="showQuickEdit = null">
            <td>
              <v-layout row justify-center align-center>
                <v-text-field
                  v-model="props.item.ref_number"
                  :disabled="props.item.id !== quickEdit"
                >
                  <template v-slot:append v-if="props.item.id === quickEdit">
                    <v-btn icon @click="saveInvoice(props.item)">
                      <v-icon>save</v-icon>
                    </v-btn>
                  </template>
                </v-text-field>
                <v-btn
                  icon
                  small
                  class="grey--text"
                  @click="quickEdit = props.item.id"
                  v-if="props.item.id !== quickEdit && showQuickEdit === props.item.id"
                >
                  <v-icon small>edit</v-icon>
                </v-btn>
              </v-layout>
            </td>
            <td>
              <PaymentStatusChip :payment="props.item.payments[0]" />
            </td>
            <td>{{ props.item.customer.name }}</td>
            <td>{{ getUnits(props.item.contract.units) }}</td>
            <td>€{{ props.item.contract.price }}</td>
            <td>{{ formatDate(props.item.start_date, 'LL') }}</td>
            <td>{{ formatDate(props.item.end_date, 'LL') }}</td>
            <td v-if="props.item.sent">{{ formatDate(props.item.sent, 'LLL') }}</td>
            <td v-else>Niet verzonden</td>
            <td>
              <v-layout align-center>
                <v-btn class="primary--text" small icon @click="editItem(props.item)">
                  <v-icon small>edit</v-icon>
                </v-btn>
                <v-btn class="primary--text" small icon @click="editItem(props.item)">
                  <v-icon small @click="deleteItem(props.item)">delete</v-icon>
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

  @Prop()
  units: any;

  private search = "";
  private response = "";
  private invoices: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private quickEdit: number | null = null;
  private showQuickEdit: number | null = null;

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
    { text: "Producten", value: "contract.units" },
    { text: "Prijs", value: "contract.price" },
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

  getUnits(unit: any) {
    return unit.map((x: any) => x.name).join(", ");
  }

  confirmSendingInvoice(invoice: any) {
    this.confirmSend = true;
    this.editedItem = invoice;
  }

  async saveInvoice(invoice: any) {
    this.quickEdit = null;
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
        customer_id: this.contract ? this.contract.customer_id : null
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
     
    store.commit("snackbar", { type: "success", message: "Invoice verwijderd!" });
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
