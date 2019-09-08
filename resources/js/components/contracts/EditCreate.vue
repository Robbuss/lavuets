<template>
  <v-dialog :value="dialog" max-width="80%" persistent>
    <v-stepper v-model="step">
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1" @click="navigate(1)">
          <span v-if="step == 1">Kies een box</span>
          <span v-if="step > 1">Check</span>
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :complete="step > 2" step="2" @click="navigate(2)">
          <span v-if="step <= 2">Kies een klant</span>
          <span v-if="step > 2">Gekozen: {{ chosenCustomerName }}</span>
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="3" @click="navigate(3)">Prijs</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="4" @click="navigate(4)">Data & Duur</v-stepper-step>
      </v-stepper-header>

      <v-stepper-items>
        <v-stepper-content step="1">
          <v-form ref="form1" v-model="valid" lazy-validation>
            Welke producten wil je verhuren?
            <v-select
              :items="mergeUnits"
              v-model="contract.units"
              item-value="id"
              item-text="display_name"
              multiple
              chips
              @input="unitsChanged"
            ></v-select>
            <v-btn color="primary" @click="navigate(2)">Verder</v-btn>
            <v-btn flat @click="$emit('input')">Annuleren</v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-content step="2">
          <v-form ref="form2" v-model="valid" lazy-validation>
            Aan welke klant verhuur je die?
            <v-select
              :items="customers"
              item-value="id"
              item-text="name"
              v-model="contract.customer_id"
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
            ></v-select>
            <v-btn color="primary" @click="navigate(3)">Verder</v-btn>

            <v-btn flat @click="$emit('input')">Annuleren</v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-content step="3">
          <v-form ref="form3" v-model="valid" lazy-validation v-if="step == 3">
            <v-layout wrap>
              <v-flex xs12 sm6 md4>
                Wat is de overeengekomen prijs in € per maand per box
                <v-text-field
                  v-for="(price, i) in contract.units"
                  :key="i"
                  v-model.number="contract.units[i].price"
                  :label="contract.units[i].display_name"
                  placeholder="Overeengekomen prijs"
                  :rules="[v => (v.length !== 0) || 'Dit veld mag niet leeg zijn']"
                  required
                ></v-text-field>
              </v-flex>
              <v-flex xs12 sm6 md4>
                Betalingsmethode
                <v-select
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  :items="['factuur', 'incasso']"
                  v-model="contract.payment_method"
                ></v-select>
              </v-flex>
              <v-flex xs12>
                Standaard notitie / instructie voor alle facturen
                <v-textarea v-model="contract.default_note"></v-textarea>
              </v-flex>
            </v-layout>
            <v-btn color="primary" @click="navigate(4)">Verder</v-btn>
            <v-btn flat @click="$emit('input')">Annuleren</v-btn>
          </v-form>
        </v-stepper-content>
        <v-stepper-content step="4">
          <v-form ref="form4" v-model="valid" lazy-validation>
            <v-layout wrap>
              <v-flex xs12 sm6>
                <h6 class="headline">
                  Kies een
                  <span class="font-weight-black">startdatum</span>
                </h6>
              </v-flex>
              <v-flex xs12 sm6>
                <v-date-picker
                  :rules="[v => !!v || 'Je moet een startdatum kiezen']"
                  required
                  landscape
                  v-model="contract.start_date"
                  label="Ingangsdatum"
                ></v-date-picker>
                <p>Gekozen datum: {{ contract.start_date }}</p>
              </v-flex>
            </v-layout>

            <v-btn color="primary" @click="save">Opslaan</v-btn>
            <v-btn flat @click="$emit('input')">Annuleren</v-btn>
          </v-form>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </v-dialog>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class EditCreateContract extends Vue {
  @Prop()
  dialog: boolean;

  @Prop()
  contract: any;

  private response = "";
  private customers: any = [];
  private units: any = [];
  private loading: boolean = true;
  private valid: boolean = true;
  private step: number = 0;

  // this is trouble because of the getter.
  unitsChanged() {
    const temp = [];
    for (let c in this.contract.units) {
      let u = this.units.free.find((x: any) => x.id === this.contract.units[c]);
      if (u) temp.push(u);
    }
    this.contract.units = temp;
  }

  get formTitle() {
    return this.contract.id ? "Contract aanmaken" : "Contract bewerken";
  }

  get chosenCustomerName() {
    let customer = this.customers.find(
      (x: any) => x.id === this.contract.customer_id
    );
    return customer && customer.name ? customer.name : null;
  }

  get mergeUnits() {
    if (this.contract.id) return this.contract.units.concat(this.units.free);
    return this.units.free;
  }

  async mounted() {
    try {
      // refactor this to an api call that gets the units in {free: [], occupied: []} and customers
      const r = (await axios.get("/api/contracts")).data;
      this.customers = r.customers;
      this.units = r.units;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
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
    if (this.contract.id) {
      axios.post("/api/contracts/" + this.contract.id, this.contract);
    } else {
      axios.post("/api/contracts/create", this.contract);
    }
    this.$emit("input");
  }
}
</script>
