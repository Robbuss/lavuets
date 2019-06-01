<template>
  <v-container fluid grid-list-md v-if="!loading" fill-height>
    <v-layout row wrap>
      <v-flex xs12 sm6 md4>
        <edit-create-unit :unit="unit" :creating="false"></edit-create-unit>
    </v-flex>
      <v-flex xs12 sm6 md8>
      <v-toolbar class="primary" dark>
        <v-toolbar-title>Contracten op dit product</v-toolbar-title>
      </v-toolbar>
      <v-list>
        <v-list-tile
          v-for="(contract, i ) in contracts"
          :key="i"
          @click="$router.push('/contracts/' + contract.id)"
        >
          <v-list-tile-content>
            <v-list-tile-title>Klant: {{ contract.customer.name }}</v-list-tile-title>
            <v-list-tile-sub-title>Prijs: €{{ contract.price }}</v-list-tile-sub-title>
          </v-list-tile-content>
          <v-list-tile-action v-if="contract.active">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-icon color="success--text" dark v-on="on">check</v-icon>
            </template>
            <span>Dit contract loopt nog</span>
          </v-tooltip>
          </v-list-tile-action>
        </v-list-tile>
        <v-list-tile v-if="contracts.length === 0">
          <v-list-tile-content>
            Nog niet verhuurd.
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-flex>
  </v-layout>
  </v-container>
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
export default class SingleUnit extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private contracts: any = null;
  private unit: any = {}

  async mounted() {
    try {
      const r = (await axios.get("/api/units/" + this.$route.params.id)).data;
      this.unit = r.unit;
      this.contracts = r.contracts;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
