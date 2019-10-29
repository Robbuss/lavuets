<template>
  <v-dialog :value="dialog" max-width="80%" persistent>
    <v-card class="pa-3">
      <v-form ref="form" v-model="valid" lazy-validation>
        <v-layout row wrap>
          <v-flex xs12>
            <h6 class="caption">
              Welke
              <span class="font-weight-black">producten</span>
              komen in dit contract?
            </h6>
            <v-autocomplete :items="mergedUnits" v-model="contract.units" multiple chips>
              <template v-slot:item="data">
                {{ data.item.display_name }}
                <span v-if="!data.item.active">- Non actief</span>
              </template>
              <template v-slot:selection="data">
                <v-chip close @input="remove(data.item)">
                  {{ data.item.display_name }}
                  <span v-if="!data.item.active">- Non actief!</span>
                </v-chip>
              </template>
            </v-autocomplete>
          </v-flex>
          <v-flex xs12>
            <v-checkbox v-model="defaultPrices" label="Standaard prijzen gebruiken"></v-checkbox>
          </v-flex>
          <v-flex xs12 v-if="contract.units.length > 0 && !defaultPrices">
            Wat is de overeengekomen prijs in € per maand per box (deze kan afwijken van de standaard prijs)
            <v-text-field
              v-for="(c, i) in contract.units"
              :key="i"
              v-model.number="c.price"
              :label="c.display_name"
              placeholder="Overeengekomen prijs"
              :rules="[v => (v.length !== 0) || 'Dit veld mag niet leeg zijn']"
              required
            ></v-text-field>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <v-flex xs12 sm12 md4>
            <h6 class="caption">
              Kies een
              <span class="font-weight-black">betalingsmethode</span>?
            </h6>
            <v-autocomplete
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              :items="['factuur', 'incasso']"
              v-model="contract.payment_method"
            ></v-autocomplete>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <v-flex xs12 sm12 md4 v-if="!contract.id">
            <h6 class="caption">
              Kies een
              <span class="font-weight-black">huurder</span>
            </h6>
            <v-autocomplete
              :items="tenants"
              item-value="id"
              item-text="name"
              v-model="contract.tenant_id"
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              color="blue-grey lighten-2"
            ></v-autocomplete>
          </v-flex>
        </v-layout>

        <v-layout row wrap>
          <v-flex xs12>
            <h6 class="caption">
              Moet de klant automatisch facturen
              <span class="font-weight-black">per mail</span> ontvangen
            </h6>
            <span></span>
            <v-checkbox v-model="contract.auto_invoice" label="Automagische facturatie" />
          </v-flex>
        </v-layout>

        <v-layout wrap>
          <v-flex xs12>
            <h6 class="caption">
              Kies een
              <span class="font-weight-black">startdatum</span>
              van het contract
            </h6>
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
              :return-value.sync="contract.start_date"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                  :value="formatDate"
                  prepend-icon="event"
                  :rules="[v => !!v || 'Je moet een startdatum kiezen']"
                  readonly
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker locale="nl" v-model="contract.start_date" no-title scrollable>
                <v-spacer></v-spacer>
                <v-btn text color="primary" @click="$refs.menu.save(contract.start_date)">OK</v-btn>
              </v-date-picker>
            </v-menu>
          </v-flex>
        </v-layout>

        <v-btn color="primary" @click="save">Opslaan</v-btn>
        <v-btn flat @click="$emit('input')">Annuleren</v-btn>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import * as moment from "moment";

@Component({})
export default class EditCreateContract extends Vue {
  @Prop()
  dialog: boolean;

  @Prop()
  contract: any;

  private response = "";
  private tenants: any = [];
  private units: any = [];
  private mergedUnits: any = [];
  private loading: boolean = true;
  private valid: boolean = true;
  private menu: boolean = false;
  private defaultPrices: boolean = true;

  get formatDate() {
    return this.contract.start_date
      ? moment(this.contract.start_date).format("DD-MM-YYYY")
      : "";
  }

  get formTitle() {
    return this.contract.id ? "Contract aanmaken" : "Contract bewerken";
  }

  remove(data: any) {
    this.contract.units.splice(data, 1);
    this.mergeUnits();
  }

  mergeUnits() {
    this.mergedUnits = this.units.free;
    if (this.contract.id) {
      this.mergedUnits = this.contract.units.concat(this.units.free);
    }
  }

  async mounted() {
    if (!this.contract.payment_method) this.contract.payment_method = "incasso";
    try {
      // refactor this to an api call that gets the units in {free: [], occupied: []} and tenants
      const r = (await axios.get("/api/contracts")).data;
      this.tenants = r.tenants;
      this.units = r.units;
    } catch (e) {
      this.response = e.message;
    }
    this.mergeUnits();
    this.loading = false;
  }

  validate() {
    return (<any>this.$refs.form).validate() ? true : false;
  }

  async save() {
    if (!this.validate()) return;
    if (this.contract.id) {
      axios.post("/api/contracts/" + this.contract.id, this.contract);
      store.commit("snackbar", {
        type: "success",
        message: "Contract aangepast!"
      });
    } else {
      axios.post("/api/contracts/create", this.contract);
      store.commit("snackbar", {
        type: "success",
        message: "Contract aangemaakt!"
      });
    }
    this.$emit("input");
  }
}
</script>
