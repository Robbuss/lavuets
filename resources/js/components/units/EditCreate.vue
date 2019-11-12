<template>
  <v-card>
    <v-toolbar flat color="primary" dark>
      <BackButton />
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>
    <v-form v-model="valid" lazy-validation ref="form">
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
            :rules="[v => (v.length !== 0) || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12>
          <v-text-field
            type="number"
            box
            prefix="€"
            label="Prijs in euro's"
            placeholder="Gebruik alleen getallen"
            v-model="editedItem.price"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12>
          <v-text-field
            type="number"
            box
            label="BTW percentage"
            suffix="%"
            placeholder="Gebruik alleen een getal"
            v-model="editedItem.vat_percentage"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12>
          <v-select box :items="activeBox" v-model="editedItem.active" label="Box is verhuurbaar" />
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
  private valid: boolean = true;
  private locations: any = [];
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
    price: null,
    vat_percentage: 21
  };
  private defaultItem: any = {
    id: null,
    location_id: null,
    name: "",
    size: "",
    active: null,
    price: null,
    vat_percentage: 21
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
