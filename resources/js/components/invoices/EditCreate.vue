<template>
  <v-card>
    <v-toolbar flat class="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>

    <v-stepper v-model="step">
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1" @click="navigate(1)">Factuur informatie</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="2" @click="navigate(2)">Data & Duur</v-stepper-step>
      </v-stepper-header>

      <v-stepper-items>
        <v-stepper-content step="1">
          <v-form ref="form1" v-model="valid" lazy-validation>
            <v-layout wrap>
              <v-flex xs12 sm6 md6>
                <v-text-field
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  v-model="editedItem.ref"
                  label="Referentie (bestandsnaam)"
                ></v-text-field>
              </v-flex>
              <v-flex xs12 sm6 md6  v-if="!creating">
                <v-text-field
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  v-model="editedItem.ref_number"
                  label="Referentie nummer administratie"
                ></v-text-field>
              </v-flex>              
            </v-layout>

            <v-layout wrap>
              <v-flex xs12>
                <v-textarea v-model="editedItem.note" label="Notitie toevoegen"></v-textarea>
              </v-flex>
            </v-layout>

            <v-layout wrap>
              <v-flex xs12 v-if="showSentDate">
                Deze factuur is verzonden op:
                <v-menu
                  ref="datePicker"
                  v-model="datePicker"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  lazy
                  transition="scale-transition"
                  offset-y
                  full-width
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      box
                      v-model="formattedDate"
                      :rules="[
                        v => !!v || 'Dit veld mag niet leeg zijn',
                        v => isInPast || 'Datum moet in het verleden liggen!'
                        ]"
                      required
                      label="Verzenddatum factuur"
                      @blur="$emit('formatDate')"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="editedItem.sent" no-title @input="datePicker = false"></v-date-picker>
                </v-menu>
              </v-flex>
              <v-flex xs12>
                <v-checkbox v-model="showSentDate" label="Deze factuur is al verzonden"></v-checkbox>
              </v-flex>
            </v-layout>

            <v-btn color="primary" @click="navigate(2)">Verder</v-btn>
            <v-btn flat @click="cancel">Annuleren</v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-content step="2">
          <v-form ref="form2" v-model="valid" lazy-validation>
            <v-layout row wrap>
              <v-flex xs12 sm6>
                <h6 class="headline">
                  Kies een
                  <span class="font-weight-black">startdatum</span>
                </h6>
                <v-date-picker landscape v-model="editedItem.start_date" label="Startdatum factuur"></v-date-picker>
              </v-flex>
              <v-flex xs12 sm6>
                <h6 class="headline">
                  Kies een
                  <span class="font-weight-black">einddatum</span>
                </h6>
                <v-date-picker landscape v-model="editedItem.end_date" label="Einddatum factuur"></v-date-picker>
              </v-flex>
            </v-layout>
            <v-btn
              :dark="!working"
              color="primary darken-1"
              :loading="working"
              :disabled="working"
              @click="save"
            >Opslaan</v-btn>
            <v-btn flat color="primary darken-1" @click="cancel">Annuleren</v-btn>
          </v-form>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import * as moment from "moment";

@Component({})
export default class EditInvoice extends Vue {
  @Prop()
  invoice: any;

  @Prop()
  creating: boolean;

  @Prop()
  contract: any;

  private valid: boolean = true;
  private step: number = 0;
  private datePicker: boolean = false;
  private showSentDate: boolean = false;
  private working: boolean = false;
  private tenant: any;
  private editedItem: any = {
    id: null,
    contract_id: null,
    payment_id: null,
    ref: "",
    ref_number: "",
    sent: null,
    note: "",
    start_date: moment()
      .subtract(1, "months")
      .format("YYYY-MM-DD"),
    end_date: moment().format("YYYY-MM-DD")
  };
  private defaultItem: any = {
    id: null,
    contract_id: null,
    payment_status: "unpaid",
    ref: "",
    ref_number: "",
    note: "",
    sent: null,
    start_date: moment()
      .subtract(1, "months")
      .format("YYYY-MM-DD"),
    end_date: moment().format("YYYY-MM-DD")
  };
  private response = "";

  @Watch('showSentDate')
  onShowSentDateChanged(newval: boolean, oldval: boolean){
    if(!newval && oldval){
      this.editedItem.sent = null;
    }
  }

  get formTitle() {
    return this.invoice
      ? this.invoice.ref + " bewerken"
      : "Nieuwe factuur maken";
  }

  get isInPast() {
    return moment().format("LL") >= this.formattedDate;
  }

  get formattedDate() {
    if(!this.editedItem.sent)
      return moment().format("LL")
    return moment(this.editedItem.sent).format("LL");
  }

  mounted() {
    if (this.invoice) {
      this.editedItem = Object.assign({}, this.invoice);
      if(this.invoice.sent)
        this.showSentDate = true;
    }
    this.editedItem.contract_id = this.contract.id;
    this.editedItem.tenant_id = this.contract.tenant_id;
  }

  editItem(item: any) {
    this.editedItem = Object.assign({}, item);
  }

  cancel() {
    this.$emit("canceled");
  }

  navigate(step: number) {
    if (this.validate()) {
      this.step = step;
    }
  }

  validate() {
    return (<any>this.$refs["form" + this.step]).validate() ? true : false;
  }

  async save() {
    if (!this.validate()) return;
    this.working = true;
    if (!this.creating) {
      await axios.post("/api/invoices/" + this.editedItem.id, this.editedItem);
      store.commit("snackbar", { type: "success", message: "Factuur aangepast!" });
    } else {
      await axios.post("/api/invoices/create", this.editedItem);
      store.commit("snackbar", { type: "success", message: "Factuur aangemaakt!" });
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
