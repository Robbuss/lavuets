<template>
  <v-col sm="12">
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Logs</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field
          class="white--text pt-0"
          clearable
          v-model="search"
          append-icon="search"
          label="Zoeken"
          single-line
          hide-details
        ></v-text-field>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :search="search"
        :items="logs"
        :items-per-page="25"
        multi-sort
        :sort-by="['created_at']"
        :sort-desc="[true]"
        class="elevation-1"
        :loading="loading"
        :pagination.sync="paginationSync"
      >
        <template v-slot:items="props">
          <td>{{ props.item.log_name }}</td>
          <td>{{ props.item.description }}</td>
          <td>{{ props.item.created_at }}</td>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Logs laden...</td>
          <td colspan="100%" v-else>Geen logs</td>
        </template>
      </v-data-table>
    </div>
  </v-col>
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
  private search: string | null = null;

  private options: any = {
    itemsPerPageText: "Logs per pagina",
    itemsPerPageAllText: "Allemaal"
  };

  private headers: any = [
    { text: "Naam", align: "left", value: "log_name" },
    { text: "Beschrijving", value: "description" },
    { text: "Datum/tijd", value: "created_at" }
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
