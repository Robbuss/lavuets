<template>
  <v-flex v-if="!loading">
    <v-layout row wrap>
      <v-flex xs12 sm12>
        <edit-create-tenant :tenant="tenant" :creating="false"></edit-create-tenant>
      </v-flex>
    </v-layout>
    <v-layout row wrap pt-3>
      <v-flex xs12 sm12>
        <v-card>
          <v-toolbar dense flat class="primary" dark>
            <BackButton />
            <v-toolbar-title>Contracten</v-toolbar-title>
          </v-toolbar>

          <v-card-text>
            <v-list>
              <template v-for="contract in tenant.contracts">
                <v-list-tile @click="$router.push('/contracts/' + contract.id)" :key="contract.id">
                  <v-list-tile-content>
                    <v-list-tile-title
                      :key="i"
                      v-for="u, i in contract.units.map((v) => v.display_name)"
                    >{{ u }}</v-list-tile-title>
                    <v-list-tile-sub-title>{{ contract.start_date }}</v-list-tile-sub-title>
                  </v-list-tile-content>
                </v-list-tile>
              </template>
            </v-list>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </v-flex>
</template> 

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import EditCreateTenant from "./EditCreate.vue";
import BackButton from "js/components/BackButton.vue";

@Component({
  components: {
    editCreateTenant: EditCreateTenant,
    BackButton
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
