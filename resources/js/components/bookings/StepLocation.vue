<template>
  <v-layout row wrap v-if="!loading">
    <v-flex xs12 sm6 md3 :class="{'pr-3' :$vuetify.breakpoint.mdAndUp}" v-for="location in locations" :key="location.id">
      <v-card flat class="grey lighten-3 pa-1">
        <v-layout row fill-height justify-center align-center pa-5 class="text-xs-center white">
          <v-flex>
            <h3 class="headline mb-0 primary--text">{{ location.facility_name }}</h3>
            <h5 class="subheading grey--text">Boxen beschikbaar: {{ location.units_count }}</h5>
          </v-flex>
        </v-layout>
        <v-btn flat color="primary" @click="$emit('done', location)">Kiezen</v-btn>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class StepLocation extends Vue {
  private locations: any = [];
  private loading: boolean = true;

  async mounted() {
    this.locations = (await axios.get("/api/book-data/locations")).data;
    this.loading = false;
  }
}
</script>
