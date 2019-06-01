<template>
  <v-card>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>
    <v-layout row wrap>
      <v-flex sm12 class="pa-2">
        <v-text-field label="Box / product naam" v-model="editedItem.name"/>
      </v-flex>
      <v-flex sm12 md6 class="pa-2">
        <v-text-field label="Grootte in m3" v-model="editedItem.size"/>
      </v-flex>
      <v-flex sm12 md6 class="pa-2">
        <v-text-field label="Prijs in euro's" v-model="editedItem.price"/>
      </v-flex>
    </v-layout>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn dark color="blue darken-1" @click="cancel">Cancel</v-btn>
      <v-btn
        :dark="!working"
        color="blue darken-1"
        :loading="working"
        :disabled="working"
        @click="save"
      >Save</v-btn>
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
  private editedItem: any = {
    id: null,
    name: "",
    size: "",
    price: 0
  };
  private defaultItem: any = {
    id: null,
    name: "",
    size: "",
    price: 0
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
