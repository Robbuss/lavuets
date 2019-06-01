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
import EditCreateUnit from "./EditCreate.vue";

@Component({
  components: {
    editCreateUnit: EditCreateUnit
  }
})
export default class Units extends Vue {
  private response = "";
  private fields = ["name", "size", "price", "x", "y"];
  private units: any = [];
  private dialog: boolean = false;
  private loading: boolean = true;
  private createMode: boolean = true;
  private editedItem: any = null;

  private headers: any = [
    { text: "Naam", value: "name" },
    { text: "Grootte (m3)", value: "size" },
    { text: "Prijs (p/m)", value: "price" },
    { text: "Acties", align: "right", sortable: false }
  ];
  private pagination: any = {
    rowsPerPage: 25
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
    const index = this.units.indexOf(item);
    confirm("Are you sure you want to delete this item?") &&
      this.units.splice(index, 1) &&
      axios.post("/api/units/" + item.id + "/delete");
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
