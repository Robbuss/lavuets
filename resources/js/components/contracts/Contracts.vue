<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Contracten</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Contract toevoegen</span>
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
        :items="contracts"
        class="elevation-1"
        :loading="loading"
        rows-per-page-text="Contracten per pagina"
        :pagination.sync="pagination"
      >
        <template v-slot:items="props">
          <td
            class="pointer"
            @click="$router.push('/units/' + props.item.unit_id)"
          >{{ props.item.unit_name }}</td>
          <td
            class="pointer"
            @click="$router.push('/customers/' + props.item.customer_id)"
          >{{ props.item.customer_name }}</td>
          <td>€{{ props.item.price }}</td>
          <td>{{ props.item.start_date }}</td>
          <td>{{ props.item.end_date }}</td>
          <td class="justify-center layout px-0">
            <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
            <v-icon small @click="deleteItem(props.item)">delete</v-icon>
          </td>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Contracten laden...</td>
          <td colspan="100%" v-else>Geen contracten</td>
        </template>
      </v-data-table>
    </div>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Contracts extends Vue {
  private response = "";
  private contracts: any = [];
  private customers: any = [];
  private units: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private showEndDate: boolean = false;
  private step: number = 0;

  private headers: any = [
    { text: "Box", value: "unit_name" },
    { text: "Naam", value: "customer_name" },
    { text: "Prijs", value: "price" },
    { text: "Ingangsdatum", value: "start_date" },
    { text: "Einddatum", value: "end_date" },
    { text: "Actions", value: "name", sortable: false }
  ];
  private editedIndex: number = -1;
  private editedItem: any = {
    id: null,
    unit_id: "",
    unit_name: "",
    customer_id: "",
    customer_name: "",
    price: 0,
    start_date: "",
    end_date: ""
  };
  private defaultItem: any = {
    id: null,
    unit_id: "",
    unit_name: "",
    customer_id: "",
    customer_name: "",
    price: 0,
    start_date: "",
    end_date: ""
  };
  private pagination: any = {
    rowsPerPage: 25
  };

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  get chosenUnitName() {
    return this.units.find((x: any) => x.id === this.editedItem.unit_id).name;
  }

  get chosenCustomerName() {
    return this.customers.find((x: any) => x.id === this.editedItem.customer_id)
      .name;
  }

  async mounted() {
    try {
      const r = (await axios.get("/api/contracts")).data;
      this.contracts = r.contracts;
      this.customers = r.customers;
      this.units = r.units;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  get formTitle() {
    return this.editedIndex === -1 ? "Contract aanmaken" : "Contract bewerken";
  }

  editItem(item: any) {
    this.editedIndex = this.contracts.indexOf(item);
    this.editedItem = Object.assign({}, item);
    this.dialog = true;
  }

  deleteItem(item: any) {
    const index = this.contracts.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.contracts.splice(index, 1) &&
      axios.post("/api/contracts/" + item.id + "/delete");
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
      axios.post("/api/contracts/" + this.editedItem.id, this.editedItem);
      Object.assign(this.contracts[this.editedIndex], this.editedItem);
    } else {
      axios.post("/api/contracts/create", this.editedItem);
      this.contracts.push(this.editedItem);
    }
    this.close();
  }
}
</script>
