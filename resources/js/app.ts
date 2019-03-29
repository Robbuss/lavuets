// <reference path="./node_modules/vuetify/types/lib.d.ts"></reference>
require("@babel/polyfill");
// import { Vue } from './vue';
import Vue from "vue";
(<any>window).Vue = Vue;

import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
Vue.use(Vuetify, {
    theme: {
        primary: "#6786a1",
        secondary: "#3a4d5c",
        accent: "#e1e1e1",
        error: "#db0000"
    }
});
Vue.component('router-component', require('./components/Router.vue').default);

const app = new Vue({
    el: '#app',
});