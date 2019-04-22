<template>
  <v-container fluid grid-list-md v-if="!loading" fill-height>
    <v-layout row wrap>
      <v-flex xs12 sm6 md4>
      <v-toolbar class="primary" dark>
        <v-toolbar-title>{{unit.name }}</v-toolbar-title>
      </v-toolbar>
      <v-card class="pa-4">{{ unit }}</v-card>
    </v-flex>
      <v-flex xs12 sm6 md8>
      <v-toolbar class="primary" dark>
        <v-toolbar-title>Contract</v-toolbar-title>
      </v-toolbar>
      <v-card class="pa-4">
        <v-flex v-if="contracts.length > 0">
          {{ contracts }}
        </v-flex>
        <p v-else>Niet verhuurd</p>
      </v-card>
    </v-flex>
  </v-layout>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class SingleUnit extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private contracts: any = null;
  private unit: any = {
    id: null,
    name: "",
    size: "",
    price: 0
  };

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

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

  close() {
    this.dialog = false;
  }

  save() {}
}
</script>
