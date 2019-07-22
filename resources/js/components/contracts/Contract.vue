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
              <v-subheader>Contract informatie</v-subheader>
              <v-list two-line>
                <v-list-tile>
                  <v-list-tile-content>
                    <v-list-tile-title>Van {{ contract.start_date }} tot {{ contract.end_date }}</v-list-tile-title>
                    <v-list-tile-sub-title>Looptijd van het contract</v-list-tile-sub-title>
                  </v-list-tile-content>
                </v-list-tile>
                <v-subheader>Producten in dit contract</v-subheader>
                <v-list-tile v-for="(u, i) in contract.units" :key="i">
                  <v-list-tile-content>
                    <v-list-tile-title>{{ u.name }} voor {{ u.pivot.price }}</v-list-tile-title>
                    <v-list-tile-sub-title>(standaard prijs: {{ u.price }})</v-list-tile-sub-title>
                  </v-list-tile-content>
                </v-list-tile>
              </v-list> 
            </v-card>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <invoices :units="contract.allUnits" :contract="contract" :customer_id="contract.customer_id" @generate="generate"></invoices>
        </v-layout>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import Invoices from "../invoices/Invoices.vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({
  components: {
    invoices: Invoices
  }
})
export default class SingleContract extends Vue {
  private response = "";
  private loading: boolean = true;
  private contract: any = null;

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async generate() {
    this.loading = true;
    await axios.post("/api/invoices/generate", {
      contract_id: this.contract.id
    });
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
