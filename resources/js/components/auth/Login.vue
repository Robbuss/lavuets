<template>
  <v-flex sm12 md8 lg5>
    <v-card>
      <v-toolbar color="primary white--text" class="mb-3">
        <v-toolbar-title>Login</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="$router.push('/register')">
          <v-icon>settings</v-icon>
        </v-btn>
      </v-toolbar>
      <v-form class="pa-3" v-model="valid" lazy-validation ref="form">
        <v-text-field
          label="E-mailadres"
          required
          :rules="[v => !!v || 'This field is required.']"
          type="text"
          v-model="user.email"
          placeholder="willecoyote@acme.mail"
          prepend-icon="mail"
        />
        <v-text-field
          label="Wachtwoord"
          type="password"
          :rules="[v => !!v || 'This field is required.']"
          required
          v-model="user.password"
          placeholder="RoadRunnermustdie"
          @keydown.native.enter="validate"
          prepend-icon="lock"
        />
        <v-btn class="ml-0" color="primary" @click="validate" :disabled="working" :loading="working">Login</v-btn>
        <v-alert
          :value="message"
          type="warning"
        >
          {{ message }}
        </v-alert>
      </v-form>
    </v-card>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class Login extends Vue {
  private message = "";
  private valid: boolean = true;
  private working: boolean = false;
  private user: any = {
    email: "",
    password: ""
  };

  validate() {
    if ((<any>this.$refs.form).validate()) {
      this.login();
    }
  }

  async login() {
    this.working = true;
    try {
      const r = await axios.post("/api/login", this.user);
      if (r.data.error) {
        this.message = r.data.message;
      }
      if (r.data.access_token) {
        store.commit("updateToken", r.data.access_token)
        this.$router.push("/");
      }
    } catch (e) {
      this.message = e;
      console.log(e);
    }
    this.working = false;
  }
}
</script>
