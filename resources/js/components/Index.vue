<template>
  <v-flex>
    <v-layout row wrap>
      <v-flex sm12>Logged in homepage</v-flex>
    </v-layout>

    <v-layout row wrap v-if="!loading">
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
            <v-toolbar-title>Omzet</v-toolbar-title>
          </v-toolbar>
          <v-layout row wrap align-center>
            <v-flex pa-3 text-xs-center>
              Maandelijkse omzet
              <p>
                Inkomsten van verhuurde boxen: €{{ this.realizedProfit }}
                <br />
                Mogelijke (extra) inkomsten: €{{ this.potentialExtraProfit }}
                <br />
                Mogelijke totale omzet: €{{ this.realizedProfit + this.potentialExtraProfit }}
              </p>
            </v-flex>
          </v-layout>
        </v-card>
      </v-flex>

      <v-flex xs12 sm4 pa-3>
        <v-card>
          <v-toolbar color="primary white--text">
            <v-toolbar-title>Todo list:</v-toolbar-title>
          </v-toolbar>
          <v-flex pa-3>
            <ul>
              <li>Bedrijfsnaam toevoegen aan contract</li>
              <li>Zorgen dat Facturen voor een periode niet dubbel gegenereerd worden</li>
              <li>Contract beeindigen of opzeggen check (deleted at veranderen in opgezegd boolean)</li>
              <li>Checken of de prijzen die in de maandelijkse omzet staan kloppen (overeengekomen prijs vs standaard prijs)</li>
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

  async mounted() {
    const r = (await axios.get("/api/contracts")).data;
    const c = (await axios.get("/api/customers")).data;
    this.contracts = r.contracts;
    this.units = r.units;
    this.customers = c;
    this.loading = false;
  }
}
</script>
