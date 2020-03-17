<template>
  <v-card v-if="false">
    <v-toolbar flat dense color="primary white--text">
      <v-toolbar-title>Todo list:</v-toolbar-title>
    </v-toolbar>
    <v-col>
      <v-list dense>
        <template v-for="(todo, i) in todos">
          <v-divider :key="'d' + i"></v-divider>
          <v-list-item :key="i">
            <v-list-item-title>{{ todo }}</v-list-item-title>
          </v-list-item>
        </template>
      </v-list>
    </v-col>
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
    "Stop using float for monetary values",
    "Keuze toevoegen bij betalen: Ik wil eerst een offerte",
    "Keuze voor begane grond of eerste verdieping => Generiek beschrijvingsveld toevoegen aan de boxen. Boxen die beneden zijn eerst tonen. 'Deze unit is bereikbaar met de lift / deze unit is bereikbaar via de begane grond'",
    "Boxen die binnenkort vrij komen laten zien in het boekingsformulier",
    "Keuze prijs per box . huren per week. Maand, 2 maanden enz.  (Even overleg hoe dit het beste te kunnen weergeven) zie plaatje",
    "In de instellingen het contract en de facturen inhoud aanpasbaar maken",
    "Maken dat elke customer een aparte mollie API gebruikt ",
    "Eventueel later: Inlog mogelijkheid voor de klant; downloaden van facturen etc",
    "Relatienummers op facturen",
    "Write a test for the MolliePaymentClass"
  ];
  done = [
    "Bij keuze locatie. Aantal boxen weghalen",
  ]
}
</script>