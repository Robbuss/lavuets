
import Vue from "vue";
import Vuetify from "vuetify/lib";
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
export default new Vuetify(opts)