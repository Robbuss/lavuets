<template>
  <v-col cols="12">
    <v-card-text>
      <v-form>
        <span v-if="showCount">Aantal beschikbare boxen wordt getoont in het formulier</span>
        <span v-else>
          Aantal beschikbare boxen wordt
          <b>niet</b> getoont in het formulier
        </span>
        <v-switch v-model="showCount" outlined :label="showCount ? 'Aan' : 'Uit'" />
        <v-btn
          @click="$emit('update')"
          color="primary"
          :disabled="loading"
          :loading="loading"
          depressed
        >Opslaan</v-btn>
      </v-form>
    </v-card-text>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class Booking extends Vue {
  @Prop()
  settings: any;

  get showCount() {
    return this.settings.find((x: this["settings"][0]) => x.key === "show_locations_booking").value;
  }

  set showCount(value: boolean) {
    this.settings.find((x: this["settings"][0]) => x.key === "show_locations_booking").value = value;
  }
}
</script>
