\<template>
  <v-app v-if="!loading">
    <v-navigation-drawer v-model="drawer" clipped fixed app v-if="authenticated">
      <nav-items :authenticated="authenticated" class="pt-4"></nav-items>
    </v-navigation-drawer>
    <v-toolbar app fixed clipped-left class="primary" dark>
      <v-toolbar-side-icon
        color="white--text secondary"
        @click.stop="drawer = !drawer"
        v-if="authenticated"
      ></v-toolbar-side-icon>
      <v-toolbar-title style="cursor: pointer" @click="$router.push('/')">{{ appName }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items v-if="authenticated">
        <v-menu offset-y bottom left open-on-hover>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on" :class="{'mt-0' : $vuetify.breakpoint.mdAndUp}">
              <v-avatar
                :tile="false"
                :size="$vuetify.breakpoint.smAndDown ? 32 : 42"
                color="grey lighten-4"
              >
                <img src="/avatar.png" alt="avatar" />
              </v-avatar>
            </v-btn>
          </template>
          <v-list>
            <v-list-tile @click="$router.push('/u/profile')">
              <v-list-tile-action>
                <v-icon>person</v-icon>
              </v-list-tile-action>
              <v-list-tile-title>Mijn profiel</v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="$router.push('/u')">
              <v-list-tile-action>
                <v-icon>people</v-icon>
              </v-list-tile-action>
              <v-list-tile-title>Alle gebruikers</v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="$router.push('/settings')">
              <v-list-tile-action>
                <v-icon>settings</v-icon>
              </v-list-tile-action>
              <v-list-tile-title>Instellingen</v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="$router.push('/logs')">
              <v-list-tile-action>
                <v-icon>description</v-icon>
              </v-list-tile-action>
              <v-list-tile-title>Systeemlog</v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="$router.push('/logout')">
              <v-list-tile-action>
                <v-icon>exit_to_app</v-icon>
              </v-list-tile-action>
              <v-list-tile-title>Uitloggen</v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-menu>
      </v-toolbar-items>
    </v-toolbar>
    <v-content>
      <v-container
        fluid
        fill-height
        :class="{'pa-0' : $vuetify.breakpoint.smAndDown}"
      >
        <v-layout justify-center :align-center="authRoutes">
          <router-view></router-view>
        </v-layout>
      </v-container>
      <v-snackbar v-model="snackbar.show" :color="snackbar.type">{{ snackbar.message }}</v-snackbar>
    </v-content>
  </v-app>
</template>
<style scoped>
.background {
  background-color: #ff11fb;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1600 800'%3E%3Cg stroke='%23000' stroke-width='100' stroke-opacity='0.05' %3E%3Ccircle fill='%23ff11fb' cx='0' cy='0' r='1800'/%3E%3Ccircle fill='%23fa00e1' cx='0' cy='0' r='1700'/%3E%3Ccircle fill='%23f200c9' cx='0' cy='0' r='1600'/%3E%3Ccircle fill='%23e900b2' cx='0' cy='0' r='1500'/%3E%3Ccircle fill='%23de009c' cx='0' cy='0' r='1400'/%3E%3Ccircle fill='%23d20088' cx='0' cy='0' r='1300'/%3E%3Ccircle fill='%23c40076' cx='0' cy='0' r='1200'/%3E%3Ccircle fill='%23b60065' cx='0' cy='0' r='1100'/%3E%3Ccircle fill='%23a70055' cx='0' cy='0' r='1000'/%3E%3Ccircle fill='%23980047' cx='0' cy='0' r='900'/%3E%3Ccircle fill='%2389003b' cx='0' cy='0' r='800'/%3E%3Ccircle fill='%23790030' cx='0' cy='0' r='700'/%3E%3Ccircle fill='%236a0626' cx='0' cy='0' r='600'/%3E%3Ccircle fill='%235a0b1e' cx='0' cy='0' r='500'/%3E%3Ccircle fill='%234c0d17' cx='0' cy='0' r='400'/%3E%3Ccircle fill='%233d0d10' cx='0' cy='0' r='300'/%3E%3Ccircle fill='%23300c07' cx='0' cy='0' r='200'/%3E%3Ccircle fill='%23240800' cx='0' cy='0' r='100'/%3E%3C/g%3E%3C/svg%3E");
  background-attachment: fixed;
  background-size: cover;
}
</style>
<script lang="ts">
// import "@babel/polyfill";
import Vue from "vue";
import axios from "js/axios";
import Router from "vue-router";
import { Component, Prop, Watch } from "vue-property-decorator";
import Users from "./user/Users.vue";
import Index from "./dashboard/Index.vue";
import Login from "./auth/Login.vue";
import SingleSignOn from "./auth/SingleSignOn.vue";
import Tenants from "./tenants/Tenants.vue";
import Tenant from "./tenants/Tenant.vue";
import Settings from "./settings/Settings.vue";
import Logs from "./logs/Logs.vue";
import Locations from "./locations/Locations.vue";
import Payments from "./payments/Payments.vue";
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
import NewCustomer from "./customers/NewCustomer.vue";

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
      path: "/registreer-verhuurder",
      component: NewCustomer
    },
    {
      path: "/:user/:sso",
      component: SingleSignOn
    },
    {
      path: "/login",
      component: Login,
      beforeEnter: (to: any, from: any, next: any) =>
        Store.getters.authenticated ? next("/") : next()
    },
    {
      path: "/register",
      component: Register,
      beforeEnter: (to: any, from: any, next: any) =>
        Store.getters.authenticated ? next("/") : next()
    },
    {
      path: "/tenants",
      component: Tenants,
      beforeEnter: (to: any, from: any, next: any) =>
        !Store.getters.authenticated ? next("/login") : next()
    },
    {
      path: "/tenants/:id",
      component: Tenant,
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
      path: "/payments",
      component: Payments,
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
      path: "/locations",
      component: Locations,
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
      path: "/settings",
      component: Settings,
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
  private loading: boolean = true;

  async created() {
    const r = (await axios.get("/api/settings/layout")).data;
    Store.commit("layout", r);
    this.$vuetify.theme.primary = Store.state.layout.primary_color;
    this.$vuetify.theme.secondary = Store.state.layout.secondary_color;
    this.loading = false;
  }

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

  get snackbar() {
    return Store.state.snackbar;
  }

  get appName() {
    return Store.state.layout.app_name;
  }

  @Watch("snackbar.show")
  onSnackbarChanged(newval: boolean, oldval: boolean) {
    if (oldval !== undefined) {
      let t = setTimeout(
        () => Store.commit("snackbar", { show: false, message: "" }),
        3500
      );
      clearTimeout(t);
    }
  }
}
</script>
