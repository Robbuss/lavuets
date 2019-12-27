<template>
  <v-tooltip bottom>
    <template v-slot:activator="{ on }">
    <v-btn dark v-on="on" icon @click="$router.back()" slot="activator">
      <v-icon>chevron_left</v-icon>
    </v-btn>
    </template>
    <span>Terug naar het vorige scherm</span>
  </v-tooltip>
</template>

<script lang="ts">
import Vue from 'vue'
import { Component } from 'vue-property-decorator'

@Component({})
export default class BackButton extends Vue {}
</script>
