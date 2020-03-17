<template>
  <v-col cols="12" v-if="!loading">
    <v-card>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Applicatie instellingen</v-toolbar-title>
      </v-toolbar>
      <v-tabs vertical>
        <v-tab class="justify-start">
          <v-icon left>mdi-account</v-icon>Basisisntellingen
        </v-tab>
        <v-tab class="justify-start">
          <v-icon left>store</v-icon>Bedrijfsinformatie
        </v-tab>
        <v-tab class="justify-start">
          <v-icon left>mdi-mail</v-icon>Boekingsformulier
        </v-tab>
        <v-tab class="justify-start">
          <v-icon left>group</v-icon>Gebruikers
        </v-tab>
        <v-tab class="justify-start">
          <v-icon left>mdi-mail</v-icon>Templates
        </v-tab>
        <v-tab-item>
          <Default :settings="settings" @update="update" :loading="loading"/>
        </v-tab-item>
        <v-tab-item>
          <Customer />
        </v-tab-item>
        <v-tab-item>
          <Booking :settings="settings" @update="update"/>
        </v-tab-item>
        <v-tab-item>
          <Users />
        </v-tab-item>
        <v-tab-item>
          <Templates />
        </v-tab-item>
      </v-tabs>
    </v-card>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import Users from "./Users.vue";
import Templates from "./Templates.vue";
import Default from "./Default.vue";
import Customer from "./Customer.vue";
import Booking from "./Booking.vue";

@Component({
  components: {
    Users,
    Default,
    Templates,
    Customer,
    Booking
  }
})
export default class Settings extends Vue {
  private response = "";
  private settings: any = [];
  private loading: boolean = true;

  private color = "#1976D2FF";
  private mask = "!#XXXXXXXX";
  private menu = false;
  private menu1 = false;

  async mounted() {
    await this.getData();
  }

  swatchStyle(key: string) {
    return {
      backgroundColor: this.settings.find((x: any) => x.key === key).value,
      cursor: "pointer",
      height: "24px",
      width: "24px",
      borderRadius: "50%"
    };
  }

  @Watch("settings", { deep: true })
  onSettingsChanged() {
    console.log("a");
    this.$vuetify.theme.themes.light.primary = this.settings.find(
      (x: any) => x.key === "primary_color"
    ).value;
    this.$vuetify.theme.themes.light.secondary = this.settings.find(
      (x: any) => x.key === "secondary_color"
    ).value;
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
      await axios.post("/api/settings", { settings: this.settings });
      store.commit("snackbar", {
        type: "success",
        message: "Instellingen aangepast. Pagina wordt herladen"
      });
      setTimeout(() => {
        window.location.reload(true); 
      },1000)
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
