<template>
  <v-card>
    <v-toolbar class="primary" flat dark>
      <v-toolbar-title>Contracten op dit product</v-toolbar-title>
    </v-toolbar>
    <v-list>
      <v-list-item
        v-for="(contract, i ) in unit.contracts"
        :key="i"
        @click="$router.push('/contracts/' + contract.id)"
      >
        <v-list-item-content>
          <v-list-item-title>Verhuurd aan {{ contract.tenant.name }}</v-list-item-title>
          <v-list-item-subtitle>Sinds {{formatDate(contract.start_date) }}</v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action v-if="contract.deactivated_at">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-icon color="success--text" dark v-on="on">check</v-icon>
            </template>
            <span>Dit contract loopt nog</span>
          </v-tooltip>
        </v-list-item-action>
      </v-list-item>
      <v-list-item v-if="unit.contracts.length === 0">
        <v-list-item-content>Nog niet verhuurd.</v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import * as moment from "moment";

@Component({})
export default class SingleUnitHistory extends Vue {
  @Prop()
  unit: any;

  formatDate(date: any) {
    return moment(date).format("LL");
  }
}
</script>
