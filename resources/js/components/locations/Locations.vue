<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary white--text">
        <v-toolbar-title>Locaties</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-2" v-on="on">New Item</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">Locatie</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.name" label="Internal name"></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field v-model="editedItem.facility_name" label="Facility Name"></v-text-field>
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
      <v-data-table :headers="headers" :items="locations" class="elevation-1">
        <template v-slot:items="props">
          <td>{{ props.item.name }}</td>
          <td>{{ props.item.facility_name }}</td>
          <td class="justify-end layout">
            <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
            <v-icon small @click="deleteItem(props.item)">delete</v-icon>
          </td>
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
export default class Locations extends Vue {
  private response = "";
  private locations: any = [];
  private loading: boolean = true;

  async mounted() {
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      this.locations = (await axios.get("/api/locations")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  private dialog: boolean = false;
  private headers: any = [
    {
      text: "Name",
      align: "left",
      value: "name"
    },
    { text: "Facility Name", value: "facility_name" },
    { text: "actions", value: "name", sortable: false, align: "right" }
  ];
  private editedIndex: number = -1;
  private editedItem: any = {
    name: "",
    facility_name: ""
  };
  private defaultItem: any = {
    name: "",
    facility_name: ""
  };

  @Watch("dialog")
  onDialogChanged(newValue: boolean, oldValue: boolean) {
    newValue || this.close();
  }

  editItem(item: any) {
    this.editedIndex = this.locations.indexOf(item);
    this.editedItem = Object.assign({}, item);
    this.dialog = true;
  }

  deleteItem(item: any) {
    const index = this.locations.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.locations.splice(index, 1);
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
      Object.assign(this.locations[this.editedIndex], this.editedItem);
    } else {
      this.locations.push(this.editedItem);
    }
    this.close();
  }
}
</script>
