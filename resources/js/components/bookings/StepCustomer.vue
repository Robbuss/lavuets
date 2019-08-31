<template>
  <v-card flat class="grey lighten-3 pa-1">
    <v-form v-model="valid" lazy-validation ref="form">
      <v-layout row fill-height justify-center align-center pa-5 class="text-xs-center white">
        <v-layout row wrap>
          <v-flex xs12 md8 pr-5>
            <v-container grid-list-md ma-0 pa-0>
              <v-layout wrap class="text-xs-left">
                <v-flex xs12>
                  <h4 class="grey--text headline text-xs-left">Details</h4>
                </v-flex>

                <v-flex xs12>
                  Ik huur ten minste voor een periode van:
                  <v-select
                    v-model="contract.period"
                    :items="items"
                    box
                    label="Kies een duur"
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                  ></v-select>
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
                        v-model="formattedDate"
                        :rules="[
                        v => !!v || 'Dit veld mag niet leeg zijn',
                        v => isInFuture || 'Datum moet in de toekomst liggen!'
                        ]"
                        required
                        label="Startdatum huur"
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
                    :rules="[v => (!!v || wantsDongle) || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="customer.phone"
                    label="Mobiel nummer voor toegang"
                    placeholder="Telefoonnummer"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12>
                  <v-checkbox
                    v-model="wantsDongle"
                    required
                    label="Ik heb geen mobiel nummer en kom een dongle halen"
                  />
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
                  <v-checkbox v-model="isCompany" required label="Ik bestel namens een bedrijf" />
                </v-flex>

                <v-layout row wrap v-if="isCompany">
                  <v-flex xs12>
                    <h4 class="grey--text headline text-xs-left">Bedrijfsgegevens</h4>
                  </v-flex>

                  <v-flex xs12 sm6 md4>
                    <v-text-field
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      box
                      v-model="customer.company_name"
                      label="Bedrijfsnaam"
                    ></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      box
                      v-model="customer.kvk"
                      label="KVK"
                    ></v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md4>
                    <v-text-field
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      box
                      v-model="customer.btw"
                      label="BTW"
                    ></v-text-field>
                  </v-flex>
                </v-layout>
              </v-layout>
            </v-container>
          </v-flex>
          <v-flex xs12 md4>
            <v-card flat class="grey lighten-3">
              <v-toolbar flat class="primary white--text text-xs-center">
                <v-toolbar-title>Overzicht</v-toolbar-title>
              </v-toolbar>
              <v-flex xs12 pa-5>
                <h3 class="subheading grey--text">
                  Je huurt een box in
                  <span class="primary--text">Breukelen</span>
                </h3>
                <h3 class="subheading grey--text">
                  Opslagruimte
                  <span class="primary--text">{{ calculateSize }} M2</span>
                </h3>
                <h3 class="subheading grey--text">
                  Prijs per maand
                  <span class="primary--text">€ {{ calculatePrice }} ,-</span>
                </h3>
                <h3 class="subheading grey--text">
                  Voor de duur van
                  <span
                    class="primary--text"
                  >{{ contract.period || 'Kies een duur' }}</span>
                </h3>
                <h3 class="subheading grey--text">
                  Begin datum
                  <span
                    class="primary--text"
                  >{{ contract.start_date || 'Selecteer datum' }}</span>
                </h3>
              </v-flex>
            </v-card>
            <v-layout row wrap justify-content>
              <v-flex xs12 class="text-xs-center">
                <v-checkbox
                  v-model="terms"
                  :rules="[v => !!v || 'Je moet akkoord gaan met de algemene voorwaarden']"
                  required
                  label="Ik ga akkoord met de algemene voorwaarden"
                />
              </v-flex>
              <v-flex xs12>
                <v-btn
                  :disabled="working"
                  :loading="working"
                  @click="validate"
                  large
                  flat
                  class="primary"
                >Naar betalen</v-btn>
              </v-flex>
            </v-layout>
          </v-flex>
        </v-layout>
      </v-layout>
    </v-form>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import * as moment from "moment";
import "moment/locale/nl";

@Component({})
export default class StepCustomer extends Vue {
  @Prop()
  contract: any;

  @Prop()
  customer: any;

  private valid: boolean = true;
  public items = ["1 maand", "3 maanden", "6 maanden", "12 maanden"];
  private datePicker: boolean = false;
  private terms: boolean = false;
  private isCompany: boolean = false;
  private wantsDongle: boolean = false;
  private working: boolean = false;

  mounted() {
    moment().locale("nl");
    if (!this.contract.start_date)
      this.contract.start_date = moment().toISOString();
  }

  get isInFuture(){
    return moment().format("LL") <= this.formattedDate;
  }

  get formattedDate() {
    return moment(this.contract.start_date).format("LL");
  }

  get calculatePrice() {
    let price = 0;
    this.contract.units.map((x: any) => (price += x.price));
    return price;
  }

  get calculateSize() {
    let size = 0;
    this.contract.units.map((x: any) => (size += x.size));
    return size;
  }

  validate() {
    if ((this.$refs.form as any).validate()) {
      this.$emit("done");
    }
  }
}
</script>
