<template>
  <v-card flat class="grey lighten-3 pa-1" v-if="units">
    <v-layout row wrap class="white">
      <v-flex md2>
        <v-toolbar dense flat color="primary" dark>
          <v-toolbar-title>Grootte</v-toolbar-title>
        </v-toolbar>
        <v-list dense style="border-right: 1px solid #EEEEEE">
          <v-item-group v-model="window" mandatory tag="v-flex">
            <v-item v-for="(n, k) in units" :key="k+'1'">
              <div slot-scope="{ active, toggle }">
                <v-list-tile :input-value="active" @click="toggle">
                  <v-list-tile-content>
                    <v-list-tile-title>{{ n[0].size }} m3</v-list-tile-title>
                  </v-list-tile-content>
                </v-list-tile>
                <v-divider></v-divider>
              </div>
            </v-item>
          </v-item-group>
        </v-list>
      </v-flex>

      <v-flex md10>
        <v-toolbar dense flat color="primary" dark>
          <v-toolbar-title>Klik op een box om te selecteren</v-toolbar-title>
        </v-toolbar>
        <v-window v-model="window" vertical>
          <v-window-item v-for="(n, k) in units" :key="k + '2'">
            <v-layout row wrap>
              <v-flex
                @click="pickBox(u)"
                ma-1
                pa-3
                v-for="u in n"
                :key="u.id"
                class="text-xs-center"
                style="border: 2px solid #EEEEEE; cursor:pointer"
                :class="{'lighten-3 primary' : contract.units.indexOf(u) > -1}"
              >{{ u.display_name }}</v-flex>
            </v-layout>
          </v-window-item>
        </v-window>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <h3 class="subtitle">Gekozen box(en)</h3>
      <v-flex xs12 v-for="chosen in contract.units" :key="chosen.id">Box: {{ chosen.display_name }}</v-flex>
      <v-flex
        xs12
        v-if="contract.units.length === 0"
      >Geen boxen gekozen. Klik een box aan om je keuze te maken</v-flex>
      <v-btn flat color="primary" @click="submit">Kiezen</v-btn>
    </v-layout>
    <v-layout row>
      <v-flex xs12>
        <v-alert :value="error">Je moet een box kiezen om door te gaan</v-alert>
      </v-flex>
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

  @Prop()
  contract: any;

  @Watch('contract.units')
  onChosenUnitsChanged(){
    this.error ? this.error = !this.error : false;
  }

  private window: number = 0;
  private error: boolean = false;

  get length() {
    return Object.keys(this.units).length;
  }

  pickBox(unit: any) {
    const index = this.contract.units.indexOf(unit);
    if (index > -1) {
      this.contract.units.splice(index, 1);
    } else {
      this.contract.units.push(unit);
    }
  }

  submit() {
    if (this.contract.units.length === 0) {
      this.error = true;
      return;
    }
    this.$emit("done", this.contract.units);
  }
}
</script>
