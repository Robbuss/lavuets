<template>
  <v-col cols="12" v-if="!loading">
    <v-alert border="top" prominent outlined color="secondary" icon="mdi-school" dismissible class="grey--text">
      De gevens die je hier invult worden gebruikt om op jouw facturen te zetten.
      <br />Ook wordt deze informatie gebruik voor de facturering van deze software.
    </v-alert>
    <v-card flat>
      <v-form>
        <v-row wrap>
          <v-col cols="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.company_name"
              label="Naam"
              prepend-icon="store"
              placeholder="ACME inc"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              prepend-icon="phone"
              v-model="customer.phone"
              label="Telefoonnummer"
              placeholder="030 123456"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="10">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.street_addr"
              prepend-icon="place"
              placeholder="Straatnaam"
              label="Straat"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="2">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.street_number"
              label="Huisnummer"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.city"
              label="Stad"
              prepend-icon="place"
              placeholder="Amsterdam"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.postal_code"
              prepend-icon="place"
              label="Postcode"
              placeholder="1234AB"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              outlined
              prepend-icon="store"
              v-model="customer.kvk"
              label="KVK"
              placeholder="KVK nummer"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field outlined v-model="customer.btw" label="BTW" placeholder="BTW nummer"></v-text-field>
          </v-col>
        </v-row>
        <v-btn
          @click="update"
          color="primary"
          :disabled="loading"
          :loading="loading"
          depressed
        >Opslaan</v-btn>
      </v-form>
    </v-card>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class Customer extends Vue {
  private response = "";
  private loading: boolean = true;
  private customer: any = {
    company_name: "",
    city: "",
    street_addr: "",
    street_number: "",
    postal_code: "",
    btw: "",
    kvk: ""
  };

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async getData() {
     this.customer = (await axios.get("/api/customers/read")).data;
    
  }

  async update() {
    try {
      await axios.post("/api/customers/update", this.customer);
      store.commit("snackbar", {
        type: "success",
        message: "Bedrijfsinformatie aangepast."
      });
    } catch (e) {
      this.response = e.message;
    }
  }
}
</script>
