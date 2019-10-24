<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Klanten</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Zoeken" single-line hide-details></v-text-field>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="createItem">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Klant toevoegen</span>
        </v-tooltip>
        <v-dialog v-model="dialog" max-width="80%">
          <edit-create-customer
            v-if="dialog"
            @saved="createdItem"
            @canceled="close"
            :creating="createMode"
            :enableFields="true"
            :customer="editedItem"
          ></edit-create-customer>
        </v-dialog>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="customers"
        class="elevation-1"
        :loading="loading"
        :pagination.sync="paginationSync"
        :search="search"
      >
        <template v-slot:items="props">
          <tr class="pointer">
            <td
              class="pointer"
              @click="$router.push('/customers/' + props.item.id)"
            >{{ props.item.name }}</td>
            <td @click="$router.push('/customers/' + props.item.id)">{{ props.item.email }}</td>
            <td @click="$router.push('/customers/' + props.item.id)">{{ props.item.company_name }}</td>
            <td @click="$router.push('/customers/' + props.item.id)">{{ props.item.phone }}</td>
            <td>{{ props.item.city }}</td>
            <td>{{ formatDate(props.item.created_at) }}</td>
            <td class="justify-center layout px-0">
              <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
              <v-icon small @click="deleteItem(props.item)">delete</v-icon>
            </td>
          </tr>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Klanten laden...</td>
          <td colspan="100%" v-else>Geen klanten</td>
        </template>
      </v-data-table>
    </div>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import EditCreateCustomer from "./EditCreate.vue";
import * as moment from "moment";

@Component({
  components: {
    editCreateCustomer: EditCreateCustomer
  }
})
export default class Customers extends Vue {
  private response = "";
  private customers: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private createMode: boolean = true;
  private editedItem: any = null;
  private search: string = "";

  private paginationSync: any = {
    rowsPerPage: 25,
    descending: true,
    sortBy: 'created_at'
  };

  private headers: any = [
    { text: "Naam", align: "left", value: "name" },
    { text: "Email", align: "left", value: "email" },
    { text: "Bedrijf", value: "company_name" },
    { text: "Telefoonnummer", value: "phone" },
    { text: "Stad", value: "city" },
    // { text: "Straat", value: "street_addr" },
    // { text: "Nr", value: "street_number" },
    // { text: "Postcode", value: "postal_code" },
    // { text: "BTW", value: "btw" },
    // { text: "KvK", value: "kvk" },
    { text: "Aangemaakt", value: "created_at" },
    { text: "Actions", value: "name", sortable: false }
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
      this.customers = (await axios.get("/api/customers")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  deleteItem(item: any) {
    const index = this.customers.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.customers.splice(index, 1) &&
      axios.post("/api/customers/" + item.id + "/delete");
    
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

  editItem(customer: any) {
    this.createMode = false;
    this.editedItem = customer;
    this.dialog = true;
  }

  close() {
    this.editedItem = null;
    this.dialog = false;
  }
}
</script>
