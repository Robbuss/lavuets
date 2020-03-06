<template>
  <v-col>
    <booking-header :step="step" v-if="$vuetify.breakpoint.mdAndUp"></booking-header>

    <v-stepper v-model="step" vertical>
      <v-stepper-step
        :complete="step > 1"
        step="1"
        @click="step > 1 ? step = 1: false"
        style="cursor:pointer"
      >
        Waar u een ruimte wilt huren
        <small>Kies een locatie</small>
      </v-stepper-step>

      <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="1">
        <StepLocation :domain="domain" @done="locationDone($event)"></StepLocation>
      </v-stepper-content>

      <v-stepper-step :complete="step > 2" step="2" @click="step > 2 ? step = 2 : false">
        Kies de gewenste grootte van de box
        <small>Prijs is per maand</small>
      </v-stepper-step>

      <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="2">
        <StepUnit
          :domain="domain"
          v-if="step == 2"
          :location="selectedLocation"
          :contract="contract"
          @done="unitDone($event)"
        />
      </v-stepper-content>

      <v-stepper-step :complete="step > 3" step="3">
        Vul de gegevens in
        <small>Binnen 24 uur kunt u de ruimte gebruiken!</small>
      </v-stepper-step>

      <v-stepper-content :class="{'ml-0' : $vuetify.breakpoint.smAndDown}" step="3">
        <StepTenant
          :location="selectedLocation"
          @done="completeOrder($event)"
          :working="working"
          :tenant="tenant"
          :contract="contract"
        ></StepTenant>
      </v-stepper-content>
    </v-stepper>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import bookingHeader from "./BookingHeader.vue";
import StepLocation from "./StepLocation.vue";
import StepUnit from "./StepUnit.vue";
import StepTenant from "./StepTenant.vue";

@Component({
  components: {
    bookingHeader,
    StepLocation,
    StepUnit,
    StepTenant
  }
})
export default class Booking extends Vue {
  @Prop()
  domain: string;

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

  locationDone(event: string) {
    this.selectedLocation = event;
    this.step = 2;
  }

  unitDone(event: any) {
    this.step = 3;
  }

  async completeOrder() {
    this.working = true;
    try {
      let url = this.domain
        ? this.domain + "/api/booking/create"
        : "/api/booking/create";
      const r = await axios.post(url, {
        tenant: this.tenant,
        contract: this.contract,
        units: this.contract.units
      });
      window.location.href = r.data.payment_url;
    } catch (e) {
      console.log("something went wrong");
    }
    this.working = false;
  }
}
</script>
