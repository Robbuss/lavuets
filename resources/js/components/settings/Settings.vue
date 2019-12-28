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
          <v-icon left>group</v-icon>Gebruikers
        </v-tab>
        <v-tab class="justify-start">
          <v-icon left>mdi-mail</v-icon>Templates
        </v-tab>        
        <v-tab-item>
          <v-card flat>
            <v-card-text>
              <v-form>
                <v-text-field
                  v-model="settings.find((x) => x.key === 'app_name').value"
                  outlined
                  label="Applicatie naam"
                  prepend-icon="person"
                />
                <v-text-field
                  v-model="settings.find((x) => x.key === 'mollie_api_key').value"
                  outlined
                  type="password"
                  label="Mollie api key"
                  prepend-icon="lock"
                />
                <v-text-field
                  v-model="settings.find((x) => x.key === 'mollie_webhook_url').value"
                  label="Mollie webhook link"
                  outlined
                  prepend-icon="web"
                />
                <v-text-field
                  v-model="settings.find((x) => x.key === 'primary_color').value"
                  label="Primaire kleur"
                  outlined
                  prepend-icon="invert_colors"
                  persistent-hint
                  hint="Klik op de kleur om een kleur kiezer te openen"
                  :mask="mask"
                  class="mb-4"
                >
                  <template v-slot:prepend>
                    <v-menu
                      v-model="menu"
                      top
                      nudge-bottom="105"
                      nudge-left="16"
                      :close-on-content-click="false"
                    >
                      <template v-slot:activator="{ on }">
                        <div :style="swatchStyle('primary_color')" v-on="on" />
                      </template>
                      <v-card>
                        <v-card-text class="pa-0">
                          <v-color-picker
                            hide-inputs
                            v-model="settings.find((x) => x.key === 'primary_color').value"
                            flat
                          />
                        </v-card-text>
                      </v-card>
                    </v-menu>
                  </template>
                </v-text-field>
                <v-text-field
                  v-model="settings.find((x) => x.key === 'secondary_color').value"
                  label="Primaire kleur"
                  outlined
                  prepend-icon="invert_colors"
                  persistent-hint
                  hint="Klik op de kleur om een kleur kiezer te openen"
                  :mask="mask"
                  class="mb-4"
                >
                  <template v-slot:prepend>
                    <v-menu
                      v-model="menu1"
                      top
                      nudge-bottom="105"
                      nudge-left="16"
                      :close-on-content-click="false"
                    >
                      <template v-slot:activator="{ on }">
                        <div :style="swatchStyle('secondary_color')" v-on="on" />
                      </template>
                      <v-card>
                        <v-card-text class="pa-0">
                          <v-color-picker
                            hide-inputs
                            v-model="settings.find((x) => x.key === 'secondary_color').value"
                            flat
                          />
                        </v-card-text>
                      </v-card>
                    </v-menu>
                  </template>
                </v-text-field>
                <v-file-input label="Upload uw logo" outlined />
                <v-btn
                  @click="update"
                  color="primary"
                  :disabled="loading"
                  :loading="loading"
                >Updaten</v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <Users/>
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
import Users from "./Users"

@Component({
  components:{
    Users
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
        message: "Instellingen aangepast. Ververs de pagina"
      });
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
