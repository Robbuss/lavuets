<template>
  <v-row wrap>
    <v-col cols="12">
      <v-expansion-panels v-model="panel">
        <v-expansion-panel v-for="template in templates" :key="template">
          <v-expansion-panel-header>{{template}}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field outlined label="Mail onderwerp" placeholder="Welkom" />
            <VueEditor v-model="content" />
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-col>
    <v-col cols="12">
      <v-btn color="primary" depressed dark>Opslaan</v-btn>
    </v-col>
  </v-row>
</template>
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import { VueEditor } from "vue2-editor";

@Component({
  components: {
    VueEditor
  }
})
export default class Templates extends Vue {
  private content: string = "";
  private panel: number = 0;
  private templates: string[] = ["bookingcomplete", "sendinvoice", "contract"];

  mounted() {}
}
</script>
  