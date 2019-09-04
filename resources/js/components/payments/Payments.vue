<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Betalingen</v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="payments"
        class="elevation-1"
        :loading="loading"
        :pagination.sync="paginationSync"
      >
        <template v-slot:items="props">
          <td v-if="status[props.item.status]">
            <v-chip flat dark class="ml-0 lighten-2 text--darken-1" :class="status[props.item.status].color">
              <v-avatar>
                <v-icon>{{ status[props.item.status].icon }}</v-icon>
              </v-avatar>
              {{ status[props.item.status].text }}
            </v-chip>
          </td>
          <td v-else>
            <v-chip flat class="ml-0">Geen status</v-chip>
          </td>
          <td>{{ props.item.customer }}</td>
          <td>{{ props.item.amount }}</td>
          <td>{{ props.item.mode }}</td>
          <td>{{ props.item.created_at }}</td>
          <td>{{ props.item.updated_at }}</td>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Betalingen laden...</td>
          <td colspan="100%" v-else>Geen betalingen</td>
        </template>
      </v-data-table>
    </div>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Payments extends Vue {
  private response = "";
  private payments: any = [];
  private loading: boolean = true;

  private paginationSync: any = {
    descending: true,
    rowsPerPage: 50, // -1 for All",
    sortBy: "created_at"
  };

  private headers: any = [
    { text: "status", align: "left", value: "status" },
    { text: "klant", align: "left", value: "customer" },
    { text: "bedrag", align: "left", value: "amount" },
    { text: "modus", align: "left", value: "mode" },
    { text: "gemaakt op", value: "created_at" },
    { text: "gewijzigd op", value: "updated_at" }
  ];

  private status: any = {
    paid: {
      text: "Betaald",
      icon: "check_circle",
      color: "green"
    },
    expired: {
      text: "Verlopen",
      icon: "schedule",
      color: "orange"
    },
    canceled: {
      text: "Geannuleerd",
      icon: "error",
      color: "red"
    },
    pending: {
      text: "Verwerken",
      icon: "slow_motion_video",
      color: "info"
    }
  };

  async mounted() {
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      this.payments = (await axios.get("/api/payments")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
