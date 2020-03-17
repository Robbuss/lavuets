<template>
  <v-hover v-slot:default="{ hover }">
    <v-card
      :elevation="hover ? 16 : 2"
      :class="{'primary white--text' : checked, 'fill-height': fill}"
      style="cursor:pointer"
      @click="$emit('picked',unit)"
    >
      <v-img class="white--text align-end" height="200px" :src="unit.thumb" />
      <v-card-title>{{ unit.name }}</v-card-title>
      <v-card-subtitle>{{ unit.size_m3 }}m3 voor €{{ unit.price}}</v-card-subtitle>
      <v-card-text class="ql-editor" v-html="unit.description"></v-card-text>
    </v-card>
  </v-hover>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class UnitCard extends Vue {
  @Prop()
  unit: any;

  @Prop()
  fill: boolean;

  @Prop()
  checked: boolean;
}
</script>