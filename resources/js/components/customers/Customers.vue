<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Klanten</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Klant toevoegen</span>
        </v-tooltip>
        <v-dialog v-model="dialog" max-width="80%">
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.name" label="Naam"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.email" label="E-mail"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.company_name" label="Bedrijfsnaam"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.city" label="Stad"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.street_addr" label="Straat"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.street_number" label="Nummer"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.postal_code" label="Postcode"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.kvk" label="KVK"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.btw" label="BTW"></v-text-field>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
              <v-btn color="blue darken-1" flat @click="save">Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
      <v-data-table :headers="headers" :items="customers" class="elevation-1" :loading="loading">
        <template v-slot:items="props">
          <td class="pointer" @click="$router.push('/customers/' + props.item.id)">{{ props.item.name }}</td>
          <td>{{ props.item.email }}</td>
          <td>{{ props.item.company_name }}</td>
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

@Component({})
export default class Customers extends Vue {
  private response = "";
  private customers: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;

  private headers: any = [
    { text: "Naam", align: "left", value: "name" },
    { text: "Email", align: "left", value: "email" },
    { text: "Bedrijf", value: "company_name" },
    { text: "Stad", value: "city" },
    { text: "Straat", value: "street_addr" },
    { text: "Nr", value: "street_number" },
    { text: "Postcode", value: "postal_code" },
    { text: "BTW", value: "btw" },
    { text: "KvK", value: "kvk" },
    { text: "Actions", value: "name", sortable: false }
  ];
  private editedIndex: number = -1;
  private editedItem: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    city: "",
    street_addr: "",
    street_number: 0,
    postal_code: "",
    btw: "",
    kvk: ""
  };
  private defaultItem: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    city: "",
    street_addr: "",
    street_number: 0,
    postal_code: "",
    btw: "",
    kvk: ""
  };

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  async mounted() {
    try {
      this.customers = (await axios.get("/api/customers")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  get formTitle() {
    return this.editedIndex === -1 ? "Klant aanmaken" : "Klant bewerken";
  }

  editItem(item: any) {
    this.editedIndex = this.customers.indexOf(item);
    this.editedItem = Object.assign({}, item);
    this.dialog = true;
  }

  deleteItem(item: any) {
    const index = this.customers.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.customers.splice(index, 1) && 
      axios.post('/api/customers/' + item.id + '/delete');
  }

  close() {
    this.dialog = false;
    setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
    }, 300);
  }

  save() {
    if (this.editedIndex > -1) {
      axios.post('/api/customers/' + this.editedItem.id, this.editedItem);
      Object.assign(this.customers[this.editedIndex], this.editedItem);
    } else {
      axios.post('/api/customers/create', this.editedItem);
      this.customers.push(this.editedItem);
    }
    this.close();
  }
}
</script>
