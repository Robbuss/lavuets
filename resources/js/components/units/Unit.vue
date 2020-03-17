<template>
  <v-col v-if="!loading" fill-height class="py-0">
    <v-row wrap>
      <v-col cols="12" md="6" lg="8">
        <EditCreateUnit :unit="unit" :creating="false" />
      </v-col>
      <v-col cols="12" md="6" lg="4">
        <v-row class="v-toolbar__title" style="height: 64px;" align="center">
          <v-col class="primary--text font-weight-bold">Preview</v-col>
        </v-row>

        <UnitCard :unit="unit" :key="rerender"/>
        <v-row>
          <v-col cols="12">
            <History :unit="unit" />
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import * as moment from "moment";
import EditCreateUnit from "./EditCreate.vue";
import UnitCard from "./UnitCard.vue";
import History from "./History.vue";

@Component({
  components: {
    EditCreateUnit,
    UnitCard,
    History
  }
})
export default class SingleUnit extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private unit: any = {};
  private rerender: number = 0;

  formatDate(date: any) {
    return moment(date).format("LL");
  }

  async mounted() {
    try {
      this.unit = (await axios.get("/api/units/" + this.$route.params.id)).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
