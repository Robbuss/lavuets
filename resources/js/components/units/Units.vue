<template>
  <v-flex sm12>
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Boxen</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <v-btn icon slot="activator" @click="dialog = true">
            <v-icon>add</v-icon>
          </v-btn>
          <span>Box toevoegen</span>
        </v-tooltip>
        <v-dialog v-model="dialog" max-width="80%" persistent>
          <v-toolbar flat color="primary" dark>
            <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="dialog = !dialog">
              <v-icon>close</v-icon>
            </v-btn>
          </v-toolbar>
          <v-card class="pa-3">
            <v-layout row wrap>
              <v-flex sm12 class="pa-2">
                <v-text-field label="Box / product naam" v-model="editedItem.name"/>
              </v-flex>
              <v-flex sm12 md6 class="pa-2">
                <v-text-field label="Grootte in m3" v-model="editedItem.size"/>
              </v-flex>
              <v-flex sm12 md6 class="pa-2">
                <v-text-field label="Prijs in euro's" v-model="editedItem.price"/>
              </v-flex>
              <v-flex sm12>
                <v-btn color="primary" @click="save">Opslaan</v-btn>
              </v-flex>
            </v-layout>
          </v-card>
        </v-dialog>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="units"
        class="elevation-1"
        :loading="loading"
        rows-per-page-text="Boxen per pagina"
        :pagination.sync="pagination"
      >
        <template v-slot:items="props">
          <td
            style="cursor: pointer"
            @click="$router.push('/units/' + props.item.id)"
          >{{ props.item.name }}</td>
          <td>{{ props.item.size }}</td>
          <td>€{{ props.item.price }}</td>
          <td class="justify-end layout">
            <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
            <v-icon small @click="deleteItem(props.item)">delete</v-icon>
          </td>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Boxen laden...</td>
          <td colspan="100%" v-else>Geen boxen geveonden</td>
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
export default class Units extends Vue {
  private response = "";
  private fields = ["name", "size", "price", "x", "y"];
  private units: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;

  private headers: any = [
    { text: "Naam", value: "name" },
    { text: "Grootte (m3)", value: "size" },
    { text: "Prijs (p/m)", value: "price" },
    { text: "Acties", align: "right", sortable: false }
  ];
  private editedIndex: number = -1;
  private editedItem: any = {
    id: null,
    name: "",
    size: "",
    price: 0
  };
  private defaultItem: any = {
    id: null,
    name: "",
    size: "",
    price: 0
  };
  private pagination: any = {
    rowsPerPage: 25
  };

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  async mounted() {
    try {
      this.units = (await axios.get("/api/units")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  get chosenUnitName() {
    return this.units.find((x: any) => x.id === this.editedItem.id).name;
  }

  get formTitle() {
    return this.editedIndex === -1 ? "Unit aanmaken" : "Unitbewerken bewerken";
  }

  editItem(item: any) {
    this.editedIndex = this.units.indexOf(item);
    this.editedItem = Object.assign({}, item);
    this.dialog = true;
  }

  deleteItem(item: any) {
    const index = this.units.indexOf(item);
    confirm("Weet je zeker dat je deze unit wilt verwijderen?") &&
      this.units.splice(index, 1) &&
      axios.post("/api/units/" + item.id + "/delete");
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
      axios.post("/api/units/" + this.editedItem.id, this.editedItem);
      Object.assign(this.units[this.editedIndex], this.editedItem);
    } else {
      axios.post("/api/units/create", this.editedItem);
      this.units.push(this.editedItem);
    }
    this.close();
  }
}
</script>
