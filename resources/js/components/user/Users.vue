<template>
  <v-col sm="12">
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title>Gebruikers</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-data-table :headers="headers" :items="users" class="elevation-1" :loading="loading">
      <template v-slot:items="props">
        <td>{{ props.item.name }}</td>
        <td>{{ props.item.email }}</td>
        <td>{{ props.item.created_at }}</td>
      </template>
      <template v-slot:no-data>
        <td colspan="100%" v-if="loading">Gebruikers laden...</td>
        <td colspan="100%" v-else>Geen gebruikers gevonden</td>
      </template>
    </v-data-table>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Dashboard extends Vue {
  private loading: boolean = true;
  private users: any = [];
  private headers: any = [
    {
      text: "Name",
      align: "left",
      value: "name"
    },
    { text: "E-mail", value: "email" },
    { text: "Created at", value: "created_at" }
  ];

  async mounted() {
    try {
      this.users = (await axios.get("/api/user/index")).data;
      this.loading = false;
    } catch (e) {
      // this.users = e.message;
    }
  }
}
</script>
