<template>
  <v-col v-if="!loading" fill-height class="py-0">
    <v-row wrap>
      <v-col cols="12" md="6">
        <edit-create-unit :unit="unit" :creating="false"></edit-create-unit>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar class="primary" flat dark>
            <v-toolbar-title>Contracten op dit product</v-toolbar-title>
          </v-toolbar>
          <v-list>
            <v-list-item
              v-for="(contract, i ) in contracts"
              :key="i"
              @click="$router.push('/contracts/' + contract.id)"
            >
              <v-list-item-content>
                <v-list-item-title>Verhuurd aan {{ contract.tenant.name }}</v-list-item-title>
                <v-list-item-subtitle>Sinds {{formatDate(contract.start_date) }}</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action v-if="contract.active">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon color="success--text" dark v-on="on">check</v-icon>
                  </template>
                  <span>Dit contract loopt nog</span>
                </v-tooltip>
              </v-list-item-action>
            </v-list-item>
            <v-list-item v-if="contracts.length === 0">
              <v-list-item-content>Nog niet verhuurd.</v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import * as moment from "moment";
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
  private unit: any = {};

  formatDate(date: any){
    return moment(date).format('LL');
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
}
</script>
