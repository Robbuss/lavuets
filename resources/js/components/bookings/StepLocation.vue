<template>
  <v-layout row wrap>
    <v-flex xs12 sm6 md3 :class="{'pr-3' :$vuetify.breakpoint.mdAndUp}">
      <v-card flat class="grey lighten-3 pa-1">
        <v-layout row fill-height justify-center align-center pa-5 class="text-xs-center white">
          <v-flex>
            <h3 class="headline mb-0 primary--text">Breukelen</h3>
            <h5 class="subheading grey--text">Boxen beschikbaar: {{ count }}</h5>
          </v-flex>
        </v-layout>
        <v-btn flat color="primary" @click="$emit('done', 'breukelen')">Kiezen</v-btn>
      </v-card>
    </v-flex>
    <v-flex xs12 sm6 md3>
      <v-card flat class="grey lighten-3 pa-1">
        <v-layout row fill-height justify-center align-center pa-5 class="text-xs-center white">
          <v-flex>
            <h3 class="headline mb-0 primary--text">Box aan huis</h3>
            <h5 class="subheading grey--text">Boxen beschikbaar: 0</h5>
          </v-flex>
        </v-layout>
        <v-btn flat color="primary" disabled @click="$emit('done', 'breukelen')">Kiezen</v-btn>
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
  @Prop()
  count: number;

  private locations: any = [];
}
</script>
