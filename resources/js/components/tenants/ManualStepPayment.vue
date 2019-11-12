<template>
  <v-form ref="form" lazy-validation v-model="valid">
    <v-text-field
      :rules="[v => !!v || 'Dit veld mag niet leeg zijn']"
      required
      box
      type="number"
      v-model.number="payment.price"
      label="Prijs"
    />
    <v-select
      chips
      required
      :rules="[v => (v.length !== 0) || 'Dit veld mag niet leeg zijn']"
      :items="paymentStatuses"
      v-model="payment.status"
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
        box
        v-model="payment.payment_id"
        label="Payment_id"
      />
    </v-expand-transition>
    <v-btn @click="validate" class="primary ml-0">Aanmaken</v-btn>
  </v-form>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import PaymentStatusChip from "js/components/PaymentStatusChip.vue";

@Component({
  components: {
    PaymentStatusChip
  }
})
export default class StepPayment extends Vue {
  @Prop()
  contract: any;

  @Prop()
  tenant: any;

  @Prop()
  working: boolean;

  @Prop()
  location: any;

  @Prop()
  payment: any;

  private paymentStatuses: any = [
    { text: "Verwerken", value: "pending" },
    { text: "Betaald", value: "paid" },
    { text: "Verlopen", value: "expired" },
    { text: "Geannuleerd", value: "canceled" }
  ];

  private valid: boolean = false;
  private manualPaymentId: boolean = false;

  validate() {
    if (!(this.$refs.form as any).validate()) return;
    this.$emit("done");
  }



  mounted() {
    this.payment.payment_id = "mt_" + Date.now();
  }
}
</script>
