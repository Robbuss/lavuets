<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Logs</v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-data-table :headers="headers" :items="logs" class="elevation-1" :loading="loading" :pagination.sync="paginationSync">
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>{{ props.item.log_name }}</td>
          <td>{{ props.item.description }}</td>
          <td>{{ props.item.subject_id }}</td>
          <td>{{ props.item.subject_type }}</td>
          <td>{{ props.item.causer_id }}</td>
          <td>{{ props.item.causer_type }}</td>
          <td>{{ props.item.properties }}</td>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Logs laden...</td>
          <td colspan="100%" v-else>Geen logs</td>
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
export default class Logs extends Vue {
  private response = "";
  private logs: any = [];
  private loading: boolean = true;

  private paginationSync: any = {
    rowsPerPage: 25,
  }

  private headers: any = [
    { text: "Id", align: "left", value: "id" },
    { text: "Naam", align: "left", value: "log_name" },
    { text: "Beschrijving", value: "description" },
    { text: "subject_id", value: "subject_id" },
    { text: "subject_type", value: "subject_type" },
    { text: "causer_id", value: "causer_id" },
    { text: "causer_type", value: "causer_type" },
    { text: "properties", value: "properties" },
  ];

  async mounted() {
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      this.logs = (await axios.get("/api/logs")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
