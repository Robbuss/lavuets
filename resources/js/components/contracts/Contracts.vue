<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Contracten</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field
          class="white--text"
          v-model="search"
          append-icon="search"
          label="Search"
          single-line
          hide-details
        ></v-text-field>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Contract toevoegen</span>
        </v-tooltip>
        <edit-create v-if="dialog" :dialog="dialog" :contract="chosenContract" @input="close"></edit-create>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :search="search"
        :items="contracts"
        class="elevation-1"
        :loading="loading"
        rows-per-page-text="Contracten per pagina"
        :pagination.sync="pagination"
      >
        <template v-slot:items="props">
          <td
            class="pointer"
            @click="$router.push('/contracts/' + props.item.id)"
          >{{ props.item.customer_name }}</td>
          <td>{{ props.item.company_name }}</td>
          <td>{{ props.item.start_date }}</td>
          <td>{{ props.item.deactivated_at ? props.item.deactivated_at : "Actief" }}</td>
          <td class="justify-center layout px-0">
            <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
            <v-icon small @click="deleteItem(props.item)">delete</v-icon>
          </td>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Contracten laden...</td>
          <td colspan="100%" v-else>Geen contracten</td>
        </template>
      </v-data-table>
    </div>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import EditCreate from "./EditCreate.vue";
import axios from "js/axios";

@Component({
  components: {
    editCreate: EditCreate
  }
})
export default class Contracts extends Vue {
  private response = "";
  private search = "";
  private contracts: any = [];
  private customers: any = [];
  private units: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private showEndDate: boolean = true;
  private step: number = 0;
  private chosenContract: any = {};

  private headers: any = [
    { text: "Klantnaam", value: "customer_name" },
    { text: "Bedrijfsnaam", value: "company_name" },
    { text: "Ingangsdatum", value: "start_date" },
    { text: "Status", value: "active" },
    { text: "Actions", value: "name", sortable: false }
  ];
  private pagination: any = {
    rowsPerPage: 25
  };

  async getData() {
    this.loading = true;
    try {
      const r = (await axios.get("/api/contracts")).data;
      this.contracts = r.contracts;
      this.customers = r.customers;
      this.units = r.units;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  async mounted() {
    await this.getData();
  }

  editItem(item: any) {
    this.chosenContract = item;
    this.dialog = true;
  }

  deleteItem(item: any) {
    const index = this.contracts.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.contracts.splice(index, 1) &&
      axios.post("/api/contracts/" + item.id + "/delete");
  }

  async close() {
    await this.getData();
    this.dialog = !this.dialog;
    this.chosenContract = {};
  }
}
</script>
