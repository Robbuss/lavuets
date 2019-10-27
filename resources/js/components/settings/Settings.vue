<template>
<v-layout row wrap>
  <v-flex xs12>
    <v-form>
    <v-text-field v-for="(setting, i) in settings" :key="i"
    :label="setting.key" v-model="setting.value"/>
    <v-btn @click="update" color="primary">Updaten</v-btn>
    </v-form>
  </v-flex>
  <v-flex sm12>
    <div>
      Hier komen algemene instellingen voor een app: 
      - naam
      - logo
      - registratie aan of uit 
      - primary, secondary colors 
      - mollie api key
      - mollie webhook
      - cron jobs instelbaar maken (invoice:due, invoices:send, contract:expired)

      later standaard files voor:
      - algemene voorwaarden
      - huisregels
      - huurcontract

    </div>
  </v-flex>
</v-layout>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Settings extends Vue {
  private response = "";
  private settings: any = [];
  private loading: boolean = true;

  async mounted() {
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      this.settings = (await axios.get("/api/settings")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
  async update() {
    this.loading = true;
    try {
     await axios.post("/api/settings", {settings: this.settings})
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
