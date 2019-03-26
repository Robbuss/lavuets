<template>
  <v-list dense>
    <v-list-tile @click="$router.push(item.link)" v-for="(item, key) in items" :key="key">
      <v-list-tile-action>
        <v-icon>{{ item.icon }}</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ item.text }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
  </v-list>
</template>
    
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import Store from "js/store"

@Component({})
export default class NavItems extends Vue {
  @Prop()
  private authenticated: boolean;

  get items() {
    if (this.authenticated) return this.itemsLoggedIn;
    return this.itemsLoggedOut;
  }

  private itemsLoggedIn: { [k: string]: string }[] = [
    {
      icon: "settings",
      link: "/u",
      text: "User"
    },
    {
      icon: "exit_to_app",
      link: "/logout",
      text: "Logout"
    }
  ];
  private itemsLoggedOut: { [k: string]: string }[] = [
    {
      icon: "lock",
      link: "/login",
      text: "Login"
    },
    {
      icon: "person",
      link: "/register",
      text: "Register"
    }
  ];
}
</script>
