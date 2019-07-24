<template>
  <v-card>
    <v-toolbar flat class="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>

    <v-stepper v-model="step">
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1">Factuur informatie</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="2">Data & Duur</v-stepper-step>
      </v-stepper-header>

      <v-stepper-items>
        <v-stepper-content step="1">
          <v-layout wrap>
            <v-flex xs12 sm6 md6>
              <v-text-field v-model="editedItem.ref" label="Referentie"></v-text-field>
            </v-flex>
            <v-flex xs12 sm6 md6>
              <v-select
                :items="paymentStatuses"
                v-model="editedItem.payment_status"
                label="Status van betaling"
              ></v-select>
            </v-flex>
          </v-layout>

          <v-layout wrap>
            <v-flex xs12>
              <v-textarea v-model="editedItem.note" label="Notitie toevoegen"></v-textarea>
            </v-flex>
          </v-layout>

          <v-btn color="primary" @click="step = 2">Verder</v-btn>
          <v-btn flat @click="cancel">Annuleren</v-btn>
        </v-stepper-content>

        <v-stepper-content step="2">
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
          <v-btn
            :dark="!working"
            color="primary"
            :loading="working"
            :disabled="working"
            @click="save"
          >Opslaan</v-btn>
          <v-btn dark depressed color="primary--text" @click="cancel">Annuleren</v-btn>
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

  @Prop()
  customer_id: number;

  @Prop()
  units: any;

  private paymentStatuses: string[] = ["paid", "unpaid"];
  private showEndDate: boolean = false;
  private step: number = 0;
  private working: boolean = false;
  private customer: any;
  private editedItem: any = {
    id: null,
    payment_status: "",
    ref: "",
    note: "",
    start_date: "",
    end_date: ""
  };
  private defaultItem: any = {
    id: null,
    payment_status: "",
    ref: "",
    note: "",
    start_date: "",
    end_date: ""
  };
  private response = "";

  get formTitle() {
    return "De gegevens van " + this.invoice.ref + " bewerken";
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
    this.editedItem.customer_id = this.customer_id;
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
