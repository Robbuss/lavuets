<template>
  <v-container fluid v-if="!loading" fill-height>
    <v-layout row wrap>
      <v-flex xs12>
        <v-layout row wrap mb-3>
          <v-flex xs12>
            <v-toolbar class="primary" dark>
              <v-toolbar-title>{{ contract.customer.name }}</v-toolbar-title>
            </v-toolbar>
            <v-card class="pa-3">
              <h3>Looptijd</h3>
              {{ contract.start_date }} tot {{ contract.end_date }}
              <h3>Units in dit contract:</h3>
              <p
                v-for="(u, i) in contract.units"
                :key="i"
              >{{ u.name }} voor {{ u.pivot.price }} (standaard prijs: {{ u.price }})</p>
            </v-card>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <v-flex xs12>
            <v-toolbar class="primary" dark>
              <v-toolbar-title>Facturen bij dit contract</v-toolbar-title>
            </v-toolbar>
            <v-card>
              <v-list>
                <v-list-tile v-for="invoice in contract.invoices" :key="invoice.id + 'i'">
                  <v-list-tile-content>{{ invoice }}</v-list-tile-content>
                </v-list-tile>
                <v-list-tile v-if="contract.invoices.length === 0">
                  Geen facturen gevonden. 
                    <v-btn class="primary" @click="generate">Genereer facturen voor dit contract</v-btn>
                </v-list-tile>                
              </v-list>
            </v-card>
          </v-flex>
        </v-layout>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class SingleContract extends Vue {
  private response = "";
  private loading: boolean = true;
  private contract: any = null;

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async generate(){
    this.loading = true;
    await axios.post("/api/invoices/generate", { contract_id: this.contract.id });
    await this.getData();
    this.loading = false;
  }

  async getData() {
    try {
      this.contract = (await axios.get(
        "/api/contracts/" + this.$route.params.id
      )).data;
    } catch (e) {
      this.response = e.message;
    }
  }
}
</script>
