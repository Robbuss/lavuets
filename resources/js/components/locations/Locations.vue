<template>
  <v-col sm="12">
    <v-toolbar flat color="primary white--text">
      <v-toolbar-title>Locaties</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px">
        <template v-slot:activator="{ on }">
          <v-btn class="white--text" icon v-on="on">
            <v-icon>add</v-icon>
          </v-btn>
        </template>
        <v-card>
          <v-form ref="form" v-model="valid">
            <v-card-title>
              <span class="primary--text headline">Locatie</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-row wrap>
                  <v-col cols="12">
                    <ImageUpload :width="300" :height="300">
                      <v-btn>ADd</v-btn>
                    </ImageUpload>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.name"
                      persistent-hint
                      hint="Naam voor intern gebruik. Kan een code of afkorting zijn"
                      label="Interne naam"
                      prepend-icon="place"
                      required
                      :rules="[v => !!v || 'Interne naam kan niet leeg zijn']"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.facility_name"
                      prepend-icon="place"
                      persistent-hint
                      required
                      hint="Naam voor klanten die gebruikt wordt in het selectie formulier"
                      :rules="[v => !!v || 'Locatie naam kan niet leeg zijn']"
                      label="Locatie naam"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="primary" text @click="close">Annuleren</v-btn>
              <v-btn class="primary" text @click="save">Opslaan</v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="locations"
      class="elevation-1"
      :sort-by="['name', 'facility_name']"
      :sort-desc="[false, true]"
      multi-sort
      :footer-props="options"
    >
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
        <v-icon small @click="deleteItem(item)">delete</v-icon>
      </template>
      <template v-slot:no-data>
        <td colspan="100%" v-if="loading">Producten laden...</td>
        <td colspan="100%" v-else>Geen producten gevonden</td>
      </template>
    </v-data-table>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import ImageUpload from "js/components/ImageUpload.vue";
import store from "js/store";

@Component({
  components: {
    ImageUpload
  }
})
export default class Locations extends Vue {
  private response = "";
  private locations: any = [];
  private loading: boolean = true;
  private dialog: boolean = false;
  private valid: boolean = false;
  private headers: any = [
    {
      text: "Interne naam",
      align: "left",
      value: "name"
    },
    { text: "Locatie naam", value: "facility_name" },
    { text: "Acties", value: "actions", sortable: false, align: "right" }
  ];
  private options: any = {
    itemsPerPageText: "Locaties per pagina",
    itemsPerPageAllText: "Allemaal"
  };
  private editedIndex: number = -1;
  private editedItem: any = {
    name: "",
    facility_name: ""
  };
  private defaultItem: any = {
    name: "",
    facility_name: ""
  };

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
    if (!confirm("Wil je dit item verwijderen? ")) return;

    const index = this.locations.indexOf(item);
    this.locations.splice(index, 1);
    axios.post("/api/locations/" + item.id + "/delete");
    store.commit("snackbar", {
      type: "success",
      message: "Locatie verwijderd."
    });
  }

  close() {
    this.dialog = false;
    setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
    }, 300);
  }

  async save() {
    if (!(<any>this.$refs.form).validate()) {
      return;
    }
    if (this.editedIndex > -1) {
      Object.assign(this.locations[this.editedIndex], this.editedItem);
      await axios.post("/api/locations/" + this.editedItem.id, this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Locatie aangepast!"
      });
    } else {
      this.locations.push(this.editedItem);
      await axios.post("/api/locations/create", this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Locatie aangemaakt!"
      });
    }
    this.close();
  }
}
</script>
