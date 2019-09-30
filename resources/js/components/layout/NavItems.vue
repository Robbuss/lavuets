<template>
  <v-list>
    <v-list-tile @click="$router.push(item.link)" v-for="(item, key) in items" :key="key">
      <v-list-tile-action>
        <v-icon class="primary--text">{{ item.icon }}</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title class="grey--text text--darken-2">{{ item.text }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
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
      link: "/units",
      text: "Boxes"
    },
    {
      icon: "person",
      link: "/customers",
      text: "Klanten"
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
    {
      icon: "description",
      link: "/logs",
      text: "Logs"
    },
    {
      icon: "folder",
      link: "/files",
      text: "Bestanden"
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
