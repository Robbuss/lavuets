<template>
  <v-container fluid grid-list-md v-if="!loading" fill-height>
    <v-layout row wrap>
      <v-flex xs12 sm12>
      <v-toolbar flat class="primary" dark>
        <v-toolbar-title>{{customer.name }}</v-toolbar-title>
      </v-toolbar>
      <v-card flat class="pa-4">
        {{ customer.name }}
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
export default class SingleCustomer extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private contracts: any = null;
  private customer: any = {
      btw: "",
      city: "",
      company_name: "",
      created_at: "",
      email: "",
      id: null,
      kvk: "",
      name: "",
      postal_code: "",
      street_addr: "",
      street_number: "",
  };

  @Watch("dialog")
  onDialogChanged(oldval: any, newval: any) {
    oldval || this.close();
  }

  async mounted() {
    try {
      this.customer = (await axios.get("/api/customers/" + this.$route.params.id)).data;
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
