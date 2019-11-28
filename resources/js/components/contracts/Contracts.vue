<template>
  <v-col sm="12">
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title>Contracten</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-text-field
        class="white--text pt-0"
        v-model="search"
        append-icon="search"
        label="Zoeken"
        single-line
        hide-details
      ></v-text-field>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" icon @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
        </template>
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
      :footer-props="options"
      :sort-by="['created_at']"
      :sort-desc="[true]"
      @click:row="$router.push('/contracts/' + $event.id)"
    >
      <template v-slot:item.start_date="{ item }">{{ formatDate(item.start_date) }}</template>
      <template v-slot:item.deactivated_at="{ item }">
        <v-chip v-if="item.deactivated_at">{{ formatDate(item.deactivated_at) }}</v-chip>
      </template>
      <template v-slot:item.payment_method="{ item }">
        <v-chip
          :class="{'info' : item.payment_method === 'incasso', 'grey' : item.payment_method === 'factuur'}"
          flat
          dark
        >{{ item.payment_method }}</v-chip>
      </template>
      <template v-slot:item.auto_invoice="{ item }">
        <v-icon v-if="item.auto_invoice" class="green--text lighten-3">check</v-icon>
        <v-icon v-else class="red--text lighten-3">close</v-icon>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
        <v-icon small @click="deleteItem(item)">delete</v-icon>
      </template>
      <template v-slot:no-data>
        <td colspan="100%" v-if="loading">Contracten laden...</td>
        <td colspan="100%" v-else>Geen contracten</td>
      </template>
    </v-data-table>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import EditCreate from "./EditCreate.vue";
import axios from "js/axios";
import store from "js/store";
import * as moment from "moment";

@Component({
  components: {
    editCreate: EditCreate
  }
})
export default class Contracts extends Vue {
  private response = "";
  private search = "";
  private contracts: any = [];
  private tenants: any = [];
  private units: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private showEndDate: boolean = true;
  private step: number = 0;
  private chosenContract: any = {};

  private options: any = {
    itemsPerPageText: "Contracten per pagina",
    itemsPerPageAllText: "Allemaal"
  };

  private headers: any = [
    { text: "Klantnaam", value: "tenant_name" },
    { text: "Ingangsdatum", value: "start_date" },
    { text: "Gedeactiveerd op", value: "deactivated_at" },
    { text: "Type", value: "payment_method" },
    { text: "Verzend factuur", value: "auto_invoice" },
    { text: "Bedrijfsnaam", value: "company_name" },
    { text: "Acties", value: "actions", sortable: false }
  ];

  formatDate(date: any) {
    return moment(date).format("LL");
  }

  async getData() {
    this.loading = true;
    try {
      const r = (await axios.get("/api/contracts")).data;
      this.contracts = r.contracts;
      this.tenants = r.tenants;
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
    if (!confirm("Wil je dit contract echt verwijderen?")) return;
    const index = this.contracts.indexOf(item);
    this.contracts.splice(index, 1);
    axios.post("/api/contracts/" + item.id + "/delete");

    store.commit("snackbar", {
      type: "success",
      message: "Contract verwijderd."
    });
  }

  async close() {
    await this.getData();
    this.dialog = !this.dialog;
    this.chosenContract = {};
  }
}
</script>
