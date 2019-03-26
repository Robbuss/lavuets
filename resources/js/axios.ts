import axiosLib from "axios";
import store from "js/store";

const axios = (<any>window).axios = axiosLib.create({
    headers: {
        "Authorization": "Bearer " + localStorage.getItem("access_token")
    }
});
store.watch((s) => s.token, (token) => axios.defaults.headers["Authorization"] = "Bearer " + token);

export default axios;
