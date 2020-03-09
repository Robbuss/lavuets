<template>
  <v-container>
    <v-img contain src="404.svg" style="max-height:400px;"/>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class NotFoundException extends Vue {}
</script>
