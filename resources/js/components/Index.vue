<template>
  <v-flex v-if="!loading">
    <v-layout row wrap>
      <v-flex sm12>
        <h1 class="subheading text-xs-center font-weight-bold">Welkom terug, {{ user.name }}!</h1>
      </v-flex>
    </v-layout>

    <v-layout row wrap>
      <v-flex xs12 sm4 py-3 pr-3>
        <v-card>
          <v-toolbar class="primary white--text">
            <v-toolbar-title>Bezetting</v-toolbar-title>
          </v-toolbar>
          <v-layout row wrap justify-center align-center>
            <v-flex pa-3 shrink>
              <v-progress-circular
                :size="128"
                :width="24"
                color="primary"
                :value="calculateOccupiedPercentage"
              ></v-progress-circular>
              <p>
                Aantal verhuurde boxen: {{ this.units.occupied.length }}
                <br />
                Aantal vrije boxen: {{ this.units.free.length }}
              </p>
            </v-flex>
          </v-layout>
        </v-card>
      </v-flex>

      <v-flex xs12 sm4 pa-3>
        <v-card>
          <v-toolbar class="primary white--text">
            <v-toolbar-title>Maandelijkse omzet</v-toolbar-title>
          </v-toolbar>
          <v-layout row wrap align-center>
            <v-flex pa-3 text-xs-center>
              <p>
                Inkomsten van verhuurde boxen: €{{ this.realizedProfit }} ({{ Math.round((this.realizedProfit / this.totalProfit) * 100)}}%)
                <br />
                Mogelijke (extra) inkomsten: €{{ this.potentialExtraProfit }}
                <br />
                Mogelijke totale omzet: €{{ this.totalProfit }}
              </p>
            </v-flex>
          </v-layout>
        </v-card>
      </v-flex>

      <v-flex xs12 sm4 pl-3 pt-3>
        <v-card>
          <v-toolbar color="primary white--text">
            <v-toolbar-title>Todo list:</v-toolbar-title>
          </v-toolbar>
          <v-flex pa-3>
            <ul>
              <li>Mollie mislukte betalingen afhandelen</li>
              <li>Check customer valid email form</li>
              <li>Box met grootte 0m3 kan niet aangepast worden</li>
            </ul>
          </v-flex>
        </v-card>
      </v-flex>
    </v-layout>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Index extends Vue {
  private loading: boolean = true;
  private contracts: any = [];
  private units: any = [];
  private user: any = {};
  private customers: any = [];

  get calculateOccupiedPercentage() {
    return Math.round(
      (this.units.occupied.length / this.units.free.length) * 100
    );
  }

  get realizedProfit() {
    let p = 0;
    this.contracts.map((x: any) => x.units.map((x: any) => (p += x.price)));
    return p;
  }

  get potentialExtraProfit() {
    let p = 0;
    this.units.free.map((x: any) => (p += x.price));
    return p;
  }

  get totalProfit() {
    return this.realizedProfit + this.potentialExtraProfit;
  }

  async mounted() {
    const r = (await axios.get("/api/contracts")).data;
    const c = (await axios.get("/api/customers")).data;
    const u = (await axios.get("/api/user/profile")).data;
    this.contracts = r.contracts;
    this.units = r.units;
    this.customers = c;
    this.user = u;
    this.loading = false;
  }
}
</script>
