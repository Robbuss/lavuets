<template>
  <v-flex sm12 md8 lg5>
    <h4 class="display-1">Logged in : -)</h4>
    <v-btn color="primary" @click="test">test api</v-btn>
    {{ response }}
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Dashboard extends Vue {
  private response = "";

  async test() {
    try{
      this.response = (await axios.get("/api/user")).data;
    }catch(e){
      this.response = e.message;
    }
  }
}
</script>
