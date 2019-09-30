<template>
  <v-flex v-if="!loading">
    <v-layout row wrap>
      <v-flex sm12>
        <h1 class="subheading text-xs-center font-weight-bold">Welkom terug, {{ user.name }}!</h1>
      </v-flex>
    </v-layout>

    <v-layout row wrap>
      <v-flex xs12 sm5 py-3 pr-3>
        <v-card>
          <v-toolbar class="primary white--text">
            <v-toolbar-title>Bezetting</v-toolbar-title>
          </v-toolbar>
          <v-layout row wrap justify-center align-center>
            <v-flex pa-1>
              <canvas ref="canvas"></canvas>
            </v-flex>
            <v-flex pa-1>
              <v-divider></v-divider>
              <v-list dense>
                <v-list-tile>
                  <v-list-tile-content>Aantal verhuurde actieve boxen:</v-list-tile-content>
                  <v-list-tile-action>{{ this.units.occupied_active }}</v-list-tile-action>
                </v-list-tile>

                <v-list-tile>
                  <v-list-tile-content>Aantal verhuurde niet actieve boxen:</v-list-tile-content>

                  <v-list-tile-action>{{ this.units.occupied_not_active }}</v-list-tile-action>
                </v-list-tile>

                <v-list-tile>
                  <v-list-tile-content>Aantal niet verhuurde actieve boxen:</v-list-tile-content>

                  <v-list-tile-action>{{ this.units.free_active }}</v-list-tile-action>
                </v-list-tile>
                <v-list-tile>
                  <v-list-tile-content>Aantal niet verhuurde niet actieve boxen:</v-list-tile-content>

                  <v-list-tile-action>{{ this.units.free_not_active }}</v-list-tile-action>
                </v-list-tile>
              </v-list>
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

      <v-flex xs12 sm3 pl-3 pt-3>
        <v-card>
          <v-toolbar color="primary white--text">
            <v-toolbar-title>Todo list:</v-toolbar-title>
          </v-toolbar>
          <v-flex pa-3>
            <ul>
              <li>Mollie mislukte betalingen afhandelen</li>
              <li>Huisregels in mail attachment</li>
              <li>Algemene voorwaarden link op wordpress en linken in mail & checkout process</li>
              <li>Add Settings</li>
              <li>Change icons for filetype</li>
              <li>Make file work level deeper</li>
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
const Chart = require("chart.js");

@Component({})
export default class Index extends Vue {
  private loading: boolean = true;
  private contracts: any = [];
  private units: any = [];
  private user: any = {};
  private customers: any = [];
  get doughnut() {
    return {
      labels: [
        "Verhuurd en actief",
        "Niet vehuurd en actief",
        "Niet verhuurd en niet actief",
        "Verhuurd en niet actief"
      ],
      datasets: [
        {
          label: "a",
          data: [
            this.units.occupied_active,
            this.units.free_active,
            this.units.free_not_active,
            this.units.occupied_not_active
          ],
          backgroundColor: ["#264B6C", "#CAD7E3", "#F9B698", "#97AFC4"]
        }
      ]
    };
  }

  // get calculateOccupiedPercentage() {
  //   return Math.round(
  //     (this.units.occupied.length / this.units.free.length) * 100
  //   );
  // }

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
    const r = (await axios.get("/api/dashboard")).data;
    this.contracts = r.contracts;
    this.units = r.units;
    this.customers = r.customers;
    this.user = r.user;
    this.loading = false;
    this.$nextTick(() => {
      new Chart((this.$refs.canvas as any).getContext("2d"), {
        type: "doughnut",
        data: this.doughnut,
        options: {
          legend: {
            display: false
          }
        }
      });
    });
  }
}
</script>