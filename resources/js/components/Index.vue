<template>
  <v-flex>
    <v-flex sm12>Logged in homepage</v-flex>
    <v-flex xs12 sm6>
      <v-card>
        <v-toolbar color="primary white--text">
          <v-toolbar-title>Todo list:</v-toolbar-title>
        </v-toolbar>
        <v-flex pa-3>
          <ul>
            <li>Zorgen dat Facturen voor een periode niet dubbel gegenereerd worden</li>
            <li>Toevoegen van zoekbalk in alle facturen, klanten en producten</li>
            <li>Contract beeindigen of opzeggen check (deleted at veranderen in opgezegd boolean)</li>
            <li>Front end validation voor het maken van klanten, contracten en producten</li>
            <li>Zorgen dat al verhuurde boxen niet nog een keer gekozen kunnen worden in contracten</li>
            <li>Een overzicht maken van alle verhuurde boxen vs alle onverhuurde boxen</li>
            <li>Een maand overzicht maken van de inkomsten (kleine statistiek pagina)</li>
          </ul>
        </v-flex>
      </v-card>
    </v-flex>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class Index extends Vue {}
</script>
