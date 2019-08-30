<template>
  <v-card flat class="grey lighten-3 pa-1">
    <v-layout row fill-height justify-center align-center pa-5 class="text-xs-center white">
      <v-flex>
        Hier komt het formulier
      </v-flex>
    </v-layout>
    <v-btn flat color="primary" @click="$emit('done', 'breukelen')">Kiezen</v-btn>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class StepCustomer extends Vue {

}
</script>
