<template>
  <v-card>
    <v-toolbar flat dense color="primary white--text">
      <v-toolbar-title>Todo list:</v-toolbar-title>
    </v-toolbar>
    <v-flex pa-0>
      <v-list dense>
        <template v-for="(todo, i) in todos">
          <v-divider :key="'d' + i"></v-divider>
          <v-list-tile :key="i">
            <v-list-tile-title>{{ todo }}</v-list-tile-title>
          </v-list-tile>
        </template>
      </v-list>
    </v-flex>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class TodoList extends Vue {
  todos: any = [
    "Aanpasbare verhuur tijd van producten",
    "Media library toevoegen",
    "Locaties toevoegen",
    "Tentant scope maken",
    "Mollie mislukte betalingen afhandelen (redirect nog naar succes page)",
    "Mollie first payment save mandate_id to db",
    "Add price formatting to manual payments",
    "If a customer has multiple contracts, all invoices are shown on each contract",
    "Add Settings",
    "Creditfacturen",
    "On Payments, add invoice information card on hover (id, ref_number, start and end date)",
    "Producten in Contract aanpassen",
    "Betalingsherinneringen",
    "Relatienummers op facturen",
    "Consistentie van invoice numbers controleren",
    "Write a test for the MolliePaymentClass"
  ];

  mounted(){
    // store.commit('snackbar', { show: true, message: "aaaa", type: "info"})
  }
}
</script>