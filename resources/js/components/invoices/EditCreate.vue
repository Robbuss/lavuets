<template>
  <v-card>
    <v-toolbar flat class="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>

    <v-form ref="form" v-model="valid" lazy-validation class="pa-3">
      <v-layout wrap>
        <v-flex xs12 sm6 md6 lg5>
          <v-text-field
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
            required
            v-model="editedItem.ref"
            label="Referentie (bestandsnaam)"
          ></v-text-field>
        </v-flex>
      </v-layout>
      <v-layout row wrap>
        <v-flex xs12 sm6 md6 lg5 v-if="!creating">
          <v-text-field
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
            required
            v-model="editedItem.ref_number"
            label="Referentie nummer administratie"
          ></v-text-field>
        </v-flex>
      </v-layout>
      <v-layout wrap>
        <v-flex xs12 md6 lg5>
          <h6 class="caption">
            Kies een
            <span class="font-weight-black">startdatum</span>
            van de factuur
          </h6>
          <v-menu
            ref="datePicker"
            v-model="datePicker1"
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
                v-model="startDate"
                prepend-icon="event"
                :rules="[
                        v => !!v || 'Dit veld mag niet leeg zijn',
                        ]"
                required
                label="Verzenddatum factuur"
                @blur="$emit('formatDate')"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              locale="nl"
              v-model="editedItem.start_date"
              no-title
              @input="datePicker1 = false"
            ></v-date-picker>
          </v-menu>
        </v-flex>
      </v-layout>
      <v-layout row wrap>
        <v-flex xs12 md6 lg5>
          <h6 class="caption">
            Kies een
            <span class="font-weight-black">einddatum</span>
            van de factuur
          </h6>
          <v-menu
            ref="datePicker"
            v-model="datePicker2"
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
                v-model="endDate"
                prepend-icon="event"
                :rules="[
                        v => !!v || 'Dit veld mag niet leeg zijn',
                        ]"
                required
                label="Verzenddatum factuur"
                @blur="$emit('formatDate')"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              locale="nl"
              v-model="editedItem.end_date"
              no-title
              @input="datePicker2 = false"
            ></v-date-picker>
          </v-menu>
        </v-flex>
      </v-layout>
      <v-layout wrap>
        <v-flex xs12 md6 lg5>
          <v-textarea v-model="editedItem.note" label="Notitie toevoegen"></v-textarea>
        </v-flex>
      </v-layout>

      <v-layout wrap>
        <v-flex xs12>
          <v-checkbox v-model="showSentDate" label="Deze factuur is al verzonden"></v-checkbox>
        </v-flex>
        <v-expand-transition>
          <v-flex xs12 md6 lg5 v-if="showSentDate">
            <h6 class="caption">
              Deze factuur is
              <span class="font-weight-black">verzonden</span>
              op
            </h6>
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
                  v-model="sent"
                  prepend-icon="event"
                  :rules="[
                        v => !!v || 'Dit veld mag niet leeg zijn',
                        ]"
                  required
                  label="Verzenddatum factuur"
                  @blur="$emit('formatDate')"
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                locale="nl"
                v-model="editedItem.sent"
                no-title
                @input="datePicker = false"
              ></v-date-picker>
            </v-menu>
          </v-flex>
        </v-expand-transition>
      </v-layout>
      <v-card-actions>
        <v-btn
          :dark="!working"
          class="primary darken-1"
          :loading="working"
          :disabled="working"
          @click="save"
        >Opslaan</v-btn>
        <v-btn @click="cancel" flat color="primary darken-1">Annuleren</v-btn>
        <v-spacer></v-spacer>
        <v-btn v-if="(invoice && !invoice.credit_invoice) && !this.creating" @click="createCredit" flat color="primary">Creditfactuur maken</v-btn>
      </v-card-actions>
    </v-form>
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
  private datePicker: boolean = false;
  private datePicker1: boolean = false;
  private datePicker2: boolean = false;
  private showSentDate: boolean = false;
  private working: boolean = false;
  private tenant: any;
  private editedItem: any = {
    id: null,
    contract_id: null,
    ref: "",
    ref_number: "",
    credit_invoice: null,
    units: [],
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
    units: [],
    note: "",
    credit_invoice: null,
    sent: null,
    start_date: moment()
      .subtract(1, "months")
      .format("YYYY-MM-DD"),
    end_date: moment().format("YYYY-MM-DD")
  };
  private response = "";

  @Watch("showSentDate")
  onShowSentDateChanged(newval: boolean, oldval: boolean) {
    if (!newval && oldval) {
      this.editedItem.sent = null;
    }
    if (oldval !== undefined && newval)
      this.editedItem.sent = moment(this.invoice.sent).format("YYYY-MM-DD");
  }

  get formTitle() {
    return this.invoice
      ? this.invoice.ref + " bewerken"
      : "Nieuwe factuur maken";
  }

  get sent() {
    if (!this.editedItem.sent) return moment().format("LL");
    return moment(this.editedItem.sent).format("LL");
  }

  get startDate() {
    if (!this.editedItem.start_date) return moment().format("LL");
    return moment(this.editedItem.start_date).format("LL");
  }

  get endDate() {
    if (!this.editedItem.end_date) return moment().format("LL");
    return moment(this.editedItem.end_date).format("LL");
  }

  mounted() {
    if (this.invoice) {
      this.editedItem = Object.assign({}, this.invoice);
      if (this.invoice.sent) this.showSentDate = true;
      this.editedItem.start_date = moment(this.invoice.start_date).format(
        "YYYY-MM-DD"
      );
      this.editedItem.end_date = moment(this.invoice.end_date).format(
        "YYYY-MM-DD"
      );
    }
  }

  editItem(item: any) {
    this.editedItem = Object.assign({}, item);
  }

  cancel() {
    this.$emit("canceled");
  }

  validate() {
    return (<any>this.$refs.form).validate() ? true : false;
  }

  async createCredit() {
    this.creating = true;
    this.editedItem.credit_invoice = this.invoice.id;
    await this.save();
    this.creating = false; // probably not needed
  }

  async save() {
    if (!this.validate()) return;
    this.working = true;
    if (!this.creating) {
      await axios.post("/api/invoices/" + this.editedItem.id, this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Factuur aangepast!"
      });
    } else {
      this.editedItem.contract_id = this.contract.id;
      await axios.post("/api/invoices/create", this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Factuur aangemaakt!"
      });
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
