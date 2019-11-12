<template>
  <v-card>
    <v-toolbar flat class="primary" dark>
      <BackButton />
      <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
    </v-toolbar>

    <v-card-text>
      <v-form v-model="valid" lazy-validation ref="form">
        <v-container grid-list-md ma-0 pa-0>
          <v-layout wrap>
            <v-flex xs12 sm6 md4>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.name"
                label="Naam"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6 md4>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.email"
                label="E-mail"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6 md4>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.phone"
                label="Mobiel nummer voor toegang"
                placeholder="Telefoonnummer"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6 md10>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.street_addr"
                label="Straat"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6 md2>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.street_number"
                label="Huisnummer"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.city"
                label="Stad"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6>
              <v-text-field
                box
                :disabled="!editFields"
                :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                required
                v-model="editedItem.postal_code"
                label="Postcode"
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                box
                :disabled="!editFields"
                v-model="editedItem.iban"
                :rules="ibanRules"
                label="IBAN Rekeningnummer"
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-checkbox
                :disabled="!editFields"
                v-model="editedItem.is_company"
                label="Deze klant is een bedrijf"
                hide-details
                class="mt-0"
              />
            </v-flex>
            <v-layout row wrap v-if="editedItem.is_company">
              <v-flex xs12 sm6 md4>
                <v-text-field
                  box
                  :disabled="!editFields"
                  v-model="editedItem.company_name"
                  label="Bedrijfsnaam"
                ></v-text-field>
              </v-flex>
              <v-flex xs12 sm6 md4>
                <v-text-field box :disabled="!editFields" v-model="editedItem.kvk" label="KVK"></v-text-field>
              </v-flex>
              <v-flex xs12 sm6 md4>
                <v-text-field box :disabled="!editFields" v-model="editedItem.btw" label="BTW"></v-text-field>
              </v-flex>
            </v-layout>
          </v-layout>
        </v-container>
      </v-form>
    </v-card-text>

    <v-card-actions>
      <v-btn
        v-if="editFields"
        :dark="!working"
        color="primary darken-1"
        :loading="working"
        :disabled="working"
        @click="save"
      >Opslaan</v-btn>
      <v-btn v-if="!editFields" color="secondary lighten-1" @click="editFields = true">Aanpassen</v-btn>
      <v-btn flat color="primary darken-1" @click="cancel">Annuleren</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import * as iban from "iban";

@Component({})
export default class EditTenant extends Vue {
  @Prop()
  tenant: any;

  @Prop()
  creating: boolean;

  @Prop()
  enableFields: boolean;

  private ibanRules: any = [
    (v: string) => !!v || "Dit veld mag niet leeg zijn",
    (v: string) => !!iban.isValid(v) || "Dit is geen geldig IBAN nummer"
  ];
  private editFields: boolean = false;
  private valid: boolean = null;
  private working: boolean = false;
  private editedItem: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    city: "",
    phone: "",
    street_addr: "",
    street_number: null,
    postal_code: "",
    is_company: false,
    btw: "",
    kvk: "",
    iban: ""
  };
  private defaultItem: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    city: "",
    phone: "",
    street_addr: "",
    street_number: null,
    postal_code: "",
    is_company: false,
    btw: "",
    kvk: "",
    iban: ""
  };
  private response = "";

  get formTitle() {
    return this.creating
      ? "Huurder aanmaken"
      : "De gegevens van " + this.tenant.name + " bewerken";
  }

  mounted() {
    if (this.enableFields) this.editFields = true;
    if (this.tenant) {
      this.editedItem = Object.assign({}, this.tenant);
    }
  }

  editItem(item: any) {
    this.editedItem = Object.assign({}, item);
  }

  cancel() {
    this.$emit("canceled");
  }

  async save() {
    if (!(this.$refs.form as any).validate()) return;
    this.editFields = false;
    this.working = true;
    if (!this.creating) {
      await axios.post("/api/tenants/" + this.editedItem.id, this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Klant aangepast!"
      });
    } else {
      await axios.post("/api/tenants/create", this.editedItem);
      store.commit("snackbar", {
        type: "success",
        message: "Klant aangemaakt!"
      });
    }
    this.$emit("saved");
    this.working = false;
  }
}
</script>
