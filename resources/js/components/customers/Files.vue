<template>
  <v-flex>
    <v-card>
      <v-toolbar flat class="primary" dark>
        <v-toolbar-title>Bestanden van deze klant</v-toolbar-title>
      </v-toolbar>
      <v-list two-line>
        <v-list-tile v-for="(file, i) in files" :key="i" @click="download(file)">
          <v-list-tile-content>{{ file.title }}</v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-card>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import Files from "./Files.vue";
import { base64StringToBlob } from "blob-util";
const saveAs: any = require("file-saver");

@Component({})
export default class SingleCustomer extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private files: any = [];

  @Prop()
  customer: any;

  download(file: any){
    const pdf = file
    var blob = base64StringToBlob(pdf.content, pdf.mime);
    saveAs(blob, this.customer.name + ' ' + file.title);
  }

  async mounted() {
    try {
      this.files = (await axios.post(
        "/api/customers/" + this.$route.params.id + "/files"
      )).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
