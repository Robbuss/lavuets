<template>
  <v-card>
    <v-card-title flat dense class="primary--text">
      <h3>Openstaande facturen</h3>
    </v-card-title>
    <v-row wrap v-if="unpaid.length > 0">
      <v-col>
        <v-data-table
          :footer-props="options"
          :items-per-page="10"
          @click:row="createPayment($event)"
          :headers="headers"
          :items="unpaid"
          :loading="loading"
          multi-sort
          :sort-by="['ref_number']"
          :sort-desc="[true]"
        >
          <template v-slot:item.payments="{ item }">
            <PaymentStatusChip :payment="item.payments[0]" />
          </template>
          <template
            v-slot:item.price="{ item }"
          >€ {{ item.price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,") }}</template>
          <template v-slot:item.start_date="{ item }">{{ formatDate(item.start_date, 'LL') }}</template>
          <template v-slot:item.end_date="{ item }">{{ formatDate(item.end_date, 'LL') }}</template>
          <template v-slot:item.sent="{ item }">
            <v-tooltip bottom v-if="item.sent">
              <template v-slot:activator="{ on }">
                <v-icon v-on="on" class="grey--text text-lighten-1" small slot="activator">mail</v-icon>
              </template>
              <span>{{ formatDate(item.sent, 'LLL') }}</span>
            </v-tooltip>
            <v-tooltip bottom v-else>
              <template v-slot:activator="{ on }">
              <v-icon v-on="on" class="primary--text" small slot="activator">mail</v-icon>
              </template>
              <span>Niet verzonden</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on }">
                <v-btn
                  slot="activator"
                  icon
                  v-on="on"
                  small
                  class="grey--text text-lighten-1"
                  @click="$router.push('/contracts/' + item.contract_id)"
                >
                  <v-icon small>send</v-icon>
                </v-btn>
              </template>
              <span>Naar het contract</span>
            </v-tooltip>
          </template>
          <template v-slot:no-data>
            <td colspan="100%" v-if="loading">Facturen laden...</td>
            <td colspan="100%" @click="$emit('generate')" v-else>Geen facturen gevonden</td>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <v-row wrap align-center justify="center" v-if="unpaid.length === 0">
      <v-col class="shrink">
        <img src="/all-set.png" />
      </v-col>
      <v-col class="grow">
        <h3 class="text-lighten-1 inline">Geen onbetaalde facturen</h3>
        <p class="grey--text text-darken-3">Whoop whoop! Money in the bank.</p>
      </v-col>
    </v-row>
    <CreatePayment
      v-model="createPaymentModal"
      :invoice="selectedInvoice"
      @input="getData"
      :key="selectedInvoice ? selectedInvoice.id : '1'"
    />
  </v-card>
</template>
<style scoped>
tr {
  cursor: pointer !important;
}
</style>
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import CreatePayment from "./CreatePayment.vue";
import PaymentStatusChip from "js/components/PaymentStatusChip.vue";
import * as moment from "moment";

@Component({
  components: {
    CreatePayment,
    PaymentStatusChip
  }
})
export default class UnsedInvoice extends Vue {
  private unpaid: any = [];
  private selectedInvoice: any = null;
  private createPaymentModal: boolean = false;
  private loading: boolean = true;
  private options: any = {
    itemsPerPageText: "Facturen per pagina",
    itemsPerPageAllText: "Allemaal"
  };
  private headers: any = [
    { text: "Factuur nr", value: "ref_number" },
    { text: "Bedrag", value: "price" },
    { text: "Betaling", value: "payments" },
    { text: "Naam", value: "tenant.name" },
    { text: "Betaalmethode", value: "payment_method" },
    { text: "Van", value: "start_date" },
    { text: "Tot", value: "end_date" },
    { text: "Verzonden", value: "sent" }
  ];

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async getData() {
    this.unpaid = (await axios.get("/api/dashboard/unpaid-invoices")).data;
  }

  formatDate(date: any) {
    return moment(date).format("LL");
  }

  createPayment(invoice: any) {
    this.selectedInvoice = invoice;
    this.createPaymentModal = true;
  }
}
</script>