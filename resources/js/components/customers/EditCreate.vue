<template>
  <v-card>
    <v-toolbar flat class="primary" dark>
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>

    <v-card-text>
      <v-container grid-list-md>
        <v-layout wrap>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.name" label="Naam"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.email" label="E-mail"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.company_name" label="Bedrijfsnaam"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.city" label="Stad"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.street_addr" label="Straat"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.street_number" label="Nummer"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.postal_code" label="Postcode"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.kvk" label="KVK"></v-text-field>
          </v-flex>
          <v-flex xs12 sm6 md4>
            <v-text-field v-model="editedItem.btw" label="BTW"></v-text-field>
          </v-flex>
        </v-layout>
      </v-container>
    </v-card-text>

    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn dark color="blue darken-1" @click="cancel">Cancel</v-btn>
      <v-btn :dark="!working" color="blue darken-1" :loading="working" :disabled="working" @click="save">Save</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class EditCustomer extends Vue {
  @Prop()
  customer: any;

  @Prop()
  creating: boolean;

  private working: boolean = false;
  private editedItem: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    city: "",
    street_addr: "",
    street_number: 0,
    postal_code: "",
    btw: "",
    kvk: ""
  };
  private defaultItem: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    city: "",
    street_addr: "",
    street_number: 0,
    postal_code: "",
    btw: "",
    kvk: ""
  };
  private response = "";

  get formTitle() {
    return this.creating
      ? "Klant aanmaken"
      : "De gegevens van " + this.customer.name + " bewerken";
  }

  mounted() {
    if (this.customer) {
      this.editedItem = Object.assign({}, this.customer);
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
      await axios.post("/api/customers/" + this.editedItem.id, this.editedItem);
    } else {
      await axios.post("/api/customers/create", this.editedItem);
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
