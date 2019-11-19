import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

let store = new Vuex.Store({
  state: {
    token: localStorage.getItem('access_token') || null as string,
    snackbar: {
      show: false,
      message: "",
      type: "success"
    },
    layout: {} as any
  },
  getters: {
    authenticated: state => {
      return state.token !== null;
    },
  },
  mutations: {
    layout: (state, layout: any) => {
      layout.map((x:any) => state.layout[x.key] = x.value);
    },
    snackbar: (state, snackbar: any) => {
      state.snackbar.message = snackbar.message
      state.snackbar.type = snackbar.type
      state.snackbar.show = true;
    },
    updateToken: (state, token) => {
      state.token = token
      if (token === null)
        return localStorage.removeItem('access_token')
      localStorage.setItem('access_token', token)
    }
  }
});
export default store;