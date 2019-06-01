<template>
  <v-card>
    <v-toolbar flat class="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>

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
          <v-select :items="units" item-value="id" item-text="name" v-model="editedItem.unit_id"></v-select>
          <v-btn color="primary" @click="step = 2">Verder</v-btn>
          <v-btn flat @click="cancel">Annuleren</v-btn>
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

          <v-btn flat @click="cancel">Annuleren</v-btn>
        </v-stepper-content>

        <v-stepper-content step="3">
          <v-layout wrap>
            <v-flex xs12 sm6 md4>
              Wat is de overeengekomen prijs in €per maand
              <v-text-field v-model="editedItem.price" label="Overeengekomen prijs"></v-text-field>
            </v-flex>
          </v-layout>
          <v-btn color="primary" @click="step = 4">Verder</v-btn>
          <v-btn flat @click="cancel">Annuleren</v-btn>
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
          <v-btn dark color="blue darken-1" @click="cancel">Cancel</v-btn>
          <v-btn
            :dark="!working"
            color="blue darken-1"
            :loading="working"
            :disabled="working"
            @click="save"
          >Save</v-btn>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class EditInvoice extends Vue {
  @Prop()
  invoice: any;

  @Prop()
  creating: boolean;

  private showEndDate: boolean = false;
  private step: number = 0;
  private working: boolean = false;
  private customers: any = []
  private units: any = []
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
  private response = "";

  get formTitle() {
    return this.creating
      ? "Klant aanmaken"
      : "De gegevens van " + this.invoice.name + " bewerken";
  }

  mounted() {
    if (this.invoice) {
      this.editedItem = Object.assign({}, this.invoice);
    }
  }

  editItem(item: any) {
    this.editedItem = Object.assign({}, item);
  }

  cancel() {
    this.$emit("canceled");
  }

  async save() {
    this.working = true;
    if (!this.creating) {
      await axios.post("/api/invoices/" + this.editedItem.id, this.editedItem);
    } else {
      await axios.post("/api/invoices/create", this.editedItem);
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
