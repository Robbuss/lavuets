<template>
  <v-flex sm12 md7 lg4>
    <v-card></v-card>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import store from "js/store";

@Component({})
export default class Login extends Vue {
  async mounted() {
    const r = (await axios.post(
      "/api/login/" + this.$route.params.user + "/" + this.$route.params.sso
    )).data;
    store.commit("updateToken", r.access_token);
    this.$router.push("/");
  }
}
</script>
