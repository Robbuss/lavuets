<template>
  <v-dialog fullscreen hide-overlay :value="value" transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar class="primary white--text">
        <v-toolbar-title>Nieuwe klant aanmaken</v-toolbar-title>
        <v-spacer />
        <v-toolbar-items>
          <v-btn icon @click="$emit('input')" class="white--text">
            <v-icon>close</v-icon>
          </v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-stepper v-model="step" vertical class="flat">
        <v-stepper-step
          :complete="step > 1"
          step="1"
          @click="step > 1 ? step = 1: false"
          style="cursor:pointer"
        >Kies een locatie</v-stepper-step>

        <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="1">
          <StepLocation @done="locationDone($event)"></StepLocation>
        </v-stepper-content>

        <v-stepper-step
          :complete="step > 2"
          step="2"
          @click="step > 2 ? step = 2 : false"
        >Kies het te verhuren product</v-stepper-step>

        <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="2">
          <v-row wrap v-if="step == 2">
            <v-col cols="12" pa-1>
              <StepUnit :location="selectedLocation" :contract="contract" @done="unitDone($event)"></StepUnit>
            </v-col>
          </v-row>
        </v-stepper-content>

        <v-stepper-step :complete="step > 3" step="3">Vul de gegevens van de huurder in</v-stepper-step>

        <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="3">
          <ManualStepTenant
            :location="selectedLocation"
            @done="tenantDone()"
            :working="working"
            :tenant="tenant"
            :contract="contract"
          />
        </v-stepper-content>

        <v-stepper-step :complete="step > 4" step="4">Voeg een betaling toe</v-stepper-step>

        <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="4">
          <ManualStepPayment
            :location="selectedLocation"
            @done="completeOrder($event)"
            :working="working"
            :tenant="tenant"
            :contract="contract"
            :payment="payment"
          />
        </v-stepper-content>
      </v-stepper>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import StepLocation from "js/components/bookings/StepLocation.vue";
import StepUnit from "js/components/bookings/StepUnit.vue";
import ManualStepTenant from "./ManualStepTenant.vue";
import ManualStepPayment from "./ManualStepPayment.vue";
import store from "js/store";

@Component({
  components: {
    StepLocation,
    StepUnit,
    ManualStepTenant,
    ManualStepPayment
  }
})
export default class ManualBooking extends Vue {
  @Prop()
  value: boolean;

  private step: number = 0;
  private count: number = 0;
  private working: boolean = false;
  private selectedLocation: any = {};
  private tenant: any = {
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
  private contract: any = {
    units: [],
    start_date: ""
  };

  private ideal: boolean = false;
  private payment: any = {
    payment_method: "factuur",
    status: "paid",
    payment_id: "",
    price: null
  };

  locationDone(event: string) {
    this.selectedLocation = event;
    this.step = 2;
  }

  unitDone(event: any) {
    this.step = 3;
  }

  tenantDone() {
    this.step = 4;
    let price = 0;
    this.contract.units.map((x: any) => (price += x.price));
    this.payment.price = price;
  }

  async completeOrder() {
    this.working = true;
    try {
      const r = await axios.post("/api/dashboard/manual-tenant", {
        tenant: this.tenant,
        payment: this.payment,
        contract: this.contract,
        units: this.contract.units
      });
      store.commit("snackbar", {
        type: "success",
        message: "Klant, contract en factuur aangemaakt!"
      });
      if (r.data.payment_url && this.ideal)
        // set ideal to true, to enable an ideal checkout link for the customer
        window.location.href = r.data.payment_url;
    } catch (e) {
      console.log("something went wrong");
    }
    this.$emit("input");
    this.working = false;
  }
}
</script>
