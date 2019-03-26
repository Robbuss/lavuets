import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

let store = new Vuex.Store({
    state: {
        token: localStorage.getItem('access_token') || null as string
    },
    getters: {
        authenticated: state => {
          return state.token !== null;
        },
    },
    mutations: {
        updateToken: (state, token) => {
          state.token = token
          if(token === null)
            return localStorage.removeItem('access_token')
          localStorage.setItem('access_token', token)
        }
    }
});
export default store;