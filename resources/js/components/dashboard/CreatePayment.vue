<template>
  <v-dialog persistent :value="value" :width="responsiveWidth">
    <v-card v-if="invoice">
      <v-card-title class="primary white--text">
        <h3 class="card-title">Betaling aanmaken voor factuur</h3>
      </v-card-title>
      <v-layout row wrap pa-3>
        <v-flex xs12 md8>
          <v-radio-group v-model="createdPayment.payment_method">
            <v-radio
              color="primary"
              value="incasso"
              :disabled="invoice.payment_method === 'factuur'"
              label="Bedrag via incasso afschrijven"
            />
            <v-radio color="primary" value="factuur" label="Handmatig een betaling invoeren" />
          </v-radio-group>
          <v-layout row wrap>
            <v-flex v-if="createdPayment.payment_method === 'factuur'">
              <v-form ref="form" lazy-validation v-model="validate">
                <v-text-field
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  box
                  v-model="createdPayment.price"
                  label="Prijs"
                />
                <v-select
                  box
                  chips
                  required
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  :items="paymentStatuses"
                  v-model="createdPayment.status"
                  label="Status"
                >
                  <template v-slot:item="data">
                    <PaymentStatusChip :payment="{status: data.item.value }" />
                  </template>
                  <template v-slot:selection="data">
                    <PaymentStatusChip :payment="{status: data.item.value }" />
                  </template>
                </v-select>
                <v-text-field
                  :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                  required
                  box
                  v-model="createdPayment.payment_id"
                  label="Payment_id"
                />
              </v-form>
            </v-flex>
            <v-flex v-else>
              <p>Er is een geldig mandaat en een klant in Mollie. Er zal een automatische incasso gestuurd worden naar de klant</p>
              <v-alert
                :value="true"
              >Een bedrag van € {{ invoice.price }} afschrijven van de rekening van de klant</v-alert>
            </v-flex>
          </v-layout>
        </v-flex>
        <v-flex xs12 md4>
          <v-card>
            <v-list>
              <v-list-tile>
                <v-list-tile-content>{{ formatDate(invoice.start_date) }} tot {{ formatDate(invoice.end_date) }}</v-list-tile-content>
              </v-list-tile>
              <v-list-tile>
                <v-list-tile-content>€ {{ invoice.price }}</v-list-tile-content>
              </v-list-tile>
              <v-list-tile>
                <v-list-tile-content>Betaalmethode contract: {{ invoice.payment_method }}</v-list-tile-content>
              </v-list-tile>
              <v-list-tile>
                <v-list-tile-content>factuurnummer: {{ invoice.ref_number }}</v-list-tile-content>
              </v-list-tile>
            </v-list>
            <v-list two-line>
              <v-list-tile v-for="(unit, i) in invoice.units" :key="i">
                <v-list-tile-content>
                  <v-list-tile-title>Overeengekomen prijs: {{ unit.pivot.price }}</v-list-tile-title>
                  <v-list-tile-sub-title>Product: {{ unit.display_name }}</v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
          </v-card>
        </v-flex>
      </v-layout>
      {{ createdPayment }}
      <v-checkbox label="Verstuur gelijk de factuur per mail naar de klant" />
      <v-card-actions>
        <v-btn flat class="primary" @click="createPayment()">
          <span v-if="createdPayment.payment_method ==='incasso'">Bedrag afschrijven via incasso</span>
          <span v-else>Handmatig betaling invoeren</span>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn flat class="secondary" @click="$emit('input')">Sluiten</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";
import * as moment from "moment";
import PaymentStatusChip from "js/components/PaymentStatusChip.vue";

@Component({
  components: {
    PaymentStatusChip
  }
})
export default class CreatePayment extends Vue {
  @Prop()
  invoice: any;

  @Prop()
  value: boolean;

  private paymentStatuses: any = [
    { text: "Verwerken", value: "pending" },
    { text: "Betaald", value: "paid" },
    { text: "Verlopen", value: "expired" },
    { text: "Geannuleerd", value: "canceled" }
  ];

  private createdPayment: any = {
    payment_method: "",
    status: "paid",
    payment_id: "",
    price: null
  };
  private timer: any = null;
  private validate: boolean = false;

  get responsiveWidth() {
    return this.$vuetify.breakpoint.smAndDown ? "100%" : "60%";
  }

  formatDate(date: any) {
    return moment(date).format("LL");
  }

  mounted() {
    if (this.invoice) {
      this.createdPayment.payment_method = this.invoice.payment_method;
      this.createdPayment.price = this.invoice.price;
      this.createdPayment.payment_id = "ss_invoice_" + this.invoice.id;
    }
  }

  destroyed() {
    clearTimeout(this.timer);
  }

  async createPayment() {
    if (this.invoice.payment_method === 'factuur' && !(this.$refs.form as any).validate()) return;
    this.createdPayment = (await axios.post(
      "/api/payments/" +
        this.invoice.contract_id +
        "/" +
        this.invoice.id +
        "/create",
      this.createdPayment
    )).data;
    store.commit("snackbar", { type: "success", message: "Betaling aangemaakt!" });

    this.timer = setTimeout(() => {
      this.$emit("input");
    }, 3000);
  }
}
</script>