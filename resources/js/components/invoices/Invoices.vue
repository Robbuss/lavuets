<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Facturen</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Nieuwe factuur maken</span>
        </v-tooltip>
        <v-dialog v-model="dialog" max-width="80%" persistent>
          <v-stepper v-model="step">
            <v-stepper-header>
              <v-stepper-step :complete="step > 1" step="1">
                <span v-if="step == 1">Kies een box</span>
                <span v-if="step > 1">Gekozen: {{ chosenUnitName }}</span>
              </v-stepper-step>
              <v-divider></v-divider>
              <v-stepper-step :complete="step > 2" step="2">
                <span v-if="step <= 2">Kies een klant</span>
                <span v-if="step > 2">Gekozen: {{ chosenCustomerName }}</span>
              </v-stepper-step>
              <v-divider></v-divider>
              <v-stepper-step step="3">Prijs</v-stepper-step>
              <v-divider></v-divider>
              <v-stepper-step step="4">Data & Duur</v-stepper-step>
            </v-stepper-header>

            <v-stepper-items>
              <v-stepper-content step="1">
                Welke box wil je veruren?
                <v-select
                  :items="units"
                  item-value="id"
                  item-text="name"
                  v-model="editedItem.unit_id"
                ></v-select>
                <v-btn color="primary" @click="step = 2">Verder</v-btn>
                <v-btn flat @click="close">Annuleren</v-btn>
              </v-stepper-content>

              <v-stepper-content step="2">
                Aan welke klant verhuur je die?
                <v-select
                  :items="customers"
                  item-value="id"
                  item-text="name"
                  v-model="editedItem.customer_id"
                ></v-select>
                <v-btn color="primary" @click="step = 3">Verder</v-btn>

                <v-btn flat @click="close">Annuleren</v-btn>
              </v-stepper-content>

              <v-stepper-content step="3">
                <v-layout wrap>
                  <v-flex xs12 sm6 md4>
                    Wat is de overeengekomen prijs in €per maand
                    <v-text-field v-model="editedItem.price" label="Overeengekomen prijs"></v-text-field>
                  </v-flex>
                </v-layout>
                <v-btn color="primary" @click="step = 4">Verder</v-btn>
                <v-btn flat @click="close">Annuleren</v-btn>
              </v-stepper-content>
              <v-stepper-content step="4">
                <v-layout wrap>
                  <v-flex xs12>
                    <h6 class="headline" v-if="!showEndDate">
                      Kies een
                      <span class="font-weight-black">startdatum</span>
                    </h6>
                    <h6 class="headline" v-if="showEndDate">
                      Kies een
                      <span class="font-weight-black">einddatum</span>
                    </h6>
                  </v-flex>
                  <v-flex xs12 sm6 md4 v-if="!showEndDate">
                    <v-date-picker landscape v-model="editedItem.start_date" label="Ingangsdatum"></v-date-picker>
                  </v-flex>
                  <v-flex xs12 sm6 md4 v-if="showEndDate">
                    <v-date-picker
                      landscape
                      v-model="editedItem.end_date"
                      label="Is er al een einddatum bekend?"
                    ></v-date-picker>
                  </v-flex>
                  <v-flex xs12>
                    <v-checkbox label="Is er al een einddatum bekend?" v-model="showEndDate"></v-checkbox>
                  </v-flex>
                </v-layout>
                <v-btn color="primary" @click="save">Opslaan</v-btn>

                <v-btn flat @click="close">Annuleren</v-btn>
              </v-stepper-content>
            </v-stepper-items>
          </v-stepper>
        </v-dialog>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="invoices"
        class="elevation-1"
        :loading="loading"
        rows-per-page-text="Facturen per pagina"
        :pagination.sync="pagination"
      >
        <template v-slot:items="props">
          <td>{{ props.item.customer.name }}</td>
          <td>{{ props.item.unit.name }}</td>
          <td>€{{ props.item.contract.price }}</td>
          <td>{{ props.item.payment_status }}</td>
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
          <td colspan="100%" v-else>Geen facturen gevonden</td>
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

@Component({})
export default class Invoices extends Vue {
  private response = "";
  private contracts: any = [];
  private customers: any = [];
  private units: any = [];
  private invoices: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private showEndDate: boolean = false;
  private step: number = 0;
  private pagination: any = {
    rowsPerPage: 25
  };
  private headers: any = [
    { text: "Naam", value: "customer.name" },
    { text: "Box", value: "unit.name" },
    { text: "Prijs", value: "contract.price" },
    { text: "Status", value: "payment_status" },
    { text: "Van", value: "start_date" },
    { text: "Tot", value: "end_date" },
    { text: "Actions", value: "name", sortable: false }
  ];
  private editedIndex: number = -1;
  private editedItem: any = {
    id: null,
    unit_id: "",
    customer_id: "",
    contract_id: "",
    price: 0,
    start_date: "",
    end_date: ""
  };
  private defaultItem: any = {
    id: null,
    unit_id: "",
    contract_id: "",
    customer_id: "",
    price: 0,
    start_date: "",
    end_date: ""
  };

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  async mounted() {
    try {
      this.invoices = (await axios.get("/api/invoices")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  get formTitle() {
    return this.editedIndex === -1 ? "Factuur aanmaken" : "Factuur bewerken";
  }

  editItem(item: any) {
    this.editedIndex = this.invoices.indexOf(item);
    this.editedItem = Object.assign({}, item);
    this.dialog = true;
  }

  deleteItem(item: any) {
    const index = this.invoices.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.invoices.splice(index, 1) &&
      axios.post("/api/invoices/" + item.id + "/delete");
  }

  close() {
    this.dialog = false;
    this.step = 0;
    setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
    }, 300);
  }

  save() {
    if (this.editedIndex > -1) {
      axios.post("/api/invoices/" + this.editedItem.id, this.editedItem);
      Object.assign(this.invoices[this.editedIndex], this.editedItem);
    } else {
      axios.post("/api/invoices/create", this.editedItem);
      this.invoices.push(this.editedItem);
    }
    this.close();
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
