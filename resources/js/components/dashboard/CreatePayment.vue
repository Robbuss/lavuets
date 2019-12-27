<template>
  <v-dialog persistent :value="value" :width="responsiveWidth">
    <v-card v-if="invoice">
      <v-card-title class="primary white--text">
        <h3 class="card-title">Betaling voor factuur nr: {{ invoice.ref_number }}</h3>
      </v-card-title>
      <v-card-text>
        <v-row wrap class="pl-4">
          <v-col cols="12" md="8">
            <v-radio-group v-model="createdPayment.payment_method">
              <v-radio
                color="primary"
                value="incasso"
                :disabled="invoice.payment_method === 'factuur'"
                label="Bedrag via incasso afschrijven"
              />
              <v-radio color="primary" value="factuur" label="Handmatig een betaling invoeren" />
            </v-radio-group>
            <v-row wrap>
              <v-col v-if="createdPayment.payment_method === 'factuur'">
                <v-alert
                  type="info"
                  :value="true"
                >Kies deze optie wanneer een klant de factuur al heeft voldaan, maar de betaling nog niet in dit systeem staat. Zo kun je de factuur op voldaan zetten.</v-alert>
                <v-form ref="form" lazy-validation v-model="validate">
                  <v-text-field
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    required
                    filled
                    v-model="createdPayment.price"
                    label="Prijs"
                  />
                  <v-select
                    chips
                    required
                    :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                    :items="paymentStatuses"
                    v-model="createdPayment.status"
                  >
                    <template v-slot:item="data">
                      <PaymentStatusChip :payment="{status: data.item.value }" />
                    </template>
                    <template v-slot:selection="data">
                      <PaymentStatusChip :payment="{status: data.item.value }" />
                    </template>
                  </v-select>
                  <v-checkbox label="Payment id aanpassen" v-model="manualPaymentId" />
                  <v-expand-transition>
                    <v-text-field
                      v-if="manualPaymentId"
                      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
                      required
                      filled
                      v-model="createdPayment.payment_id"
                      label="Payment_id"
                    />
                  </v-expand-transition>
                </v-form>
              </v-col>
              <v-col v-else>
                <v-alert
                  type="info"
                  :value="true"
                >Het bedrag afschrijven van de rekening van de klant via automatische incasso. Het verwerken van de betaling zal 3 tot 5 werkdagen duren.</v-alert>
                <v-alert
                  type="error"
                  :value="true"
                >Een bedrag van € {{ invoice.price }} afschrijven van de rekening van de klant</v-alert>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" md="4" pl-4>
            <v-card>
              <v-subheader class="text-center primary--text">Samenvatting</v-subheader>
              <v-list dense>
                <v-list-item>
                  <v-list-item-content>Totaalprijs: € {{ invoice.price.toFixed(2) }}</v-list-item-content>
                </v-list-item>
                <v-divider />
                <v-list-item>
                  <v-list-item-content>Periode: {{ formatDate(invoice.start_date) }} - {{ formatDate(invoice.end_date) }}</v-list-item-content>
                </v-list-item>
                <v-divider />
                <v-list-item>
                  <v-list-item-content>Betaalmethode contract: {{ invoice.payment_method }}</v-list-item-content>
                </v-list-item>
                <v-divider />
                <v-list-item>
                  <v-list-item-content>Factuurnummer: {{ invoice.ref_number }}</v-list-item-content>
                </v-list-item>
              </v-list>
              <v-divider />
              <v-list two-line>
                <v-list-item v-for="(unit, i) in invoice.units" :key="i">
                  <v-list-item-content>
                    <v-list-item-title>Overeengekomen prijs: {{ unit.pivot.price }}</v-list-item-title>
                    <v-list-item-subtitle>Product: {{ unit.display_name }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-actions>
        <v-btn text class="primary" @click="createPayment()">
          <span v-if="createdPayment.payment_method ==='incasso'">Bedrag afschrijven via incasso</span>
          <span v-else>Handmatig betaling invoeren</span>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn text class="secondary" @click="$emit('input')">Sluiten</v-btn>
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
  private validate: boolean = false;
  private manualPaymentId: boolean = false;
  private working: boolean = false;

  get responsiveWidth() {
    return this.$vuetify.breakpoint.smAndDown ? "100%" : "60%";
  }

  formatDate(date: any) {
    return moment(date).format("DD-MM-YYYY");
  }

  mounted() {
    if (this.invoice) {
      this.createdPayment.payment_method = this.invoice.payment_method;
      this.createdPayment.price = this.invoice.price;
      this.createdPayment.payment_id = "ss_invoice_" + this.invoice.id;
    }
  }

  async createPayment() {
    this.working = true;
    if (
      this.invoice.payment_method === "factuur" &&
      !(this.$refs.form as any).validate()
    )
      return;
    try {
      const r = (await axios.post(
        "/api/payments/" +
          this.invoice.contract_id +
          "/" +
          this.invoice.id +
          "/create",
        this.createdPayment
      )).data;
      store.commit("snackbar", {
        type: "success",
        message: "Betaling aangemaakt!"
      });
    } catch (e) {
      store.commit("snackbar", {
        type: "error",
        message: e.message
      });
    }

    this.working = false;
    this.$emit("input");
  }
}
</script>