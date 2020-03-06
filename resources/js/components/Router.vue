<template>
  <v-app v-if="!loading">
    <v-navigation-drawer
      v-model="drawer"
      clipped
      fixed
      app
      v-if="authenticated && !authRoutes && !registerRoute"
    >
      <NavItems :authenticated="authenticated" class="pt-6" />
    </v-navigation-drawer>
    <v-app-bar
      app
      fixed
      clipped-left
      class="primary"
      dark
      v-if="authenticated && !authRoutes && !registerRoute"
    >
      <v-app-bar-nav-icon class="white--text secondary" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
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
            </v-list-item>
            <v-list-item @click="$router.push('/settings')">
              <v-list-item-action>
                <v-icon>settings</v-icon>
              </v-list-item-action>
              <v-list-item-title>Instellingen</v-list-item-title>
            </v-list-item>
            <v-list-item @click="$router.push('/logs')">
              <v-list-item-action>
                <v-icon>description</v-icon>
              </v-list-item-action>
              <v-list-item-title>Systeemlog</v-list-item-title>
            </v-list-item>
            <v-list-item @click="$router.push('/logout')">
              <v-list-item-action>
                <v-icon>exit_to_app</v-icon>
              </v-list-item-action>
              <v-list-item-title>Uitloggen</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-toolbar-items>
    </v-app-bar>
    <v-content :class="{ 'background' : registerRoute || authRoutes}" :style="svgbg">
      <v-container
        fluid
        :class="{'fill-height justify-center' : authRoutes || registerRoute }"
      >
        <v-row justify="center" :align-center="authRoutes || registerRoute">
          <router-view></router-view>
        </v-row>
      </v-container>
      <v-snackbar v-model="snackbar.show" :color="snackbar.type">{{ snackbar.message }}</v-snackbar>
    </v-content>
  </v-app>
</template>
<style scoped>
.background {
  background-color: #ffffff;
  background-attachment: fixed;
  background-size: cover;
  background-image: var(--svgbg);
}
</style>
<script lang="ts">
// import "@babel/polyfill";
import Vue from "vue";
import axios from "js/axios";
import Router from "vue-router";
import { Component, Prop, Watch } from "vue-property-decorator";
import Index from "./dashboard/Index.vue";
import Login from "./auth/Login.vue";
import ResetPassword from "./auth/ResetPassword.vue";
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
import NotFound from "./errors/404.vue";
import Booking from "./bookings/Booking.vue";
import Store from "js/store";
import NewCustomer from "./customers/NewCustomer.vue";
import NavItems from "./layout/NavItems.vue";

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
      path: "/reset-password",
      component: ResetPassword
    },    
    {
      path: "/login/:user/:sso",
      component: SingleSignOn
    },
    {
      path: "/login",
      component: Login,
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
    NavItems
  }
})
export default class RouterComponent extends Vue {
  private drawer: boolean = true;
  private loading: boolean = true;

  get svgbg() {
    if (!this.authRoutes && !this.registerRoute) return;
    let svg =
      "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25'%3E%3Cdefs%3E%3ClinearGradient id='a' gradientUnits='userSpaceOnUse' x1='0' x2='0' y1='0' y2='100%25' gradientTransform='rotate(240)'%3E%3Cstop offset='0' stop-color='%23ffffff'/%3E%3Cstop offset='1' stop-color='%23REPLACECOLOR'/%3E%3C/linearGradient%3E%3Cpattern patternUnits='userSpaceOnUse' id='b' width='540' height='450' x='0' y='0' viewBox='0 0 1080 900'%3E%3Cg fill-opacity='0.1'%3E%3Cpolygon fill='%23444' points='90 150 0 300 180 300'/%3E%3Cpolygon points='90 150 180 0 0 0'/%3E%3Cpolygon fill='%23AAA' points='270 150 360 0 180 0'/%3E%3Cpolygon fill='%23DDD' points='450 150 360 300 540 300'/%3E%3Cpolygon fill='%23999' points='450 150 540 0 360 0'/%3E%3Cpolygon points='630 150 540 300 720 300'/%3E%3Cpolygon fill='%23DDD' points='630 150 720 0 540 0'/%3E%3Cpolygon fill='%23444' points='810 150 720 300 900 300'/%3E%3Cpolygon fill='%23FFF' points='810 150 900 0 720 0'/%3E%3Cpolygon fill='%23DDD' points='990 150 900 300 1080 300'/%3E%3Cpolygon fill='%23444' points='990 150 1080 0 900 0'/%3E%3Cpolygon fill='%23DDD' points='90 450 0 600 180 600'/%3E%3Cpolygon points='90 450 180 300 0 300'/%3E%3Cpolygon fill='%23666' points='270 450 180 600 360 600'/%3E%3Cpolygon fill='%23AAA' points='270 450 360 300 180 300'/%3E%3Cpolygon fill='%23DDD' points='450 450 360 600 540 600'/%3E%3Cpolygon fill='%23999' points='450 450 540 300 360 300'/%3E%3Cpolygon fill='%23999' points='630 450 540 600 720 600'/%3E%3Cpolygon fill='%23FFF' points='630 450 720 300 540 300'/%3E%3Cpolygon points='810 450 720 600 900 600'/%3E%3Cpolygon fill='%23DDD' points='810 450 900 300 720 300'/%3E%3Cpolygon fill='%23AAA' points='990 450 900 600 1080 600'/%3E%3Cpolygon fill='%23444' points='990 450 1080 300 900 300'/%3E%3Cpolygon fill='%23222' points='90 750 0 900 180 900'/%3E%3Cpolygon points='270 750 180 900 360 900'/%3E%3Cpolygon fill='%23DDD' points='270 750 360 600 180 600'/%3E%3Cpolygon points='450 750 540 600 360 600'/%3E%3Cpolygon points='630 750 540 900 720 900'/%3E%3Cpolygon fill='%23444' points='630 750 720 600 540 600'/%3E%3Cpolygon fill='%23AAA' points='810 750 720 900 900 900'/%3E%3Cpolygon fill='%23666' points='810 750 900 600 720 600'/%3E%3Cpolygon fill='%23999' points='990 750 900 900 1080 900'/%3E%3Cpolygon fill='%23999' points='180 0 90 150 270 150'/%3E%3Cpolygon fill='%23444' points='360 0 270 150 450 150'/%3E%3Cpolygon fill='%23FFF' points='540 0 450 150 630 150'/%3E%3Cpolygon points='900 0 810 150 990 150'/%3E%3Cpolygon fill='%23222' points='0 300 -90 450 90 450'/%3E%3Cpolygon fill='%23FFF' points='0 300 90 150 -90 150'/%3E%3Cpolygon fill='%23FFF' points='180 300 90 450 270 450'/%3E%3Cpolygon fill='%23666' points='180 300 270 150 90 150'/%3E%3Cpolygon fill='%23222' points='360 300 270 450 450 450'/%3E%3Cpolygon fill='%23FFF' points='360 300 450 150 270 150'/%3E%3Cpolygon fill='%23444' points='540 300 450 450 630 450'/%3E%3Cpolygon fill='%23222' points='540 300 630 150 450 150'/%3E%3Cpolygon fill='%23AAA' points='720 300 630 450 810 450'/%3E%3Cpolygon fill='%23666' points='720 300 810 150 630 150'/%3E%3Cpolygon fill='%23FFF' points='900 300 810 450 990 450'/%3E%3Cpolygon fill='%23999' points='900 300 990 150 810 150'/%3E%3Cpolygon points='0 600 -90 750 90 750'/%3E%3Cpolygon fill='%23666' points='0 600 90 450 -90 450'/%3E%3Cpolygon fill='%23AAA' points='180 600 90 750 270 750'/%3E%3Cpolygon fill='%23444' points='180 600 270 450 90 450'/%3E%3Cpolygon fill='%23444' points='360 600 270 750 450 750'/%3E%3Cpolygon fill='%23999' points='360 600 450 450 270 450'/%3E%3Cpolygon fill='%23666' points='540 600 630 450 450 450'/%3E%3Cpolygon fill='%23222' points='720 600 630 750 810 750'/%3E%3Cpolygon fill='%23FFF' points='900 600 810 750 990 750'/%3E%3Cpolygon fill='%23222' points='900 600 990 450 810 450'/%3E%3Cpolygon fill='%23DDD' points='0 900 90 750 -90 750'/%3E%3Cpolygon fill='%23444' points='180 900 270 750 90 750'/%3E%3Cpolygon fill='%23FFF' points='360 900 450 750 270 750'/%3E%3Cpolygon fill='%23AAA' points='540 900 630 750 450 750'/%3E%3Cpolygon fill='%23FFF' points='720 900 810 750 630 750'/%3E%3Cpolygon fill='%23222' points='900 900 990 750 810 750'/%3E%3Cpolygon fill='%23222' points='1080 300 990 450 1170 450'/%3E%3Cpolygon fill='%23FFF' points='1080 300 1170 150 990 150'/%3E%3Cpolygon points='1080 600 990 750 1170 750'/%3E%3Cpolygon fill='%23666' points='1080 600 1170 450 990 450'/%3E%3Cpolygon fill='%23DDD' points='1080 900 1170 750 990 750'/%3E%3C/g%3E%3C/pattern%3E%3C/defs%3E%3Crect x='0' y='0' fill='url(%23a)' width='100%25' height='100%25'/%3E%3Crect x='0' y='0' fill='url(%23b)' width='100%25' height='100%25'/%3E%3C/svg%3E";
    let primary = Store.state.layout.primary_color.replace("#", "");
    svg = svg.replace("REPLACECOLOR", primary);
    return { backgroundImage: 'url("' + svg + '")' };
  }

  async created() {
    const r = (await axios.get("/api/settings/layout")).data;
    Store.commit("layout", r);
    this.$vuetify.theme.themes.light.primary = Store.state.layout.primary_color;
    this.$vuetify.theme.themes.light.secondary =
      Store.state.layout.secondary_color;
    this.loading = false;
  }
  get registerRoute() {
    return this.$route.fullPath.startsWith("/registreer-verhuurder");
  }

  get authRoutes() {
    return this.$route.fullPath.startsWith("/login") || this.$route.fullPath.startsWith("/reset-password");
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
