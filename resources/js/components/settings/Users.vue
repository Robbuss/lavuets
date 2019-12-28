<template>
  <v-card flat class="pb-6">
    <v-list v-if="!loading">
      <template v-for="(u, i) of users">
        <v-list-item :key="u.id">
          <v-list-item-content>
            <v-list-item-title>{{ u.name }}</v-list-item-title>
            <v-list-item-subtitle>{{ u.email }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-divider v-if="i !== users.length" />
      </template>
    </v-list>
    <v-form ref="form" lazy v-model="valid">
      <v-col sm="12" class="grey--text text--darken-2">Nieuwe gebruiker uitnodigen</v-col>
      <v-col sm="12">
        <v-text-field
          hide-details
          outlined
          :rules="[v => !!v || 'Dit is een verplicht veld']"
          label="Naam van de gebruiker"
          placeholder="Will E Coyote"
          v-model="newUser.name"
        />
      </v-col>
      <v-col sm="12">
        <v-text-field
          hide-details
          :rules="[v => !!v || 'Dit is een verplicht veld']"
          outlined
          placeholder="gebruiker@domein.nl"
          label="E-mailadres"
          v-model="newUser.email"
        />
      </v-col>
      <v-col sm="12">
        <v-btn :working="loading" class="primary" text @click="register">Uitnodigen</v-btn>
      </v-col>
    </v-form>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class Dashboard extends Vue {
  private loading: boolean = true;
  private valid: boolean = false;
  private users: any = [];
  private newUser: any = {
    name: "",
    email: ""
  };

  async register() {
    if ((this.$refs as any).form.validate()) {
      this.loading = true;
      await axios.post("/api/user/create", this.newUser);
      this.getData();
      store.commit("snackbar", {
        type: "success",
        message: "Uitnodiging verstuurt naar: " + this.newUser.email
      });
    }
    this.loading = false;
  }

  async getData() {
    this.users = (await axios.get("/api/user/index")).data;
    this.loading = false;
  }

  async mounted() {
    this.getData();
  }
}
</script>
