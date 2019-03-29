<template>
  <v-flex sm12 md8 lg5>
    <v-card color="primary"> 
      <v-card-title>
        <v-btn dark icon>
          <v-icon>chevron_left</v-icon>
        </v-btn>

        <v-spacer></v-spacer>

        <v-btn dark icon class="mr-3">
          <v-icon>edit</v-icon>
        </v-btn>

        <v-btn dark icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </v-card-title>

      <v-spacer></v-spacer>

      <v-card-title class="white--text">
        <div class="display-1">{{ user.name }}</div>
      </v-card-title>

      <v-list two-line>
        <v-list-tile @click>
          <v-list-tile-action>
            <v-icon color="indigo">phone</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>(650) 555-1234</v-list-tile-title>
            <v-list-tile-sub-title>Mobile</v-list-tile-sub-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile @click>
          <v-list-tile-action></v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>{{ user.email }}</v-list-tile-title>
            <v-list-tile-sub-title>Work</v-list-tile-sub-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-card>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({})
export default class Dashboard extends Vue {
  private response = "";
  private user: any = {
    name: "",
    email: "",
    created_at: ""
  };
  async mounted() {
    try {
      this.user = (await axios.get("/api/user/profile")).data;
    } catch (e) {
      this.response = e.message;
    }
  }
}
</script>
