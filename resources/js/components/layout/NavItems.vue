<template>
  <v-list nav>
    <template v-for="(item, key) in items">
      <v-list-group :prepend-icon="item.icon" :value="false" :key="key" v-if="item.group">
        <template v-slot:activator>
          <v-list-item-title>{{ item.text }}</v-list-item-title>
        </template>
        <v-list-item @click="$router.push(i.link)" v-for="(i, key) in item.links" :key="key">
          <v-list-item-content>
            <v-list-item-title class="grey--text text--darken-2">{{ i.text }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-icon>
            <v-icon>{{i.icon}}</v-icon>
          </v-list-item-icon>
        </v-list-item>
      </v-list-group>
      <v-list-item @click="$router.push(item.link)" :key="key" v-else>
        <v-list-item-action>
          <v-icon class="primary--text">{{ item.icon }}</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          <v-list-item-title class="grey--text text--darken-2">{{ item.text }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </template>
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

  private itemsLoggedIn = [
    {
      icon: "store",
      link: "/locations",
      text: "Locaties"
    },
    {
      group: true,
      icon: "dashboard",
      text: "Boxen",
      links: [
        {
          icon: "view_comfy",
          link: "/units",
          text: "Alle boxen"
        },
        {
          icon: "post_add",
          link: "/boek",
          text: "Boekingsformulier"
        }
      ]
    },
    {
      icon: "account_circle",
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
    }
  ];
  private itemsLoggedOut = [
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
