<template>
  <v-col v-if="!loading">
    <v-container class="container--fluid">
      <v-row wrap justify="space-between" align="center">
        <v-col class="py-0">
          <v-btn outlined small class="primary--text" @click="manualTenant">Klant toevoegen</v-btn>
        </v-col>
        <v-col class="py-0">
          <h1 class="subtitle-1 primary--text text-center font-weight-bold">{{ greeting }}</h1>
        </v-col>
        <v-col></v-col>
      </v-row>
      <v-row wrap>
        <v-col cols="12" sm="6" md="5">
          <PaymentChart :payments="payments" />
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <OccupationRate :units="units" />
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <Revenue :contracts="contracts" :units="units" />
        </v-col>

        <v-col cols="12">
          <UnpaidInvoices />
        </v-col>

        <v-col cols="12" sm="6">
          <TodoList />
        </v-col>
      </v-row>
    </v-container>
    <ManualTenant v-model="dialog" />
  </v-col>
</template>

<style scoped>
.noscroll {
  overflow: hidden;
}
</style>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import PaymentChart from "./PaymentChart.vue";
import TodoList from "./TodoList.vue";
import UnpaidInvoices from "./UnpaidInvoices.vue";
import Revenue from "./RevenueTiles.vue";
import OccupationRate from "./OccupationRate.vue";
import ManualTenant from "js/components/tenants/ManualTenant.vue";

@Component({
  components: {
    PaymentChart,
    TodoList,
    Revenue,
    OccupationRate,
    UnpaidInvoices,
    ManualTenant
  }
})
export default class Index extends Vue {
  private loading: boolean = true;
  private contracts: any = [];
  private units: any = [];
  private payments: any = [];
  private user: any = {};
  private tenants: any = [];
  private dialog: boolean = false;
  private greetings: any = [
    ":name:, you magnificent bastard!",
    "Welcome back, old chap",
    "So good to see you again, :name:",
    "Good dawning to thee, friend",
    "Well be with you, :name:"
  ];

  async mounted() {
    const r = (await axios.get("/api/dashboard")).data;
    this.contracts = r.contracts;
    this.units = r.units;
    this.tenants = r.tenants;
    this.payments = r.payments;
    this.user = r.user;
    this.loading = false;
  }

  manualTenant() {
    this.dialog = !this.dialog;
  }

  get greeting() {
    return this.greetings[
      Math.floor(Math.random() * Math.floor(this.greetings.length))
    ].replace(":name:", this.user.name);
  }
}
</script>