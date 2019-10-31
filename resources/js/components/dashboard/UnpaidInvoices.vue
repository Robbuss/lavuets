<template>
  <v-card>
    <v-card-title flat dense class="primary--text">
      <h3>Openstaande facturen</h3>
    </v-card-title>
    <v-layout row wrap v-if="unpaid.length > 0">
      <v-flex>
        <v-data-table
          :headers="headers"
          :items="unpaid"
          :loading="loading"
          rows-per-page-text="Facturen per pagina"
          :pagination.sync="pagination"
        >
          <template v-slot:items="props">
            <tr @click="createPayment(props.item)" class="pointer">
              <td>{{ props.item.ref_number }}</td>
              <td>€ {{ props.item.price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,") }}</td>
              <td>
                <PaymentStatusChip :payment="props.item.payments[0]" />
              </td>
              <td>{{ props.item.tenant.name }}</td>
              <td>{{ props.item.payment_method }}</td>
              <td>{{ formatDate(props.item.start_date, 'LL') }}</td>
              <td>{{ formatDate(props.item.end_date, 'LL') }}</td>
              <td>
                <v-tooltip bottom v-if="props.item.sent">
                  <v-icon class="grey--text text-lighten-1" small slot="activator">mail</v-icon>
                  <span>{{ formatDate(props.item.sent, 'LLL') }}</span>
                </v-tooltip>
                <v-tooltip bottom v-else>
                  <v-icon class="primary--text" small slot="activator">mail</v-icon>
                  <span>Niet verzonden</span>
                </v-tooltip>
                <v-tooltip bottom>
                  <v-btn
                    slot="activator"
                    icon
                    small
                    class="grey--text text-lighten-1"
                    @click="$router.push('/contracts/' + props.item.contract_id)"
                  >
                    <v-icon small>send</v-icon>
                  </v-btn>
                  <span>Naar het contract</span>
                </v-tooltip>
              </td>
            </tr>
          </template>
          <template v-slot:no-data>
            <td colspan="100%" v-if="loading">Facturen laden...</td>
            <td colspan="100%" @click="$emit('generate')" v-else>Geen facturen gevonden</td>
          </template>
        </v-data-table>
      </v-flex>
    </v-layout>
    <v-layout row wrap align-center justify-center v-if="unpaid.length === 0">
      <v-flex shrink>
        <img src="/all-set.png" />
      </v-flex>
      <v-flex grow>
        <h3 class="text-lighten-1 inline">Geen onbetaalde facturen</h3>
        <p class="grey--text text-darken-3">Whoop whoop! Money in the bank.</p>
      </v-flex>
    </v-layout>
    <CreatePayment
      v-model="createPaymentModal"
      :invoice="selectedInvoice"
      @input="getData"
      :key="selectedInvoice ? selectedInvoice.id : '1'"
    />
  </v-card>
</template>

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
  private pagination: any = {
    rowsPerPage: 10,
    descending: true,
    sortBy: "ref_number"
  };
  private headers: any = [
    { text: "Factuur nr", value: "ref_number" },
    { text: "Prijs", value: "price" },
    { text: "Betaling", value: "payment.payment_id" },
    { text: "Naam", value: "tenant.name" },
    { text: "Betaalmethode", value: "payment_method" },
    { text: "Van", value: "start_date" },
    { text: "Tot", value: "end_date" },
    { text: "Verzonden", value: "sent" },
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