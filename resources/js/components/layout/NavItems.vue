<template>
  <v-list nav>
    <v-list-item @click="$router.push(item.link)" v-for="(item, key) in items" :key="key">
      <v-list-item-action>
        <v-icon class="primary--text">{{ item.icon }}</v-icon>
      </v-list-item-action>
      <v-list-item-content>
        <v-list-item-title class="grey--text text--darken-2">{{ item.text }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
  </v-list>
</template>
    
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import Store from "js/store";

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
      icon: "store",
      link: "/locations",
      text: "Locaties"
    },    
    {
      icon: "view_quilt",
      link: "/units",
      text: "Boxen / producten"
    },
    {
      icon: "person",
      link: "/tenants",
      text: "Huurders"
    },
    {
      icon: "description",
      link: "/contracts",
      text: "Contracten"
    },
    {
      icon: "account_balance_wallet",
      link: "/invoices",
      text: "Facturen"
    },
    {
      icon: "attach_money",
      link: "/payments",
      text: "Betalingen"
    },
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
