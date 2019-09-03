<template>
    <v-stepper
      alt-labels
      class="flat-style"
      :value="step"
      style="  
        background: none !important;
        border: none !important;
        box-shadow: none !important;">
      <v-stepper-header>
        <v-stepper-step :complete="step > 1" step="1">Kies een locatie</v-stepper-step>
        <v-divider></v-divider>

        <v-stepper-step :complete="step > 2" step="2">Kies een box</v-stepper-step>
        <v-divider></v-divider>

        <v-stepper-step step="3">
          Vul gegevens in
          <small>je kan de box in 24 uur gebruiken</small>
        </v-stepper-step>
      </v-stepper-header>
    </v-stepper>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class BookingHeader extends Vue {
  @Prop()
  step: number;
}
</script>
