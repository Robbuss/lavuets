<template>
  <v-tooltip bottom>
    <v-btn icon @click="$router.back()" slot="activator">
      <v-icon>chevron_left</v-icon>
    </v-btn>
    <span>Terug naar het vorige scherm</span>
  </v-tooltip>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class BackButton extends Vue {}
</script>