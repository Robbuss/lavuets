<template>
  <v-dialog :value="dialog" max-width="80%" persistent>
    <v-card class= "pa-4" v-if="!loading">
      <v-form ref="form" v-model="valid" lazy-validation>
        <v-row wrap>
          <v-col cols="12">
            <h6 class="caption">
              Welke
              <span class="font-weight-black">producten</span>
              komen in dit contract?
            </h6>
            <v-select :items="mergedUnits" v-model="editedItem.units" multiple chips>
              <template v-slot:item="data">
                <v-col
                  class="align-center grey--text"
                  style="display:flex"
                  :class="{'primary--text' : editedItem.units.indexOf(data.item) > -1}"
                >
                  <v-icon
                    :class="{'primary--text' : editedItem.units.indexOf(data.item) > -1}"
                    v-if="editedItem.units.indexOf(data.item) > -1"
                  >check_filled </v-icon>
                  <v-icon v-else>check_filled _outline_blank</v-icon>
                  <span class= "pl-4">{{ data.item.display_name }}</span>
                  <span v-if="!data.item.active">- Non actief!</span>
                </v-col>
              </template>
              <template v-slot:selection="data">
                <v-chip close @input="remove(data.item)">
                  {{ data.item.display_name }}
                  <span v-if="!data.item.active">- Non actief!</span>
                </v-chip>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12">
            <v-checkbox  v-model="defaultPrices" label="Standaard prijzen gebruiken"></v-checkbox >
          </v-col>
          <v-expand-transition>
            <v-col cols="12" v-if="editedItem.units.length > 0 && !defaultPrices">
              Wat is de overeengekomen prijs in € per maand per filled (deze kan afwijken van de standaard prijs)
              <v-text-field
                v-for="(c, i) in editedItem.units"
                :key="i"
                v-model.number="c.price"
                type="number"
                :label="c.display_name"
                placeholder="Overeengekomen prijs"
                :rules="[v => (v.length !== 0) || 'Dit veld mag niet leeg zijn']"
                required
              ></v-text-field>
            </v-col>
          </v-expand-transition>
        </v-row>
        <v-row wrap>
          <v-col cols="12" sm="12" md="4">
            <h6 class="caption">
              Kies een
              <span class="font-weight-black">betalingsmethode</span>?
            </h6>
            <v-autocomplete
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              :items="['factuur', 'incasso']"
              v-model="editedItem.payment_method"
            ></v-autocomplete>
          </v-col>
        </v-row>
        <v-row wrap>
          <v-col cols="12" sm="12" md="4" v-if="!editedItem.id">
            <h6 class="caption">
              Kies een
              <span class="font-weight-black">huurder</span>
            </h6>
            <v-autocomplete
              :items="tenants"
              item-value="id"
              item-text="name"
              v-model="editedItem.tenant_id"
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              color="blue-grey lighten-2"
            ></v-autocomplete>
          </v-col>
        </v-row>

        <v-row wrap>
          <v-col cols="12">
            <h6 class="caption">
              Moet de klant automatisch facturen
              <span class="font-weight-black">per mail</span> ontvangen
            </h6>
            <span></span>
            <v-checkbox  v-model="editedItem.auto_invoice" label="Automagische facturatie" />
          </v-col>
        </v-row>

        <v-row wrap>
          <v-col cols="12">
            <h6 class="caption">
              Kies een
              <span class="font-weight-black">startdatum</span>
              van het contract
            </h6>
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
              :return-value.sync="editedItem.start_date"
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
              <v-date-picker locale="nl" v-model="editedItem.start_date" no-title scrollable>
                <v-spacer></v-spacer>
                <v-btn text color="primary" @click="$refs.menu.save(editedItem.start_date)">OK</v-btn>
              </v-date-picker>
            </v-menu>
          </v-col>
        </v-row>

        <v-btn color="primary" @click="save">Opslaan</v-btn>
        <v-btn text @click="$emit('input')">Annuleren</v-btn>
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
  private editedItem: any = {
    tenant_id: null,
    auto_invoice: true,
    method: "addMonth",
    payment_method: "incasso",
    period: 1,
    start_date: null,
    units: []
  };

  get formatDate() {
    return this.editedItem.start_date
      ? moment(this.editedItem.start_date).format("DD-MM-YYYY")
      : "";
  }

  get formTitle() {
    return this.editedItem.id ? "Contract aanmaken" : "Contract bewerken";
  }

  remove(data: any) {
    this.editedItem.units.splice(this.editedItem.units.indexOf(data), 1);
    this.mergeUnits();
  }

  mergeUnits() {
    this.mergedUnits = this.units.free;
    if (this.editedItem.id) {
      this.mergedUnits = this.editedItem.units.concat(this.units.free);
    }
  }

  async mounted() {
    // use an exisiting contract when editing
    if (this.contract && this.contract.id) this.editedItem = this.contract; //Object.assign(this.editedItem, this.contract);
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
    if (this.editedItem.id) {
      axios.post("/api/contracts/" + this.editedItem.id, this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Contract aangepast!"
      });
    } else {
      axios.post("/api/contracts/create", this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Contract aangemaakt!"
      });
    }
    this.$emit("input");
  }
}
</script>
