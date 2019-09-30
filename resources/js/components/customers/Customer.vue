<template>
  <v-container v-if="!loading">
    <v-layout row wrap pa-3>
      <v-flex xs12 sm12>
        <edit-create-customer :customer="customer" :creating="false"></edit-create-customer>
      </v-flex>
    </v-layout>
    <v-layout row wrap pa-3 mt-3>
      <v-flex xs12>
        <files :customer="customer"></files>
      </v-flex>
    </v-layout>
  </v-container>
</template> 

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import EditCreateCustomer from "./EditCreate.vue";
import Files from "../files/Files.vue";

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
  private customer: any = {};

  async mounted() {
    try {
      this.customer = (await axios.get(
        "/api/customers/" + this.$route.params.id
      )).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
