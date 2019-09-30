<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Klanten</v-toolbar-title>
        <v-spacer></v-spacer>
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
          :customer="editedItem">
          </edit-create-customer>
        </v-dialog>
      </v-toolbar>
      <v-data-table :headers="headers" :items="customers" class="elevation-1" :loading="loading" :pagination.sync="paginationSync">
        <template v-slot:items="props">
          <td
            class="pointer"
            @click="$router.push('/customers/' + props.item.id)"
          >{{ props.item.name }}</td>
          <td>{{ props.item.email }}</td>
          <td>{{ props.item.company_name }}</td>
          <td>{{ props.item.phone }}</td>
          <td>{{ props.item.city }}</td>
          <td>{{ props.item.street_addr }}</td>
          <td>{{ props.item.street_number }}</td>
          <td>{{ props.item.postal_code }}</td>
          <td>{{ props.item.btw }}</td>
          <td>{{ props.item.kvk }}</td>
          <td class="justify-center layout px-0">
            <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
            <v-icon small @click="deleteItem(props.item)">delete</v-icon>
          </td>
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
import EditCreateCustomer from "./EditCreate.vue";

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

  private paginationSync: any = {
    rowsPerPage: 25,
  }

  private headers: any = [
    { text: "Naam", align: "left", value: "name" },
    { text: "Email", align: "left", value: "email" },
    { text: "Bedrijf", value: "company_name" },
    { text: "Telefoonnummer", value: "phone" },
    { text: "Stad", value: "city" },
    { text: "Straat", value: "street_addr" },
    { text: "Nr", value: "street_number" },
    { text: "Postcode", value: "postal_code" },
    { text: "BTW", value: "btw" },
    { text: "KvK", value: "kvk" },
    { text: "Actions", value: "name", sortable: false }
  ];

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
  }

  createItem() {
    this.createMode = true;
    this.dialog = true;
  }

  async createdItem(){
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
