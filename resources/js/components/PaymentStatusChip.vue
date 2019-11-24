<template>
  <v-chip flat dark class=" lighten-2 text--darken-1" :class="hasPayment.color">
    <v-avatar>
      <v-icon>{{ hasPayment.icon }}</v-icon>
    </v-avatar>
    {{ hasPayment.text }}
  </v-chip>
</template>

<script lang="ts">
import Vue from 'vue'
import { Component, Prop } from 'vue-property-decorator'

@Component({})
export default class PaymentStatusChip extends Vue {
  @Prop()
  payment: any;

  get hasPayment () {
    return this.payment
      ? this.status[this.payment.status]
      : {
        text: 'Onbekend',
        icon: 'info',
        color: 'grey'
      }
  }

  private status: any = {
    paid: {
      text: 'Betaald',
      icon: 'check_circle',
      color: 'green'
    },
    expired: {
      text: 'Verlopen',
      icon: 'schedule',
      color: 'orange'
    },
    canceled: {
      text: 'Geannuleerd',
      icon: 'error',
      color: 'red'
    },
    pending: {
      text: 'Verwerken',
      icon: 'slow_motion_video',
      color: 'info'
    }
  };
}
</script>
