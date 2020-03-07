<template>
  <div>
    <Booking :domain="domain" v-if="!loading" />
    <v-btn class="btn">Test</v-btn>
    <button class="btn">From webcomponent</button>
  </div>
</template>

<style scoped>
.btn {
  background-color: #333;
}
</style>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import Booking from "./Booking.vue";
// import { VBtn } from "vuetify/lib"

@Component({
  components: {
    Booking
    // VBtn,
  }
})
export default class BookingContainer extends Vue {
  @Prop()
  domain: string;

  private loading: boolean = true;

  async created() {
    let url = this.domain
      ? this.domain + "/api/settings/layout"
      : "/api/settings/layout";
    const r = (await axios.get(url)).data;
    this.$vuetify.theme.themes.light.primary = r.primary_color;
    this.$vuetify.theme.themes.light.secondary = r.secondary_color;
    this.loading = false;
  }
}
</script>
