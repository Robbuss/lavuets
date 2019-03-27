<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" clipped fixed app v-if="authenticated">
      <v-list dense>
        <nav-items :authenticated="authenticated"></nav-items>
      </v-list>
    </v-navigation-drawer>
    <v-toolbar app fixed clipped-left>
      <v-toolbar-side-icon @click.stop="drawer = !drawer" v-if="authenticated"></v-toolbar-side-icon>
      <v-toolbar-title>Application</v-toolbar-title>
    </v-toolbar>
    <v-content :class="{ 'bg--image' : !authenticated}">
      <v-container fluid fill-height>
        <v-layout justify-center align-center>
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
import Index from "./Index.vue";
import Login from "./auth/Login.vue";
import Register from "./auth/Register.vue";
import Dashboard from "./user/Dashboard.vue";
import NavItems from "./layout/NavItems.vue";
import NotFound from "./errors/404.vue";
import Store from "js/store";

Vue.use(Router);

const router = new Router({
  mode: "history",
  routes: [
    {
      path: "/",
      component: Index
    },
    {
      path: "/login",
      component: Login,
      beforeEnter: (to: any, from: any, next: any) => (Store.getters.authenticated) ? next("/u") : next()
    },
    {
      path: "/register",
      component: Register,
      beforeEnter: (to: any, from: any, next: any) => (Store.getters.authenticated) ? next("/u") : next()
    },
    {
      path: "/u",
      component: Dashboard,
      beforeEnter: (to: any, from: any, next: any) => (!Store.getters.authenticated) ? next("/login") : next()
    },
    {
      path: "/logout",
      component: () => {
        Store.commit("updateToken", null)
        router.push("/");
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
  components:{
    navItems: NavItems
  }
})
export default class RouterComponent extends Vue {
  private drawer: boolean = true;

  get authenticated() {
    return Store.getters.authenticated;
  }
}
</script>
