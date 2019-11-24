<template>
  <v-card>
    <v-card-title>
      <h3 class="primary--text heading">Bezetting</h3>
    </v-card-title>
    <v-row wrap justify-center>
      <v-col>
        <canvas ref="canvas"></canvas>
      </v-col>
    </v-row>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
const Chart = require("chart.js");

@Component({})
export default class OccupationRate extends Vue {
  @Prop()
  units: any;
  
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
          backgroundColor: ["#75E8BC", "#2E84E0", "#FF7657", "#FFB74D"]
        }
      ]
    };
  }
  mounted() {
    this.$nextTick(() => {
      new Chart((this.$refs.canvas as any).getContext("2d"), {
        type: "doughnut",
        data: this.doughnut,
        options: {
          legend: {
            display: true,
            position: "bottom",
            labels: {
              usePointStyle: true
            }
          }
        }
      });
    });
  }
}
</script>