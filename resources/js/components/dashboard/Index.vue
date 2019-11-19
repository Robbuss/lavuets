<template>
  <v-flex v-if="!loading">
    <v-layout row wrap justify-space-between align-center>
      <v-flex px-3>
        <v-btn outline small class="primary primary--text" @click="manualTenant">Klant toevoegen</v-btn>
      </v-flex>
      <v-flex>
        <h1
          class="subheading primary--text font-weight-bold"
        >{{ greeting }}</h1>
      </v-flex>
      <v-flex></v-flex>
    </v-layout>
    <v-container fluid grid-list-xl>
      <v-layout row wrap>
        <v-flex d-flex xs12 sm6 md5>
          <PaymentChart :payments="payments" />
        </v-flex>
        <v-flex d-flex xs12 sm6 md4>
          <OccupationRate :units="units" />
        </v-flex>
        <v-flex d-flex xs12 sm6 md3>
          <v-layout row wrap>
            <v-flex d-flex>
              <Revenue :contracts="contracts" :units="units" />
            </v-flex>
          </v-layout>
        </v-flex>

        <v-flex d-flex xs12>
          <UnpaidInvoices />
        </v-flex>

        <v-flex d-flex xs12 sm6>
          <TodoList />
        </v-flex>
      </v-layout>
    </v-container>
    <ManualTenant v-model="dialog" />
  </v-flex>
</template>

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
    ':name:, you magnificent bastard!',
    'Welcome back, old chap',
    'So good to see you again, :name:',
    'Good dawning to thee, friend',
    'Well be with you, :name:'
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
    this.dialog = true;
  }

  get greeting (){
    return this.greetings[Math.floor(Math.random() * Math.floor(this.greetings.length))].replace(':name:', this.user.name);
  }
}
</script>