<template>
  <v-flex v-if="!loading">
    <v-layout row wrap>
      <v-flex sm12>
        <h1
          class="subheading primary--text text-xs-center font-weight-bold"
        >Welkom terug {{ user.name }}, you magnificent bastard!</h1>
      </v-flex>
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
@Component({
  components: {
    PaymentChart,
    TodoList,
    Revenue,
    OccupationRate,
    UnpaidInvoices,
  }
})
export default class Index extends Vue {
  private loading: boolean = true;
  private contracts: any = [];
  private units: any = [];
  private payments: any = [];
  private user: any = {};
  private customers: any = [];

  async mounted() {
    const r = (await axios.get("/api/dashboard")).data;
    this.contracts = r.contracts;
    this.units = r.units;
    this.customers = r.customers;
    this.payments = r.payments;
    this.user = r.user;
    this.loading = false;
  }
}
</script>