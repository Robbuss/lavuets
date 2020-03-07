<template>
  <v-row v-if="!loading">
    <v-col
      cols="12"
      sm="6"
      md="3"
      :class="{'pr-3' :$vuetify.breakpoint.mdAndUp}"
      v-for="location in locations"
      :key="location.id"
    >
      <v-card outlined class="grey lighten-3">
        <v-card-text class="white text-center">
          <h3 class="headline mb-0 primary--text">{{ location.facility_name }}</h3>
          <h5 class="subtitle-1 grey--text" v-if="location.units_count">Aantal beschikbaar: {{ location.units_count }}</h5>
        </v-card-text>
        <v-card-actions>
          <v-btn text color="primary" @click="$emit('done', location)">Kiezen</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class StepLocation extends Vue {
  @Prop()
  domain: string;

  private locations: any = [];
  private loading: boolean = true;

  async mounted() {
    let url = this.domain
      ? this.domain + "/api/book-data/locations"
      : "/api/book-data/locations";

    this.locations = (await axios.get(url)).data;
    this.loading = false;
  }
}
</script>
