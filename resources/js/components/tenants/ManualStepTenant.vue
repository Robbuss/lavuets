<template>
  <v-card flat class="grey lighten-3 pa-1">
    <v-form v-model="valid" lazy-validation ref="form">
      <v-row
        row
        fill-height
        justify-center
        align-center
        :class="{'pa-5' :$vuetify.breakpoint.mdAndUp, 'pa-1': $vuetify.breakpoint.smAndDown}"
        class="text-center white"
      >
        <v-row wrap>
          <v-col cols="12" md="8" :class="{'pr-5' :$vuetify.breakpoint.mdAndUp}">
            <v-container grid-list-md ma-0 pa-0>
              <v-row wrap class="text-xs-left">
                <v-col cols="12">
                  <h4 class="grey--text headline text-xs-left">Details</h4>
                </v-col>

                <v-col cols="12">
                  Begindatum van het contract
                  <v-menu
                    ref="datePicker"
                    v-model="datePicker"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    lazy
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
                  <v-checkbox  v-model="isCompany" required label="Ik bestel namens een bedrijf" />
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
                <v-row wrap>
                  <v-col cols="12">
                    <v-btn class="primary" @click="validate">Volgende</v-btn>
                  </v-col>
                </v-row>
              </v-row>
            </v-container>
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

  get formattedDate() {
    return moment(this.contract.start_date).format("LL");
  }

  validate() {
    if ((this.$refs.form as any).validate()) {
      this.$emit("done");
    }
  }
}
</script>
