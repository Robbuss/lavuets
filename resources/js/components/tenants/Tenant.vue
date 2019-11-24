<template>
  <v-col v-if="!loading">
    <v-row wrap>
      <v-col cols="12" sm="12">
        <edit-create-tenant :tenant="tenant" :creating="false"></edit-create-tenant>
      </v-col>
    </v-row>
    <v-row wrap  pt-4>
      <v-col cols="12" sm="12">
        <v-card>
          <v-toolbar dense flat class="primary" dark>
            <BackButton />
            <v-toolbar-title>Contracten</v-toolbar-title>
          </v-toolbar>

          <v-card-text>
            <v-list>
              <template v-for="contract in tenant.contracts">
                <v-list-item @click="$router.push('/contracts/' + contract.id)" :key="contract.id">
                  <v-list-item-content>
                    <v-list-item-title
                      :key="i"
                      v-for="u, i in contract.units.map((v) => v.display_name)"
                    >{{ u }}</v-list-item-title>
                    <v-list-item-subtitle>{{ contract.start_date }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-col>
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
