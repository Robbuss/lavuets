<template>
  <v-col sm="12">
    <div>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title>Betalingen</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field
          class="white--text pt-0"
          v-model="search"
          append-icon="search"
          label="Zoeken"
          single-line
          hide-details
        ></v-text-field>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="payments"
        :search="search"
        class="elevation-1"
        :loading="loading"
        :sort-by="['created_at']"
        :sort-desc="[true]"
        multi-sort
        @click:row="toggleInfo($event)"
        :footer-props="options"
      >
        <template v-slot:item.amount="{ item }" class="font-weight-bold">€ {{ item.amount }}</template>
        <template v-slot:item.status="{ item }">
          <PaymentStatusChip :payment="item" />
        </template>
        <template v-slot:item.crea  ted_at="{ item }">{{ formatDate(item.created_at) }}</template>
        <template v-slot:item.updated_at="{ item }">{{ formatDate(item.updated_at) }}</template>
        <template v-slot:no-data>
          <td colspan="100%" v-if="loading">Betalingen laden...</td>
          <td colspan="100%" v-else>Geen betalingen</td>
        </template>
      </v-data-table>
    </div>
    <v-dialog v-model="showPaymentInfo" :width="responsiveWidth">
      <v-card v-if="!loadingRelated && showPaymentInfo">
        <v-card-title class="primary white--text">
          <h3 class="card-title">Informatie over deze betaling</h3>
        </v-card-title>
        <v-row wrap>
          <v-col cols="12">
            Betalingen op dit factuurnummer: {{ relatedPayments.length }}
            <v-list>
              <v-list-item v-for="(payment, i) in relatedPayments" :key="i">
                <v-list-item-content>
                  <span class="font-weight-bold">€ {{ payment.amount }}</span>
                </v-list-item-content>
                <v-list-item-content>
                  <PaymentStatusChip :payment="payment" />
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-col>
        </v-row>
        <v-card-actions>
          <v-btn @click="$router.push('/contracts/' + selectedPayment.contract_id)">naar contract</v-btn>
          <v-btn @click="showPaymentInfo = false; selectedPayment = null">sluiten</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-col>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import PaymentStatusChip from "js/components/PaymentStatusChip.vue";
import * as moment from "moment";

@Component({
  components: {
    PaymentStatusChip
  }
})
export default class Payments extends Vue {
  private response = "";
  private payments: any = [];
  private loading: boolean = true;
  private loadingRelated: boolean = false;
  private showPaymentInfo: boolean = false;
  private selectedPayment: any = null;
  private relatedPayments: any = null;
  private search: string | null = null;

  private headers: any = [
    { text: "Bedrag", align: "left", value: "amount" },
    { text: "Status", align: "left", value: "status" },
    { text: "Klant", align: "left", value: "tenant" },
    { text: "Betalings nr", align: "left", value: "payment_id" },
    { text: "Factuur nr", align: "left", value: "invoice_ref_number" },
    { text: "Gemaakt op", value: "created_at" },
    { text: "Gewijzigd op", value: "updated_at" }
  ];

  private options: any = {
    itemsPerPageText: "Betalingen per pagina",
    itemsPerPageAllText: "Allemaal"
  };

  formatDate(date: any) {
    return moment(date).format("LL");
  }

  get responsiveWidth() {
    return this.$vuetify.breakpoint.smAndDown ? "100%" : "60%";
  }

  async toggleInfo(payment: any) {
    this.selectedPayment = payment;
    await this.relatedPaymentsRequest();
    this.showPaymentInfo = true;
  }

  async relatedPaymentsRequest() {
    this.loadingRelated = true;
    this.relatedPayments = (await axios.post(
      "/api/payments/" + this.selectedPayment.id + "/related"
    )).data;
    this.loadingRelated = false;
  }

  async mounted() {
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      this.payments = (await axios.get("/api/payments")).data;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }
}
</script>
