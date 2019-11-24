// <reference path="./node_modules/vuetify/types/lib.d.ts"></reference>
require("@babel/polyfill");
import Vue from "vue";
import Vuetify from "vuetify/lib";
import "@mdi/font/css/materialdesignicons.css";
const opts = {
    theme: {
        themes: {
            light: {
                primary: "#6786a1",
                secondary: "#3a4d5c",
                accent: "#e1e1e1",
                error: "#db0000"
            }
        }
    }
};

Vue.use(Vuetify);
Vue.component("router-component", require("./components/Router.vue").default);
Vue.component(
    "booking-form",
    require("./components/bookings/Booking.vue").default
);
Vue.component("BackButton", require("js/components/BackButton.vue").default);
new Vue({
    vuetify: new Vuetify(opts)
}).$mount("#vue-app");

// import '@webcomponents/webcomponentsjs/webcomponents-bundle.js';

// // Application components
// const ce = class extends HTMLElement {
//     private initialized = false;

//     constructor() {
//         super();
//     }

//     connectedCallback() {
//         if (this.initialized) {
//             return;
//         }
//         this.initialized = true;

//         const vueDiv = document.createElement("div");
//         const element = document.createElement(this.getAttribute('type') || 'div');
//         for(const attr of this.getAttributeNames()) {
//             element.setAttribute(attr, this.getAttribute(attr) || "");
//         }
//         vueDiv.appendChild(element);
//         const shadowRoot = this;
//         shadowRoot.appendChild(vueDiv);

//         (async () => {
//             new Vue().$mount(vueDiv);
//         })();
//     }
// };
// window.customElements.define('vue-app', ce);
