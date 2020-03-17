<template>
  <v-card flat class="pa-1" v-if="units">
    <v-row wrap no-gutters>
      <v-col md="2" :class="{ 'pr-0' : $vuetify.breakpoint.mdAndUp}">
        <v-toolbar dense flat color="primary" dark>
          <v-toolbar-title>Grootte</v-toolbar-title>
          <v-spacer />
          <v-radio-group row hide-details v-model="unitOfMeasurement">
            <v-radio value="m3" label="m3" />
            <v-radio value="m2" label="m2" />
          </v-radio-group>
        </v-toolbar>
        <v-list dense style="border-right: 1px solid #EEEEEE">
          <v-item-group v-model="window" mandatory tag="v-col">
            <v-item v-for="(n, k) in units[unitOfMeasurement]" :key="k+'1'">
              <div slot-scope="{ active, toggle }">
                <v-divider></v-divider>
                <v-list-item
                  :input-value="active"
                  @click="toggle"
                  :class="{ 'primary white--text' : groupContainsUnit(n)}"
                >
                  <v-list-item-content>
                    <v-list-item-title>{{ n[0]['size_' + unitOfMeasurement] }} {{ unitOfMeasurement}}</v-list-item-title>
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
          <v-window-item v-for="(category, k) in units[unitOfMeasurement]" :key="k + '2'">
            <v-row wrap :class="{'pl-3' : $vuetify.breakpoint.mdAndUp}">
              <v-col sm="12" md="6" lg="4" v-for="u in getFirstUnits(category, k)" :key="u.id">
                <UnitCard :unit="u" @picked="pickBox($event)" :checked="unitChecked(u)" :fill="true"/>
              </v-col>
              <v-col sm="12" class="text-center">
                <v-btn text @click="showAll.push(k)" v-if="!showAll.includes(k) && category.length > 3">Laat meer zien</v-btn>
              </v-col>
            </v-row>
          </v-window-item>
        </v-window>
      </v-col>
    </v-row>
    <v-divider />
    <v-row wrap no-gutters>
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
                <v-list-item-subtitle>{{ chosen.size_m3 }}m3 voor €{{ chosen.price }} per maand</v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-action @click="pickBox (chosen)">
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
    <v-row v-if="error" no-gutters>
      <v-col cols="12">
        <v-alert color="primary" dark :value="error">Je moet een box kiezen om door te gaan</v-alert>
      </v-col>
    </v-row>
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
import UnitCard from "js/components/units/UnitCard.vue"

@Component({
  components:{
    UnitCard
  }
})
export default class StepUnit extends Vue {
  @Prop()
  contract: any;

  @Prop()
  location: any;

  @Prop()
  domain: string;

  @Watch("contract.units")
  onChosenUnitsChanged() {
    this.error ? (this.error = !this.error) : false;
  }

  @Watch('unitOfMeasurement')
  onChanged(){
    this.window = 0;
  }

  private unitOfMeasurement: "m3" | "m2" = "m3";
  private showAll: number[] = [];

  private units: any = [];
  private window: number = 0;
  private error: boolean = false;
  private loading: boolean = true;

  async mounted() {
    let url = this.domain
      ? this.domain + "/api/book-data/units"
      : "/api/book-data/units";
    this.units = (
      await axios.post(url, {
        location_id: this.location.id
      })
    ).data;
    this.loading = false;
  }

  getFirstUnits(category: any, key: number){
    if(this.showAll.includes(key))
      return category;
    return category.slice(0, 3);
  }

  unitChecked(unit: any) {
    for (let i = 0; i < this.contract.units.length; i++) {
      if (this.contract.units[i].id === unit.id) return true;
    }
    return false;
  }

  groupContainsUnit(units: any) {
    for (let i = 0; i < units.length; i++) {
      if (this.unitChecked(units[i])) return true;
    }
    return false;
  }

  pickBox(unit: any) {
    for (var i = 0; i < this.contract.units.length; i++) {
      if (this.contract.units[i].id === unit.id)
        return this.contract.units.splice(i, 1);
    }
    return this.contract.units.push(unit);
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
