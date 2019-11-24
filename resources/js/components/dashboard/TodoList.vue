<template>
  <v-card>
    <v-toolbar flat dense color="primary white--text">
      <v-toolbar-title>Todo list:</v-toolbar-title>
    </v-toolbar>
    <v-col pa-0>
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
    "Aanpasbare verhuur tijd van producten",
    "Mollie mislukte betalingen afhandelen (redirect nog naar succes page)",
    "Betalingsherinneringen",
    "Add Settings",
    "Relatienummers op facturen",
    "Write a test for the MolliePaymentClass",
  ];
}
</script>