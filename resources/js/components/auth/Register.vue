<template>
  <v-col sm="12" md="8" lg="5">
    <v-card>
      <v-toolbar color="primary white--text" class= "mb-4">
        <v-toolbar-title>Register</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="$router.push('/login')">
          <v-icon>person</v-icon>
        </v-btn>
      </v-toolbar>
      <v-row wrap v-if="erroredFields">
        <v-col cols="12" v-for="(field, i) in erroredFields" :key="i"  px-4>
          <v-alert :value="true" type="error" :key="i">{{ erroredFields[i] }}</v-alert>
        </v-col>
      </v-row>
      <v-form class= "pa-4" v-model="valid" lazy-validation ref="form">
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
          
          color="primary"
          @click="validate"
          :disabled="working"
          :loading="working"
        >Registreren</v-btn>
      </v-form>
    </v-card>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class Register extends Vue {
  private erroredFields: { [k: string]: string[] } = {};
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
    this.working = true;
    try {
      const r = await axios.post("/api/register", this.user);
      if (r.data.access_token){
        store.commit("updateToken", r.data.access_token.access_token)
        this.$router.push("/u");
      }
      if(r.data.errors){
        this.erroredFields[0] = r.data.errors;
      }
    } catch (e) {
      this.erroredFields = e.response.data.errors;
    }
    this.working = false;
  }
}
</script>
