// <reference path="./node_modules/vuetify/types/lib.d.ts"></reference>
require("@babel/polyfill");
import Vue from "vue";
import vueCustomElement from 'vue-custom-element'
// import '@webcomponents/webcomponentsjs/webcomponents-bundle.js';

Vue.use(vueCustomElement);
Vue.component("booking-container",
    require("./components/bookings/BookingContainer.vue").default
)
Vue.customElement('vue-app', {
    props: [
        'domain'
    ],
    template: '<booking-container :domain="domain"/>',
}, {
    shadow: true
})


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
//         for (const attr of this.getAttributeNames()) {
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

// window.addEventListener('DOMContentLoaded', (event) => {
//     var elements = document.querySelectorAll(".tenants-form");
//     elements.forEach(function (element) {
//         let domain = element.getAttribute('data-tn-domain')
//         new Vue({
//             el: element,
//             vuetify: new Vuetify(opts),
//             template: '<booking-container domain="' + domain + '"/>'
//         }).$mount(element);
//     });
//     // window.customElements.define('vue-app', TenantElement);
// }); 

