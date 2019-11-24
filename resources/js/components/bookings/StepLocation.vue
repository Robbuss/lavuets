<template>
  <v-row wrap v-if="!loading">
    <v-col cols="12" sm="6" md="3" :class="{'pr-3' :$vuetify.breakpoint.mdAndUp}" v-for="location in locations" :key="location.id">
      <v-card flat class="grey lighten-3 pa-1">
        <v-row fill-height justify-center align-center  pa-12 class="text-center white">
          <v-col>
            <h3 class="headline mb-0 primary--text">{{ location.facility_name }}</h3>
            <h5 class="subtitle-1 grey--text">filled en beschikbaar: {{ location.units_count }}</h5>
          </v-col>
        </v-row>
        <v-btn text color="primary" @click="$emit('done', location)">Kiezen</v-btn>
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
  private locations: any = [];
  private loading: boolean = true;

  async mounted() {
    this.locations = (await axios.get("/api/book-data/locations")).data;
    this.loading = false;
  }
}
</script>
