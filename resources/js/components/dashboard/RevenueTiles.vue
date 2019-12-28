<template>
  <v-card class="fill-height">
    <v-card-title>
      <h3 class="primary--text heading inline" v-if="monthly">Maandelijkse omzet</h3>
      <h3 class="primary--text heading inline" v-else>Jaarlijkse omzet</h3>
    </v-card-title>
    <v-row wrap class="px-4">
      <v-col class="px-4">
        <v-card
          flat
          class="pa-2 round green-box"
          :class="{'white--text' : (this.percentageProfit > 5)}"
          :style="{width: this.percentageProfit + '%'}"
        >
          <h3 class="heading">€{{ formatMoney(this.realizedProfit) }}</h3>
        </v-card>
        <span
          class="font-italic green-text"
          style="font-size: 0.8em"
        >Inkomsten van verhuurde box en ({{ this.percentageProfit }}%)</span>
        <v-card
          flat
          class="pa-2 mr-6 red-box round"
          :class="{'white--text' : (this.percentageProfit > 5)}"
          :style="{width: Math.round((this.potentialExtraProfit / this.totalProfit) * 100) + '%'}"
        >
          <h3 class="heading">€{{ formatMoney(this.potentialExtraProfit) }}</h3>
        </v-card>
        <span class="font-italic red-text" style="font-size: 0.8em">Mogelijke (extra) inkomsten</span>
        <v-card flat class="pa-2 blue-box white--text round">
          <h3 class="heading">€{{ formatMoney(this.totalProfit) }}</h3>
        </v-card>
        <span class="font-italic blue-text" style="font-size: 0.8em">Mogelijke totale omzet</span>
      </v-col>
    </v-row>
    <v-row justify="center" align="end">
      <v-col class="shrink">
        <v-switch class="pa-0 ma-0" v-model="monthly">
          <span
            class="grey--text text-lighten-1"
            style="font-size:0.7em; line-height: 2em;"
            slot="prepend"
          >jaarlijks</span>
          <span
            class="grey--text text-lighten-1"
            style="font-size:0.7em; line-height: 2em;"
            slot="append"
          >maandelijks</span>
        </v-switch>
      </v-col>
    </v-row>
  </v-card>
</template>

<style scoped>
.red-box {
  color: rgb(255, 255, 255);
  background: linear-gradient(to right, rgb(255, 118, 87), rgb(227, 74, 74));
}
.green-box {
  color: rgb(255, 255, 255);
  background: linear-gradient(to right, rgb(102, 229, 181), rgb(41, 224, 105));
}
.blue-box {
  color: rgb(255, 255, 255);
  background: linear-gradient(to right, rgb(52, 181, 229), rgb(46, 132, 224));
}
.red-text {
  color: rgb(255, 118, 87);
}
.blue-text {
  color: rgb(52, 181, 229);
}
.green-text {
  color: rgb(102, 229, 181);
}
</style>
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class RevenueTiles extends Vue {
  @Prop()
  contracts: any;

  @Prop()
  units: any;

  private monthly: boolean = true;

  formatMoney(number: any) {
    return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
  }

  get realizedProfit() {
    let p = 0;
    this.contracts.map((x: any) =>
      x.units.map((x: any) => (p += Number(x.price)))
    );
    return this.monthly ? p : p * 12;
  }

  get potentialExtraProfit() {
    let p = 0;
    this.units.free.map((x: any) => (p += Number(x.price)));
    return this.monthly ? p : p * 12;
  }

  get percentageProfit() {
    if(this.realizedProfit === 0)
      return 100;
    return Math.round((this.realizedProfit / this.totalProfit) * 100);
  }

  get totalProfit() {
    return this.realizedProfit + this.potentialExtraProfit;
  }
}
</script>