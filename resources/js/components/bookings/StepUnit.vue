<template>
  <v-card flat class="grey lighten-3 pa-1" v-if="units">
    <v-card-text>
      <v-row wrap>
        <v-col md="2" :class="{ 'pr-0' : $vuetify.breakpoint.mdAndUp}">
          <v-toolbar dense flat color="primary" dark>
            <v-toolbar-title>Grootte</v-toolbar-title>
          </v-toolbar>
          <v-list dense style="border-right: 1px solid #EEEEEE">
            <v-item-group v-model="window" mandatory tag="v-col">
              <v-item v-for="(n, k) in units" :key="k+'1'">
                <div slot-scope="{ active, toggle }">
                  <v-divider></v-divider>
                  <v-list-item
                    :input-value="active"
                    @click="toggle"
                    :class="{'primary white--text' : unitChecked(n)}"
                  >
                    <v-list-item-content>
                      <v-list-item-title>{{ n[0].size }} m3</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </div>
              </v-item>
            </v-item-group>
          </v-list>
        </v-col>

        <v-col md="10" :class="{ 'pl-0' : $vuetify.breakpoint.mdAndUp}">
          <v-toolbar dense flat color="primary" dark>
            <v-toolbar-title>Klik op een box om deze te selecteren</v-toolbar-title>
          </v-toolbar>
          <v-window v-model="window" vertical>
            <v-window-item v-for="(n, k) in units" :key="k + '2'">
              <v-row wrap class="mx-0">
                <v-col
                  @click="pickfilled (u)"
                  v-for="u in n"
                  :key="u.id"
                  class="ma-1 pa-4 text-center white"
                  style="border: 2px solid #EEEEEE; cursor:pointer"
                  :class="{'lighten-3 primary' : contract.units.indexOf(u) > -1}"
                >
                  <span class="font-weight-bold">{{ u.name }}</span>
                  <br />
                  {{ u.size }}m3
                  <br />

                  €{{ u.price}}
                </v-col>
              </v-row>
            </v-window-item>
          </v-window>
        </v-col>
      </v-row>
      <v-row wrap>
        <v-col cols="12">
          <v-list>
            <v-subheader>Gekozen box(en)</v-subheader>
            <template v-for="chosen in contract.units">
              <v-divider :key="chosen.id + 'd'"></v-divider>
              <v-list-item :key="chosen.id">
                <v-list-item-avatar>
                  <v-avatar>
                    <v-img src="/closed_box.png" />
                  </v-avatar>
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title>{{ chosen.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ chosen.size }}m3 voor €{{ chosen.price }} per maand</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action @click="pickfilled (chosen)">
                  <v-btn icon color="grey--text">
                    <v-icon>delete</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </template>

            <v-list-item v-if="contract.units.length === 0">
              <v-list-item-avatar>
                <v-avatar>
                  <v-img src="/open_box.png" />
                </v-avatar>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>Geen box en gekozen.</v-list-item-title>
                <v-list-item-subtitle>Klik een box aan om je keuze te maken</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
      <v-row v-if="error">
        <v-col cols="12">
          <v-alert type="warning" :value="error">Je moet een box kiezen om door te gaan</v-alert>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-btn text color="primary" @click="submit">Door naar gegevens invullen</v-btn>
    </v-card-actions>
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
  contract: any;

  @Prop()
  location: any;

  @Watch("contract.units")
  onChosenUnitsChanged() {
    this.error ? (this.error = !this.error) : false;
  }

  private units: any = [];
  private window: number = 0;
  private error: boolean = false;
  private loading: boolean = true;

  async mounted() {
    this.units = (await axios.post("/api/book-data/units", {
      location_id: this.location.id
    })).data;
    this.loading = false;
  }

  unitChecked(units: any) {
    for (let i = 0; i < units.length; i++) {
      if (this.contract.units.indexOf(units[i]) > -1) {
        return true;
      }
    }
  }

  pickfilled(unit: any) {
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
