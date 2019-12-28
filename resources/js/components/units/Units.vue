<template>
  <v-col sm="12">
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Boxen en Producten</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field
          class="white--text"
          v-model="search"
          append-icon="search"
          label="Zoeken"
          single-line
          hide-details
        ></v-text-field>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on" @click="createItem">
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <span>Product toevoegen</span>
        </v-tooltip>
        <v-dialog v-model="dialog" max-width="80%" persistent>
          <edit-create-unit
            v-if="dialog"
            @saved="createdItem"
            @canceled="close"
            :creating="createMode"
            :unit="editedItem"
          ></edit-create-unit>
        </v-dialog>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :search="search"
        :items="units"
        class="elevation-1 pointer"
        :loading="loading"
        :itemsPerPage="50"
        :footer-props="options"
        multi-sort
        @click:row="$router.push('/units/' + $event.id)"
      >
        <template v-slot:item.price="{ item }">
          €{{ item.price }}
        </template>
        <template v-slot:item.status="{ item }">
          <v-chip
            flat
            dark
            :class="{'green' : item.active, 'orange' : !item.active }"
            v-if="item.free"
          >
            Beschikbaar
            <span v-if="!item.active">, niet verhuurbaar</span>
          </v-chip>
          <v-chip flat v-else>Verhuurd</v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon small class="mr-2" @click.stop="editItem(item)">edit</v-icon>
          <v-icon small @click.stop="deleteItem(item)">delete</v-icon>
        </template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Producten laden...</td>
          <td colspan="100%" v-else>Geen producten gevonden</td>
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
import EditCreateUnit from "./EditCreate.vue";

@Component({
  components: {
    editCreateUnit: EditCreateUnit
  }
})
export default class Units extends Vue {
  private response = "";
  private units: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private createMode: boolean = true;
  private editedItem: any = null;
  private search: string = "";

  private headers: any = [
    { text: "Locatie", value: "facility_name" },
    { text: "Naam", value: "name" },
    { text: "Grootte (m3)", value: "size" },
    { text: "Prijs (p/m)", value: "price" },
    { text: "Status", value: "status" },
    { text: "Acties", value: "actions", align: "right", sortable: false }
  ];

  private options: any = {
    itemsPerPageText: "Producten per pagina",
    itemsPerPageAllText: "Allemaal"
  };

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
      this.units = (await axios.get("/api/units")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  deleteItem(item: any) {
    if (
      !confirm(
        "Product verwijderen? Als er contracten actief zijn op dit product gaat het mis!"
      )
    )
      return;

    const index = this.units.indexOf(item);
    this.units.splice(index, 1) &&
      axios.post("/api/units/" + item.id + "/delete") &&
      store.commit("snackbar", {
        type: "success",
        message: "Product verwijderd!"
      });
  }

  createItem() {
    this.createMode = true;
    this.dialog = true;
  }

  async createdItem() {
    this.dialog = false;
    await this.getData();
  }

  editItem(unit: any) {
    this.createMode = false;
    this.editedItem = unit;
    this.dialog = true;
  }

  close() {
    this.editedItem = null;
    this.dialog = false;
  }
}
</script>
