<template>
  <v-card>
    <v-card-title>
      <h3 class="primary--text heading">Betalingen</h3>
    </v-card-title>
    <v-layout row wrap justify-center>
      <v-flex>
        <canvas ref="canvas"></canvas>
      </v-flex>
    </v-layout>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import * as moment from "moment";
const Chart = require("chart.js");

@Component({})
export default class PaymentChart extends Vue {
  @Prop()
  payments: any;

  private statuses: any = [
    { status: "paid", color: "rgba(102, 229, 181, 0.9)", label: "Betaald" },
    { status: "expired", color: "#ffb74d", label: "Verlopen" },
    { status: "canceled", color: "rgb(255, 118, 87)", label: "Geannuleerd" },
    { status: "pending", color: "rgb(46, 132, 224)", label: "Verwerken" }
  ];

  private chartData: any = [];

  get paymentsDate() {
    return {
      labels: this.labels,
      datasets: this.chartData
    };
  }

  get labels() {
    // use Set to make sure we only have unique values
    return [...new Set(this.payments.map((x: any) => moment(x.date).format('DD-MM')))]; 
  }

  async mounted() {
    // get the expired dates with data
    for (let i = 0; i < this.statuses.length; i++) {
      this.chartData.push({
        label: this.statuses[i].label,
        data: this.payments.map((x: any) =>
          x.status === this.statuses[i].status ? x.amount : 0
        ),
        borderColor: this.statuses[i].color,
        backgroundColor: this.statuses[i].color,
        fill: false
      });
    }

    this.$nextTick(() => {
      new Chart((this.$refs.canvas as any).getContext("2d"), {
        type: "line",
        data: this.paymentsDate,
        options: {
          scales: {
            yAxes: [
              {
                ticks: {
                  stepSize: 250
                }
              }
            ]
          },
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