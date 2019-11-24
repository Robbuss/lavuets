<template>
  <v-col sm="12" md="8" lg="5">
    <v-card color="primary"> 
      <v-card-title>
        <v-btn dark icon>
          <v-icon>chevron_left</v-icon>
        </v-btn>

        <v-spacer></v-spacer>

        <v-btn dark icon class= "mr-4">
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
        <v-list-item @click>
          <v-list-item-action>
            <v-icon color="indigo">phone</v-icon>
          </v-list-item-action>

          <v-list-item-content>
            <v-list-item-title>(650) 555-1234</v-list-item-title>
            <v-list-item-subtitle>Mobile</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>

        <v-list-item @click>
          <v-list-item-action></v-list-item-action>

          <v-list-item-content>
            <v-list-item-title>{{ user.email }}</v-list-item-title>
            <v-list-item-subtitle>Work</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card>
  </v-col>
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
