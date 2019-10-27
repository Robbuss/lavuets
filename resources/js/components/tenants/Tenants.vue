<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Huurders</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field class="pt-0" v-model="search" append-icon="search" label="Zoeken" single-line hide-details></v-text-field>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="createItem">
            <v-icon>add</v-icon>
          </v-btn>
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
        :pagination.sync="paginationSync"
        :search="search"
      >
        <template v-slot:items="props">
          <tr class="pointer">
            <td
              class="pointer"
              @click="$router.push('/tenants/' + props.item.id)"
            >{{ props.item.name }}</td>
            <td @click="$router.push('/tenants/' + props.item.id)">{{ props.item.email }}</td>
            <td @click="$router.push('/tenants/' + props.item.id)">{{ props.item.company_name }}</td>
            <td @click="$router.push('/tenants/' + props.item.id)">{{ props.item.phone }}</td>
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
      this.tenants = (await axios.get("/api/tenants")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  deleteItem(item: any) {
    const index = this.tenants.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.tenants.splice(index, 1) &&
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
