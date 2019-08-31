<template>
  <v-flex>
    <booking-header></booking-header>

    <v-stepper v-model="step" vertical>
      <v-stepper-step
        :complete="step > 1"
        step="1"
        @click="step > 1 ? step = 1: false"
        style="cursor:pointer"
      >
        Kies een locatie
        <small>Waar je een box wilt huren</small>
      </v-stepper-step>

      <v-stepper-content step="1">
        <StepLocation :count="count" @done="locationDone($event)"></StepLocation>
      </v-stepper-content>

      <v-stepper-step :complete="step > 2" step="2" @click="step > 2 ? step = 2 : false">
        Kies de grootte van de box die je wil huren
        <small>Prijs is per maand</small>
      </v-stepper-step>

      <v-stepper-content step="2">
        <v-layout row wrap>
          <v-flex xs12 pa-1>
            <StepUnit :contract="contract" :units="units" @done="unitDone($event)"></StepUnit>
          </v-flex>
        </v-layout>
      </v-stepper-content>

      <v-stepper-step :complete="step > 3" step="3">
        Vul je gegevens in
        <small>Binnen 24 uur kan je de ruimte gebruiken!</small>
      </v-stepper-step>

      <v-stepper-content step="3">
        <StepCustomer @done="completeOrder($event)" :customer="customer" :contract="contract"></StepCustomer>
      </v-stepper-content>
    </v-stepper>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import bookingHeader from "./BookingHeader.vue";
import StepLocation from "./StepLocation.vue";
import StepUnit from "./StepUnit.vue";
import StepCustomer from "./StepCustomer.vue";

@Component({
  components: {
    bookingHeader,
    StepLocation,
    StepUnit,
    StepCustomer
  }
})
export default class Booking extends Vue {
  private step: number = 0;
  private count: number = 0;
  private units: any = [];
  private location: string = "";
  private working: boolean = false;
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
  private contract: any = {
    units: [],
    start_date: "",
    period: ""
  };

  async mounted() {
    const r = (await axios.post("/api/book-data")).data;
    this.units = r.units;
    this.count = r.count;
  }

  locationDone(event: string) {
    this.location = event;
    this.step = 2;
  }

  unitDone(event: any) {
    this.step = 3;
  }

  async completeOrder() {
    this.working = true;
    try {
      const r = await axios.post("/api/booking/create", {
        customer: this.customer,
        contract: this.contract,
        units: this.contract.units
      });
      window.location.href = r.data.redirect_url;
    } catch (e) {
      console.log("something went wrong");
    }
    this.working = false;
  }
}
</script>
