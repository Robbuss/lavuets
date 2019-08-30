<template>
  <v-card flat class="grey lighten-3 pa-1" v-if="units">
    <v-layout row wrap class="white">
      <v-flex md2>
        <v-item-group v-model="window" class="mr-4" mandatory tag="v-flex">
          <v-item v-for="n in units" :key="n">
            <div slot-scope="{ active, toggle }">
              <v-btn :input-value="active" @click="toggle">{{ n[0].size }} M3</v-btn>
            </div>
          </v-item>
        </v-item-group>
      </v-flex>

      <v-flex md10>
        <v-window v-model="window" vertical>
          <v-window-item v-for="n in units" :key="n">
            <v-layout row wrap>
              <v-flex
                @click="pickBox(u)"
                xs3
                md1
                pa-3
                v-for="u in n"
                :key="u.id"
                class="hover"
                :class="{'lighten-3 grey' : chosenUnits.indexOf(u) > -1}"
              >{{ u.display_name }}</v-flex>
            </v-layout>
          </v-window-item>
        </v-window>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <h3 class="subtitle">Gekozen boxen</h3>
      <v-flex xs12 v-for="chosen in chosenUnits" :key="chosen.id">Box: {{ chosen.display_name }}</v-flex>
      <v-flex
        xs12
        v-if="chosenUnits.length === 0"
      >Geen boxen gekozen. Klik een box aan om je keuze te maken</v-flex>
      <v-btn flat color="primary" @click="$emit('done', chosenUnits)">Kiezen</v-btn>
    </v-layout>
  </v-card>
</template>

<style>
.hover:hover {
  cursor: pointer;
  background-color: #333 !important;
  box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2),
    0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
}
</style>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class StepUnit extends Vue {
  @Prop()
  units: any;

  private window: number = 0;
  private chosenUnits: any = [];

  get length() {
    return Object.keys(this.units).length;
  }

  pickBox(unit: any) {
    const index = this.chosenUnits.indexOf(unit);
    if (index > -1) {
      this.chosenUnits.splice(index, 1);
    } else {
      this.chosenUnits.push(unit);
    }
  }
}
</script>
