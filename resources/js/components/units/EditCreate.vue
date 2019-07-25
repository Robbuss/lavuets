<template>
  <v-card>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>
    <v-form v-model="valid" lazy-validation ref="form">
      <v-layout row wrap class="pa-3">
        <v-flex sm12>
          <v-text-field
            label="Product naam"
            placeholder="Komt op de factuur"
            v-model="editedItem.name"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12 md6>
          <v-text-field
            type="number"
            label="Grootte in m3"
            placeholder="Gebruik alleen getallen"
            v-model="editedItem.size"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
        </v-flex>
        <v-flex sm12 md6>
          <v-text-field
            type="number"
            label="Prijs in euro's"
            placeholder="Gebruik alleen getallen"
            v-model="editedItem.price"
            required
            :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
          />
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

@Component({})
export default class Editunit extends Vue {
  @Prop()
  unit: any;

  @Prop()
  creating: boolean;

  private working: boolean = false;
  private valid: boolean = true;
  private editedItem: any = {
    id: null,
    name: "",
    size: "",
    price: null
  };
  private defaultItem: any = {
    id: null,
    name: "",
    size: "",
    price: null
  };
  private response = "";

  get formTitle() {
    return this.creating
      ? "Klant aanmaken"
      : "De gegevens van " + this.unit.name + " bewerken";
  }

  mounted() {
    if (this.unit) {
      this.editedItem = Object.assign({}, this.unit);
    }
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
    } else {
      await axios.post("/api/units/create", this.editedItem);
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
