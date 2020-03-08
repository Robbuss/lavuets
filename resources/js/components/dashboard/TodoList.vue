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
    "Bij keuze locatie. Aantal boxen weghalen",
    "Keuze voor begane grond of eerste verdieping",
    "Boxen die binnenkort vrij komen laten zien in het boekingsformulier",
    "Foto's per box in het formulier -> minimaal 3 (hier kan ook een plaatje van de plattegrond waar de box zich bevind)",
    "In formulier keuze m2 of m3 tonen omdat we boven lagere boxen krijgen…",
    "Boxen een Lengte x Breedte x Hoogte geven aan de voorkant m2 en m3 automatisch tonen",
    "Met meer informatie in het boxen veld worden de vakjes per box groter…. 2 boxen standaard tonen per soort? En knop ‘meer tonen’ ?  of scroll mogelijkheid?",
    "Keuze prijs per box . huren per week. Maand, 2 maanden enz.  (Even overleg hoe dit het beste te kunnen weergeven) zie plaatje",
    "Maten box tonen bij knop extra info over deze box? LXBXH ",
    "In de instellingen het contract en de facturen inhoud aanpasbaar maken",
    "Maken dat elke customer een aparte mollie API gebruikt ",
    "Eventueel later: Inlog mogelijkheid voor de klant; downloaden van facturen etc",
    "Aanpasbare verhuur tijd van producten met eventueel een andere prijs per maand",
    "Relatienummers op facturen",
    "Write a test for the MolliePaymentClass"
  ];
}
</script>