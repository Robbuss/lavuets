<template>
  <v-card flat class="grey lighten-3 pa-1">
    <v-layout row fill-height justify-center align-center pa-5 class="text-xs-center white">
      <v-layout row wrap>
        <v-flex xs12 md8 pr-5>
          <v-form v-model="valid" lazy-validation ref="form">
            <v-container grid-list-md ma-0 pa-0>
              <v-layout wrap class="text-xs-left">
                <v-flex xs12>
                  <h4 class="grey--text headline text-xs-left">Details</h4>
                </v-flex>

                <v-flex xs12>
                  Ik huur ten minste voor een periode van:
                  <v-select :items="items" box label="Kies een duur"></v-select>
                </v-flex>

                <v-flex xs12>
                  Ik wil graag als begin datum:
                  <v-menu
                    ref="datePicker"
                    v-model="datePicker"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    max-width="290px"
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        box
                        v-model="contract.start_date"
                        label="Date"
                        hint="MM/DD/YYYY format"
                        persistent-hint
                        @blur="$emit('formatDate')"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="contract.start_date"
                      no-title
                      @input="datePicker = false"
                    ></v-date-picker>
                  </v-menu>
                </v-flex>

                <v-flex xs12>
                  <h4 class="grey--text headline text-xs-left">Persoonsgegevens</h4>
                </v-flex>
                <v-flex xs12>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.name"
                    label="Naam"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.email"
                    label="E-mail"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.phone"
                    label="Telefoonnummer"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm10>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.street_addr"
                    label="Straat"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md2>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.street_number"
                    label="Huisnummer"
                  ></v-text-field>
                </v-flex>

                <v-flex xs12 sm8>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.city"
                    label="Stad"
                  ></v-text-field>
                </v-flex>

                <v-flex xs12 sm6 md4>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.postal_code"
                    label="Postcode"
                  ></v-text-field>
                </v-flex>

                <v-flex xs12 sm12>
                  <v-text-field
                    box
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.iban"
                    label="IBAN Bankrekeningnummer"
                  ></v-text-field>
                </v-flex>

                <v-flex xs12>
                  <h4 class="grey--text headline text-xs-left">Bedrijfsgegevens</h4>
                </v-flex>

                <v-flex xs12 sm6 md4>
                  <v-text-field box v-model="customer.company_name" label="Bedrijfsnaam"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field box v-model="customer.kvk" label="KVK"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field box v-model="customer.btw" label="BTW"></v-text-field>
                </v-flex>
              </v-layout>
            </v-container>
          </v-form>
        </v-flex>
        <v-flex xs12 md4>
          <v-card flat class="grey lighten-3">
            <v-toolbar flat class="primary white--text text-xs-center">
              <v-toolbar-title>Overzicht</v-toolbar-title>
            </v-toolbar>
            <v-flex xs12 pa-5 v-if="chosenUnits.length === 1">
              <h3 class="subheading grey--text">
                Je huurt een box in
                <span class="primary--text">Breukelen</span>
              </h3>
              <h3 class="subheading grey--text">
                Grootte van de box
                <span class="primary--text">{{ chosenUnits[0].size }} M2</span>
              </h3>
              <h3 class="subheading grey--text">
                Prijs per maand
                <span class="primary--text">€ {{ chosenUnits[0].price }} ,-</span>
              </h3>
              <h3 class="subheading grey--text">
                Begin datum
                <span class="primary--text">{{ startDate || 'Selecteer datum' }}</span>
              </h3>
            </v-flex>
          </v-card>
          <v-layout row wrap justify-content>
            <v-flex xs12 class="text-xs-center">
              <v-checkbox v-model="terms" label="Ik ga akkoord met de algemene voorwaarden" />
            </v-flex>
            <v-flex xs12>
              <v-btn large flat class="primary">Verzenden</v-btn>
            </v-flex>
          </v-layout>
        </v-flex>
      </v-layout>
    </v-layout>
    <v-btn flat color="primary" @click="$emit('done', 'breukelen')">Kiezen</v-btn>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class StepCustomer extends Vue {
  @Prop()
  chosenUnits: any;

  private valid: true;
  private name: "";
  private email: "";
  private select: null;
  public items = ["1 maand", "3 maanden", "6 maanden", "12 maanden"];
  private checkbox: false;
  private startDate: "";
  private datePicker: boolean = false;

  private contract: any = {
    start_date: ""
  };

  private customer: any = {
    id: null,
    name: "",
    company_name: "",
    email: "",
    phone: "",
    city: "",
    street_addr: "",
    street_number: null,
    postal_code: "",
    btw: "",
    kvk: "",
    iban: ""
  };

  validate() {
    if ((this.$refs.form as any).validate()) {
      //
    }
  }

  reset() {
    (this.$refs.form as any).reset();
  }

  resetValidation() {
    (this.$refs.form as any).resetValidation();
  }
}
</script>
