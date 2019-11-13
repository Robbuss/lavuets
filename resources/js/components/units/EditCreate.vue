<template>
  <v-card>
    <v-toolbar flat color="primary" dark>
      <BackButton />
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>
    <v-form lazy-validation ref="form">
      <v-layout row wrap class="pa-3">
        <v-flex sm12>
          <v-select
            label="Kies een locatie"
            placeholder="Waar de unit zich bevindt"
            v-model="editedItem.location_id"
            required
            box
            item-text="facility_name"
            item-value="id"
            :items="locations"
            :rules="[v => !!v || 'Je moet een locatie kiezen']"
          />
        </v-flex>
        <v-flex sm12>
          <v-text-field
            label="Product naam"
            box
            placeholder="Komt op de factuur"
            v-model="editedItem.name"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12>
          <v-text-field
            type="number"
            box
            label="Grootte in m3"
            suffix="m3"
            placeholder="Gebruik alleen getallen"
            v-model="editedItem.size"
            required
            :rules="[
              v => (v.length !== 0) || 'Dit veld mag niet leeg zijn',
              v => v >= 0 || 'Waarde moet positief zijn'
            ]"
          />
        </v-flex>
        <v-flex sm12>
          <v-text-field
            type="number"
            box
            min="0"
            prefix="€"
            label="Prijs in euro's"
            placeholder="Gebruik alleen getallen"
            v-model="editedItem.price"
            required
            :rules="[
              v => (v.length !== 0) || 'Dit veld mag niet leeg zijn',
              v => v >= 0 || 'Waarde moet positief zijn'
            ]"
          />
        </v-flex>
        <v-flex sm12 md6>
          <v-text-field
            type="number"
            box
            label="BTW percentage"
            suffix="%"
            placeholder="Gebruik alleen een getal"
            v-model="editedItem.vat_percentage"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
            :class="{'pr-3' : $vuetify.breakpoint.mdAndUp}"
          />
        </v-flex>
        <v-flex sm12 md6>
          <v-select
            box
            label="BTW rekenen voor"
            placeholder="Kies wat voor klanten btw betalen"
            v-model="editedItem.should_tax"
            required
            item-text="key"
            item-value="value"
            :items="shouldTaxOptions"
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12 md6>
          <v-select
            box
            :class="{'pr-3' : $vuetify.breakpoint.mdAndUp}"
            :items="activeBox"
            v-model="editedItem.active"
            label="Product is verhuurbaar"
          />
        </v-flex>
        <v-flex sm12 md6>
          <v-select
            box
            :items="activeBox"
            v-model="editedItem.show_frontend"
            label="Product tonen in boekingsformulier"
          />
        </v-flex>
        <v-flex xs12>
          <v-alert
            :value="(editedItem.show_frontend && !editedItem.active) || (!editedItem.show_frontend && editedItem.active)"
            type="info"
          >Wanneer het product niet verhuurbaar is óf wanneer product tonen in het boekingsformulier uit staat, zal het niet worden getoond.</v-alert>
        </v-flex>
      </v-layout>
    </v-form>

    <v-card-actions>
      <v-btn
        :dark="!working"
        color="primary darken-1"
        :loading="working"
        :disabled="working"
        @click="save"
      >Opslaan</v-btn>
      <v-btn flat color="primary darken-1" @click="cancel">Annuleren</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class Editunit extends Vue {
  @Prop()
  unit: any;

  @Prop()
  creating: boolean;

  private loading: boolean = true;
  private working: boolean = false;
  private locations: any = [];
  private shouldTaxOptions: any = [
    { value: "companies", key: "Alleen bedrijven" },
    { value: "all", key: "Iedereen" },
    { value: "none", key: "Niemand" }
  ];
  private activeBox: any = [
    {
      text: "Ja",
      value: true
    },
    {
      text: "Nee",
      value: false
    }
  ];
  private editedItem: any = {
    id: null,
    name: "",
    location_id: null,
    size: "",
    active: true,
    price: 0,
    vat_percentage: 0.21,
    should_tax: [],
    show_frontend: true
  };
  private defaultItem: any = {
    id: null,
    location_id: null,
    name: "",
    size: "",
    active: null,
    price: 0,
    vat_percentage: 0.21,
    should_tax: [],
    show_frontend: true
  };
  private response = "";

  get formTitle() {
    return this.creating && !this.unit
      ? "Product aanmaken"
      : "De gegevens van " + this.unit.name + " bewerken";
  }

  async mounted() {
    if (this.unit) {
      this.editedItem = this.unit;
    }
    this.locations = (await axios.get("/api/locations")).data;
    this.loading = false;
  }

  editItem(item: any) {
    this.editedItem = Object.assign({}, item);
  }

  cancel() {
    this.$emit("canceled");
  }

  async save() {
    if (!(<any>this.$refs.form).validate()) {
      return;
    }
    this.working = true;
    if (!this.creating) {
      await axios.post("/api/units/" + this.editedItem.id, this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Product aangepast!"
      });
    } else {
      await axios.post("/api/units/create", this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Product aangemaakt!"
      });
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
