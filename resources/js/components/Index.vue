<template>
  <v-flex sm12 md8 lg5>
    <h4 class="primary--text dijsplay-1">Logged out home page</h4>
    <v-btn @click="$router.push('/login')">Login</v-btn>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import NavItems from "./layout/NavItems.vue";

@Component({})
export default class Index extends Vue {
}
</script>
