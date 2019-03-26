<template>
  <v-flex sm12 md8 lg5>
    <v-card>
      <v-toolbar color="primary white--text" class="mb-3">
        <v-toolbar-title>Register</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="$router.push('/login')">
          <v-icon>person</v-icon>
        </v-btn>
      </v-toolbar>
      <v-form class="pa-3" v-model="valid" lazy-validation ref="form">
        <v-text-field
          label="Naam"
          required
          :rules="[v => !!v || 'This field is required.']"
          type="text"
          v-model="user.name"
          placeholder="Will E Coyote"
          prepend-icon="person"
        />
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
        <v-btn
          class="ml-0"
          color="primary"
          @click="validate"
          :disabled="working"
          :loading="working"
        >Register</v-btn>
        <v-alert :value="message" type="warning">{{ message }}</v-alert>
      </v-form>
    </v-card>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Register extends Vue {
  private message = "";
  private valid: boolean = true;
  private working: boolean = false;
  private user: any = {
    name: "",
    email: "",
    password: ""
  };

  validate() {
    if ((<any>this.$refs.form).validate()) {
      this.register();
    }
  }

  async register() {
    try {
      const r = await axios.post("/api/register", this.user);
      if (r.data.access_token) 
        localStorage.setItem("access_token", r.data.access_token);
        this.$router.push("/u");
    } catch (e) {
      console.log(e);
    }
  }
}
</script>
