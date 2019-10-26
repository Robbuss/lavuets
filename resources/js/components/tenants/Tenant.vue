<template>
  <v-flex v-if="!loading">
    <v-layout row wrap>
      <v-flex xs12 sm12>
        <edit-create-tenant :tenant="tenant" :creating="false"></edit-create-tenant>
      </v-flex>
    </v-layout>
    <v-layout row wrap mt-3>
      <v-flex xs12>
        <files :tenant="tenant"></files>
      </v-flex>
    </v-layout>
  </v-flex>
</template> 

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import EditCreateTenant from "./EditCreate.vue";
import Files from "../files/Files.vue";

@Component({
  components: {
    editCreateTenant: EditCreateTenant,
    files: Files
  }
})
export default class SingleTenant extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private tenant: any = {};

  async mounted() {
    try {
      this.tenant = (await axios.get(
        "/api/tenants/" + this.$route.params.id
      )).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
