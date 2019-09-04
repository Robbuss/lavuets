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
          <v-btn icon slot="activator" router href="/invoices" target="_blank">
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
              flat
              :class="{
            'white--text green' : props.item.payment.payment_id}"
            >{{ props.item.payment.payment_id }}</v-chip>
          </td>
          <td>{{ props.item.customer.name }}</td>
          <td>{{ getUnits(props.item.contract.units) }}</td>
          <td>€{{ props.item.contract.price }}</td>
          <td>{{ props.item.sent }}</td>
          <td>{{ props.item.start_date }}</td>
          <td>{{ props.item.end_date }}</td>
          <td class="justify-center layout px-0">
            <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
            <v-icon small @click="deleteItem(props.item)">delete</v-icon>
            <v-icon small @click="download(props.item)">attach_file</v-icon>
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
  private editedItem: any = null;
  private pagination: any = {
    rowsPerPage: 25
  };
  private headers: any = [
    { text: "Status", value: "payment.payment_id" },
    { text: "Naam", value: "invoice.name" },
    { text: "Producten", value: "contract.units" },
    { text: "Prijs", value: "contract.price" },
    { text: "Verzonden", value: "sent" },
    { text: "Van", value: "start_date" },
    { text: "Tot", value: "end_date" },
    { text: "Actions", sortable: false }
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
