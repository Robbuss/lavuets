<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" clipped fixed app v-if="authenticated">
      <nav-items :authenticated="authenticated" class="pt-4"></nav-items>
      <v-img
        :width="150"
        src="https://www.opslagmagazijn.nl/wp-content/uploads/2018/02/self-storage-breukelen-1-1.png"
      />
    </v-navigation-drawer>
    <v-toolbar app fixed clipped-left class="primary" dark>
      <v-toolbar-side-icon
        color="white--text secondary"
        @click.stop="drawer = !drawer"
        v-if="authenticated"
      ></v-toolbar-side-icon>
      <v-toolbar-title style="cursor: pointer" @click="$router.push('/')">OPSLAGMAGAZIJN</v-toolbar-title>
    </v-toolbar>
    <v-content :class="{ 'bg--image' : !authenticated}">
      <v-container fluid fill-height>
        <v-layout justify-center :align-center="authRoutes">
          <router-view></router-view>
        </v-layout>
      </v-container>
    </v-content>
  </v-app>
</template>
<style>
.bg--image {
  /* background-image: url("/images/your-background-image.jpg"); */
  /* background-position: center center; */
}
</style>

<script lang="ts">
import Vue from "vue";
import Router from "vue-router";
import { Component, Prop, Watch } from "vue-property-decorator";
import Users from "./user/Users.vue";
import Index from "./Index.vue";
import Login from "./auth/Login.vue";
import Customers from "./customers/Customers.vue";
import Customer from "./customers/Customer.vue";
import Logs from "./logs/Logs.vue";
import Units from "./units/Units.vue";
import Unit from "./units/Unit.vue";
import Invoices from "./invoices/Invoices.vue";
import Contracts from "./contracts/Contracts.vue";
import Contract from "./contracts/Contract.vue";
import Register from "./auth/Register.vue";
import Dashboard from "./user/Dashboard.vue";
import NavItems from "./layout/NavItems.vue";
import NotFound from "./errors/404.vue";
import Booking from "./bookings/Booking.vue";
import Store from "js/store";

Vue.use(Router);

const router = new Router({
  mode: "history",
  routes: [
    {
      path: "/",
      component: Index,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/boek",
      component: Booking
    },
    {
      path: "/login",
      component: Login,
      beforeEnter: (to: any, from: any, next: any) =>
        Store.getters.authenticated ? next("/u") : next()
    },
    {
      path: "/register",
      component: Register,
      beforeEnter: (to: any, from: any, next: any) =>
        Store.getters.authenticated ? next("/u") : next()
    },
    {
      path: "/customers",
      component: Customers,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/customers/:id",
      component: Customer,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/units",
      component: Units,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/units/:id",
      component: Unit,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/contracts",
      component: Contracts,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/contracts/:id",
      component: Contract,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/invoices",
      component: Invoices,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/u",
      component: Users,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/logs",
      component: Logs,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/u/profile",
      component: Dashboard,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/logout",
      component: () => {
        Store.commit("updateToken", null);
        router.push("/login");
      }
    },
    {
      path: "*",
      component: NotFound
    }
  ]
});

@Component({
  router: router,
  store: Store,
  components: {
    navItems: NavItems
  }
})
export default class RouterComponent extends Vue {
  private drawer: boolean = true;

  mounted() {}
  get authRoutes() {
    if (
      this.$route.fullPath.startsWith("/login") ||
      this.$route.fullPath.startsWith("/register")
    )
      return true;
    return false;
  }

  get authenticated() {
    return Store.getters.authenticated;
  }
}
</script>
