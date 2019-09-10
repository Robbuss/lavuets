<template>
  <v-container fluid pa-3 ma-0 grid-list-md v-if="!loading">
    <v-layout row wrap>
      <v-flex xs12 sm12>
        <edit-create-customer :customer="customer" :creating="false"></edit-create-customer>
    </v-flex>
  </v-layout>
  <v-layout row wrap>
    <files :customer="customer"></files>
  </v-layout>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import EditCreateCustomer from "./EditCreate.vue";
import Files from "./Files.vue";

@Component({
  components: {
    editCreateCustomer: EditCreateCustomer,
    files: Files
  }
})
export default class SingleCustomer extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private customer: any = {}

  async mounted() {
    try {
      this.customer = (await axios.get("/api/customers/" + this.$route.params.id)).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
