<template>
  <v-col cols="12">
    <v-card-text>
      <v-form>
        <v-switch
          v-model="settings.find((x) => x.key === 'show_locations_booking').value"
          outlined
          label="Laat het aantal beschikbare boxen zien in boekingsformulier"
        />
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
}
</script>
