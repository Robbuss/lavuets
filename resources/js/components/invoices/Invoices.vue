<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Facturen</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field
          class="white--text"
          v-model="search"
          append-icon="search"
          label="Search"
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
              <v-icon left>mail</v-icon>
              Ja, versturen
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
          <td>
            <v-chip
              v-if="props.item.payment.payment_id"
              flat
              :class="{
            'white--text green' : props.item.payment.payment_id}"
            >{{ props.item.payment.payment_id }}</v-chip>
            <v-chip v-else flat class="grey lighten-3">{{ props.item.payment.status }}</v-chip>
          </td>
          <td>{{ props.item.customer.name }}</td>
          <td>{{ getUnits(props.item.contract.units) }}</td>
          <td>€{{ props.item.contract.price }}</td>
          <td>{{ props.item.start_date_localized }}</td>
          <td>{{ props.item.end_date_localized }}</td>
          <td v-if="props.item.sent">{{ props.item.sent_localized }}</td>
          <td v-else>Niet verzonden</td>
          <td class="justify-center layout px-0">
            <v-btn class="primary--text" small icon @click="editItem(props.item)">
              <v-icon small>edit</v-icon>
            </v-btn>
            <v-btn class="primary--text" small icon @click="editItem(props.item)">
              <v-icon small @click="deleteItem(props.item)">delete</v-icon>
            </v-btn>
            <v-btn class="primary--text" small icon @click="download(props.item)">
              <v-icon small>attach_file</v-icon>
            </v-btn>
            <v-btn class="primary--text" :disabled="props.item.sent ? true : false" small icon @click="confirmSendingInvoice(props.item)">
              <v-icon small>mail</v-icon>
            </v-btn>
          </td>
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

@Component({
  components: {
    editCreateInvoice: EditCreateInvoice
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

  private createMode: boolean = true;
  private confirmSend: boolean = false;
  private editedItem: any = null;
  private pagination: any = {
    rowsPerPage: 25
  };
  private headers: any = [
    { text: "Status", value: "payment.payment_id" },
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

  getUnits(unit: any) {
    return unit.map((x: any) => x.name).join(", ");
  }

  confirmSendingInvoice(invoice: any){
    this.confirmSend = true;
    this.editedItem = invoice;
  }

  async sendInvoice(){
    this.confirmSend = false;
    await axios.post("/api/invoices/" + this.editedItem.id + "/send");
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
