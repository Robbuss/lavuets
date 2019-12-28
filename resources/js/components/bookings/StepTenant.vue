<template>
  <v-card flat class="grey lighten-3 pa-1">
    <v-form v-model="valid" lazy-validation ref="form">
      <v-row
        wrap
        no-gutters
        justify="center"
        align="center"
        :class="{'pa-5' :$vuetify.breakpoint.mdAndUp, 'pa-1': $vuetify.breakpoint.smAndDown}"
        class="text-center white fill-height"
      >
        <v-row wrap>
          <v-col cols="12" md="8" :class="{'pr-5' :$vuetify.breakpoint.mdAndUp}">
            <v-container grid-list-md ma-0 pa-0>
              <v-row wrap class="text-xs-left">
                <v-col cols="12">
                  <h4 class="grey--text headline text-xs-left">Details</h4>
                </v-col>

                <v-col cols="12" v-if="false">
                  Ik huur ten minste voor een periode van:
                  <v-select
                    v-model="contract.period"
                    :items="items"
                    filled
                    label="Kies een duur"
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    :persistent-hint="contract.period ? true : false"
                    :hint="'Het totaalbedrag voor deze periode zal elke ' + contract.period + ' in rekening worden gebracht.'"
                    required
                  ></v-select>
                </v-col>

                <v-col cols="12">
                  Ik wil graag als begin datum:
                  <v-menu
                    ref="datePicker"
                    v-model="datePicker"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        filled
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
                </v-col>

                <v-col cols="12">
                  <h4 class="grey--text headline text-xs-left">Persoonsgegevens</h4>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    filled
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="tenant.name"
                    label="Naam"
                    autocomplete="name"
                    name="name"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    filled
                    :rules="[
                    v => !!v || 'Dit veld mag niet leeg zijn',
                    v => /.+@.+/.test(v) || 'Geef een geldig e-mailadres op']"
                    required
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="tenant.email"
                    label="E-mail"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    filled
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="tenant.phone"
                    label="Mobiel nummer voor toegang"
                    placeholder="Telefoonnummer"
                    type="tel"
                    name="phone"
                    autocomplete="tel"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="10">
                  <v-text-field
                    filled
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="tenant.street_addr"
                    label="Straat"
                    autocomplete="shipping street-address"
                    name="ship-address"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                  <v-text-field
                    filled
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="tenant.street_number"
                    label="Huisnummer"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="8">
                  <v-text-field
                    filled
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="tenant.city"
                    label="Woonplaats"
                    autocomplete="shipping locality"
                    name="ship-city"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6" md="4">
                  <v-text-field
                    filled
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    v-model="tenant.postal_code"
                    label="Postcode"
                    autocomplete="shipping postal-code"
                    name="ship-zip"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="12">
                  <v-text-field
                    filled
                    :rules="ibanRules"
                    required
                    v-model="tenant.iban"
                    label="IBAN Bankrekeningnummer"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-checkbox v-model="isCompany" required label="Ik bestel namens een bedrijf" />
                </v-col>

                <v-row wrap v-if="isCompany">
                  <v-col cols="12">
                    <h4 class="grey--text headline text-xs-left">Bedrijfsgegevens</h4>
                  </v-col>

                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      id="company"
                      filled
                      v-model="tenant.company_name"
                      label="Bedrijfsnaam"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      filled
                      v-model="tenant.kvk"
                      label="KVK"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      filled
                      v-model="tenant.btw"
                      label="BTW"
                    ></v-text-field>
                  </v-col>
                  <v-row wrap>
                    <v-col cols="12">
                      <p>Voor bedrijven wordt 21% BTW in rekening gebracht. De BTW bedraagt: €{{ calculateVatAmount }}. Het totaal bedrag komt daarmee op: €{{ totalIncludingVat }}.</p>
                    </v-col>
                  </v-row>
                </v-row>
              </v-row>
            </v-container>
          </v-col>
          <v-col cols="12" md="4">
            <v-card flat class="grey lighten-3">
              <v-toolbar flat class="primary white--text text-center">
                <v-toolbar-title>Overzicht</v-toolbar-title>
              </v-toolbar>
              <v-col
                cols="12"
                :class="{'pa-5' :$vuetify.breakpoint.mdAndUp, 'pa-2' : $vuetify.breakpoint.smAndDown}"
              >
                <h3 class="subtitle-1 grey--text">
                  U huurt een box in
                  <span class="primary--text">{{ location.facility_name }}</span>
                </h3>
                <h3 class="subtitle-1 grey--text">
                  Opslagruimte
                  <span class="primary--text">{{ calculateSize }} M3</span>
                </h3>
                <h3 class="subtitle-1 grey--text">
                  Prijs per maand
                  <span
                    class="primary--text"
                    v-if="!isCompany"
                  >€ {{ calculatePrice }} ,-</span>
                  <span class="primary--text" v-if="isCompany">€ {{ totalIncludingVat }} ,-</span>
                </h3>
                <h3 class="subtitle-1 grey--text" v-if="false">
                  Voor de duur van
                  <span
                    class="primary--text"
                  >{{ contract.period || 'Kies een duur' }}</span>
                </h3>
                <h3 class="subtitle-1 grey--text">
                  Begin datum
                  <span class="primary--text">{{ formattedDate || 'Selecteer datum' }}</span>
                </h3>
              </v-col>
            </v-card>
            <v-row wrap justify-content>
              <v-col cols="12" class="text-center">
                <v-checkbox
                  v-model="terms"
                  :rules="[v => !!v || 'Je moet akkoord gaan met de algemene voorwaarden']"
                  required
                  label="Ik ga akkoord met de algemene voorwaarden en de automatische afschrijving van vervolg betalingen"
                />
              </v-col>
              <v-col cols="12">
                <v-btn
                  :disabled="working"
                  :loading="working"
                  @click="validate"
                  large
                  text
                  class="primary"
                >Naar betalen</v-btn>
              </v-col>
              <v-col class="justify-content">
                <img width="100px" src="logo.png" />
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-row>
    </v-form>
  </v-card>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import * as moment from "moment";
import "moment/locale/nl";
import * as iban from "iban";

@Component({})
export default class StepTenant extends Vue {
  @Prop()
  contract: any;

  @Prop()
  tenant: any;

  @Prop()
  working: boolean;

  @Prop()
  location: any;

  @Watch("contract.start_date")
  onStartDateChanged(newval: any, oldval: any) {
    if (oldval) (this.$refs.form as any).resetValidation();
  }

  private ibanRules: any = [
    (v: string) => !!v || "Dit veld mag niet leeg zijn",
    (v: string) => !!iban.isValid(v) || "Dit is geen geldig IBAN nummer"
  ];

  private valid: boolean = true;
  public items = ["1 maand", "3 maanden", "6 maanden", "12 maanden"];
  private datePicker: boolean = false;
  private terms: boolean = false;
  private isCompany: boolean = false;

  mounted() {
    moment().locale("nl");
    if (!this.contract.start_date)
      this.contract.start_date = moment().format("YYYY-MM-DD");
  }

  get isInFuture() {
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

  get calculateVatAmount() {
    return (this.calculatePrice * 0.21).toFixed(2);
  }

  get totalIncludingVat() {
    return (this.calculatePrice * 1.21).toFixed(2);
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
