<template>
  <v-col sm="12">
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Huurders</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field
          class="pt-0"
          v-model="search"
          append-icon="search"
          label="Zoeken"
          single-line
          hide-details
        ></v-text-field>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" icon slot="activator" @click="createItem">
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <span>Huurder toevoegen</span>
        </v-tooltip>
        <v-dialog v-model="dialog" max-width="80%">
          <edit-create-tenant
            v-if="dialog"
            @saved="createdItem"
            @canceled="close"
            :creating="createMode"
            :enableFields="true"
            :tenant="editedItem"
          ></edit-create-tenant>
        </v-dialog>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="tenants"
        class="elevation-1"
        :loading="loading"
        :footer-props="options"
        :search="search"
        :items-per-page="25"
        multi-sort
        :sort-by="['created_at']"
        :sort-desc="[true]"
        @click:row="$router.push('/tenants/' + $event.id)"
      >
        <template v-slot:item.created_at="{ item }">{{ formatDate(item.created_at) }}</template>
        <template v-slot:item.actions="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
          <v-icon small @click="deleteItem(item)">delete</v-icon>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Klanten laden...</td>
          <td colspan="100%" v-else>Geen klanten</td>
        </template>
      </v-data-table>
    </div>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import EditCreateTenant from "./EditCreate.vue";
import * as moment from "moment";

@Component({
  components: {
    editCreateTenant: EditCreateTenant
  }
})
export default class Tenants extends Vue {
  private response = "";
  private tenants: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private createMode: boolean = true;
  private editedItem: any = null;
  private search: string = "";

  private options: any = {
    itemsPerPageText: "Huurders per pagina",
    itemsPerPageAllText: "Allemaal"
  };

  private headers: any = [
    { text: "Naam", align: "left", value: "name" },
    { text: "Email", align: "left", value: "email" },
    { text: "Bedrijf", value: "company_name" },
    { text: "Telefoonnummer", value: "phone" },
    { text: "Stad", value: "city" },
    { text: "Aangemaakt", value: "created_at" },
    { text: "Acties", value: "actions", sortable: false, align: "right" }
  ];

  formatDate(date: any) {
    return moment(date).format("LLL");
  }

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  async mounted() {
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      this.tenants = (await axios.get("/api/tenants")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  deleteItem(item: any) {
    if (!confirm("Wil je deze klant verwijderen?")) return;

    const index = this.tenants.indexOf(item);
    this.tenants.splice(index, 1);
    axios.post("/api/tenants/" + item.id + "/delete");
    store.commit("snackbar", { type: "success", message: "Klant verwijderd." });
  }

  createItem() {
    this.createMode = true;
    this.dialog = true;
  }

  async createdItem() {
    this.dialog = false;
    await this.getData();
  }

  editItem(tenant: any) {
    this.createMode = false;
    this.editedItem = tenant;
    this.dialog = true;
  }

  close() {
    this.editedItem = null;
    this.dialog = false;
  }
}
</script>
