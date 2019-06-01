<template>
  <v-container fluid pa-3 ma-0 grid-list-md v-if="!loading">
    <v-layout row wrap>
      <v-flex xs12 sm12>
        <edit-create-invoice :invoice="invoice" :creating="false"></edit-create-invoice>
    </v-flex>
  </v-layout>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import EditCreateInvoice from "./EditCreate.vue";

@Component({
  components: {
    editCreateInvoice: EditCreateInvoice
  }
})
export default class SingleInvoice extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private invoice: any = {}

  async mounted() {
    try {
      this.invoice = (await axios.get("/api/invoices/" + this.$route.params.id)).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
