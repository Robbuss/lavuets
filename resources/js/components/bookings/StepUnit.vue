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
                <v-divider></v-divider>
                <v-list-tile
                  :input-value="active"
                  @click="toggle"
                  :class="{'primary white--text' : unitChecked(n)}"
                >
                  <v-list-tile-content>
                    <v-list-tile-title>{{ n[0].size }} m3</v-list-tile-title>
                  </v-list-tile-content>
                </v-list-tile>
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
    <v-divider></v-divider>
    <v-layout row wrap>
      <v-flex xs12>
        <v-list>
          <v-subheader>Gekozen box(en)</v-subheader>
          <template v-for="chosen in contract.units">
            <v-divider :key="chosen.id + 'd'"></v-divider>
            <v-list-tile :key="chosen.id" avatar>
              <v-list-tile-avatar>
                <v-avatar>
                  <v-img src="/closed_box.png" />
                </v-avatar>
              </v-list-tile-avatar>

              <v-list-tile-content>
                <v-list-tile-title>{{ chosen.name }}</v-list-tile-title>
                <v-list-tile-sub-title>{{ chosen.size }}m3 voor €{{ chosen.price }} per maand</v-list-tile-sub-title>
              </v-list-tile-content>

              <v-list-tile-action @click="pickBox(chosen)">
                <v-btn icon color="grey--text">
                  <v-icon>delete</v-icon>
                </v-btn>
              </v-list-tile-action>

            </v-list-tile>
          </template>

          <v-list-tile v-if="contract.units.length === 0" avatar>
            <v-list-tile-avatar>
              <v-avatar>
                <v-img src="/open_box.png" />
              </v-avatar>
            </v-list-tile-avatar>

            <v-list-tile-content>
              <v-list-tile-title>Geen boxen gekozen.</v-list-tile-title>
              <v-list-tile-sub-title>Klik een box aan om je keuze te maken</v-list-tile-sub-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-flex>
      <v-flex xs12>
        <v-btn flat color="primary" @click="submit">Door naar gegevens invullen</v-btn>
      </v-flex>
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

  @Watch("contract.units")
  onChosenUnitsChanged() {
    this.error ? (this.error = !this.error) : false;
  }

  private window: number = 0;
  private error: boolean = false;

  unitChecked(units: any) {
    for (let i = 0; i < units.length; i++) {
      if (this.contract.units.indexOf(units[i]) > -1) {
        return true;
      }
    }
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
