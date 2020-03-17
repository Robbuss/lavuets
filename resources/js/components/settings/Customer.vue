<template>
  <v-col cols="12" v-if="!loading">
    <v-alert
      border="top"
      prominent
      outlined
      color="secondary"
      icon="mdi-school"
      dismissible
      class="grey--text"
    >
      De gevens die je hier invult worden gebruikt om op jouw facturen te zetten.
      <br />Ook wordt deze informatie gebruik voor de facturering van deze software.
    </v-alert>
    <v-card flat>
      <v-form>
        <v-row>
          <v-col cols="12" class="pl-12">
            <v-hover v-slot:default="{ hover }">
              <ImageUpload :regular="true" v-model="customer.logo">
                <span class="label">Logo</span>
                <v-img class="logo" contain :src="customer.logo">
                  <v-expand-transition>
                    <v-row
                      v-if="hover"
                      align="center"
                      justify="center"
                      class="transition-fast-in-fast-out primary darken-2 white--text fill-height pointer"
                      style="opacity: 0.7"
                    >
                      <v-col cols="12" class="text-center py-0">
                        <v-btn dark icon>
                          <v-icon>add_a_photo</v-icon>
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-expand-transition>
                </v-img>
              </ImageUpload>
            </v-hover>
          </v-col>
        </v-row>
        <v-row wrap>
          <v-col cols="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.company_name"
              label="Naam"
              prepend-icon="store"
              placeholder="ACME inc"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              prepend-icon="phone"
              v-model="customer.phone"
              label="Telefoonnummer"
              placeholder="030 123456"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="10">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.street_addr"
              prepend-icon="place"
              placeholder="Straatnaam"
              label="Straat"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="2">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.street_number"
              label="Huisnummer"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.city"
              label="Stad"
              prepend-icon="place"
              placeholder="Amsterdam"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="12">
            <v-text-field
              outlined
              :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
              required
              v-model="customer.postal_code"
              prepend-icon="place"
              label="Postcode"
              placeholder="1234AB"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              outlined
              prepend-icon="store"
              v-model="customer.kvk"
              label="KVK"
              placeholder="KVK nummer"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field outlined v-model="customer.btw" label="BTW" placeholder="BTW nummer"></v-text-field>
          </v-col>
        </v-row>
        <v-btn
          @click="update"
          color="primary"
          :disabled="loading"
          :loading="loading"
          depressed
        >Opslaan</v-btn>
      </v-form>
    </v-card>
  </v-col>
</template>

<style scoped>
.logo{
  min-height:52px;
  max-height:150px;
  min-width:128px;
  max-width:200px;
}
.label {
  font-size: 0.8em;
  padding-left: 8px;
  line-height: 1;
  min-height: 8px;
  color: rgba(0, 0, 0, 0.6);
}
</style>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import ImageUpload from "js/components/ImageUpload.vue";

@Component({
  components: {
    ImageUpload
  }
})
export default class Customer extends Vue {
  private response = "";
  private loading: boolean = true;
  private customer: any = {
    company_name: "",
    logo: "",
    city: "",
    street_addr: "",
    street_number: "",
    postal_code: "",
    btw: "",
    kvk: ""
  };

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async getData() {
    this.customer = (await axios.get("/api/customers/read")).data;
  }

  async update() {
    try {
      await axios.post("/api/customers/update", this.customer);
      store.commit("snackbar", {
        type: "success",
        message: "Bedrijfsinformatie aangepast."
      });
    } catch (e) {
      this.response = e.message;
    }
  }
}
</script>
