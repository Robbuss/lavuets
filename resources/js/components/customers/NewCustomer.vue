<template>
  <v-row wrap justify-center>
    <v-col cols="12" sm="8" md="6" lg="4">
      <v-card class= "pa-4">
        <h3 class="text-center">Welkom</h3>
        <v-row justify-center>
          <v-col shrink>
            <img width="125" src="/logo.png" />
          </v-col>
        </v-row>
        <v-form v-model="valid" lazy-validation ref="form">
          <v-container grid-list-md ma-0 pa-0>
            <v-row wrap justify-center>
              <v-col cols="12">
                <v-text-field
                  filled 
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  v-model="newCustomer.name"
                  label="Naam"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  filled 
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  v-model="newCustomer.company_name"
                  label="Bedrijfsnaam"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  filled 
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  v-model="newCustomer.email"
                  label="E-mail"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  filled 
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  v-model="newCustomer.domain"
                  suffix=".dimonforini.nl"
                  label="Domein naam"
                ></v-text-field>
              </v-col>
              <v-col cols="12" text-center>
                <v-btn
                  :dark="!working"
                  color="primary darken-1"
                  :loading="working"
                  :disabled="working"
                  @click="save"
                >
                  Nu beginnen!
                  <template v-slot:loader>
                    <span class="custom-loader">
                      <v-icon light>cached</v-icon>
                    </span>
                  </template>
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card>
    </v-col>
  </v-row>
</template>
<style>
.custom-loader {
  animation: loader 1s infinite;
  display: flex;
}
@-moz-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
@-webkit-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
@-o-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
@keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class NewCustomer extends Vue {
  private valid: boolean = false;
  private working: boolean = false;
  private newCustomer: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    domain: ""
  };

  async save() {
    if (!(this.$refs.form as any).validate()) return;
    this.working = true;
    try {
      await axios.post("/api/customers/create", this.newCustomer);
    } catch (e) {}
    window.location.href = "http://" + this.newCustomer.domain + ".opslag.dev.v-d-berg.com";
    this.working = false;
  }
}
</script>