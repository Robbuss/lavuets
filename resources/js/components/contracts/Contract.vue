<template>
  <v-col fluid v-if="!loading" fill-height>
    <v-toolbar class="primary white--text" tabs>
      <BackButton />
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-toolbar-title
            slot="activator"
            style="cursor:pointer;"
            v-on="on"
            @click="$router.push('/tenants/' + contract.tenant.id)"
          >{{ contract.tenant.name }}</v-toolbar-title>
        </template>
        <span>Naar klant informatie</span>
      </v-tooltip>
      <v-spacer></v-spacer>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn icon dark slot="activator" v-on="on" @click="download">
            <v-icon>insert_drive_file</v-icon>
          </v-btn>
        </template>
        <span>Contract downloaden</span>
      </v-tooltip>

      <template v-slot:extension>
        <v-tabs v-model="tab" dark background-color="primary">
          <v-tabs-slider color="secondary"></v-tabs-slider>
          <v-tab v-for="item in items" :key="item.title">{{ item.title }}</v-tab>
        </v-tabs>
      </template>
    </v-toolbar>

    <v-tabs-items v-model="tab">
      <v-tab-item v-for="(item, index) in items" :key="index">
        <component
          :units="units"
          :contract="contract"
          :noPadding="true"
          :hidetoolbar="true"
          v-bind:is="item.component"
        />
      </v-tab-item>
    </v-tabs-items>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import Invoices from "../invoices/Invoices.vue";
import EditCreate from "./EditCreate.vue";
import Information from "./Information.vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import { base64StringToBlob } from "blob-util";
const saveAs: any = require("file-saver");
import * as moment from "moment";
import store from "js/store";

@Component({
  components: {
    invoices: Invoices,
    editCreate: EditCreate,
    information: Information
  }
})
export default class SingleContract extends Vue {
  private response = "";
  private loading: boolean = true;
  private contract: any = null;
  private showWarning: boolean = false;
  private units: any = [];

  private items: any = [
    { title: "Informatie", component: Information },
    { title: "Facturen", component: Invoices }
  ];
  private tab: number = 0;

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async getData() {
    try {
      const r = (await axios.get("/api/contracts/" + this.$route.params.id))
        .data;
      this.contract = r.contract;
      this.units = r.free_units;
    } catch (e) {
      this.response = e.message;
    }
  }

  async download() {
    const pdf = ((await axios.post(
      "/api/contracts/" + this.$route.params.id + "/pdf"
    )) as any).data;
    if (pdf.success === false) {
      store.commit("snackbar", { type: "error", message: pdf.message });
      return;
    }
    var blob = base64StringToBlob(pdf.content, pdf.mime);
    saveAs(blob, "huurcontract." + pdf.extension);
  }
}
</script>
