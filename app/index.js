import Vue from "vue";
import Vuex from "vuex";
import VueRouter from "vue-router";
import App from "./index.vue";
import axios from "axios";
import vueSlider from "vue-slider-component";
import VRuntimeTemplate from "v-runtime-template";
import storePlugin from "./utils/storePlugin";
import { i18n } from "./utils/i18n";
import router from './router'

Vue.prototype.$axios = axios;
Vue.prototype.$cache = {};

// configuration base (data)
Vue.prototype.$configbase = document.body.dataset.configbase || false

// load info
Vue.prototype.$info = JSON.parse(
  document.getElementById("bootload-info").innerText
);

// load config
Vue.prototype.$config = JSON.parse(
  document.getElementById("bootload-config").innerText
);

// load tests
Vue.prototype.$tests = JSON.parse(
  document.getElementById("bootload-tests").innerText
);

Vue.component("VueSlider", vueSlider);
Vue.component("Vrt", VRuntimeTemplate);

Vue.component("Mdc", {
  template: "#markdowncontact",
});

// Vue.directive('bgimg', bgimg)
// Vue.component('loadimg', loadimg)

// import all components
const components = require.context("./components", true, /\.vue$/);
components.keys().forEach((key) => {
  let newkey = key.replace(/(\.\/|\.vue)/g, "");
  newkey = newkey.replace(/\//g, "-");
  Vue.component(newkey, components(key).default);
});

// import all directives
const directives = require.context("./directives", true, /\.vue$/);
directives.keys().forEach((key) => {
  let newkey = key.replace(/(\.\/|\.vue)/g, "");
  Vue.directive(newkey, directives(key).default);
});

Vue.use(VueRouter);

// stores
Vue.use(Vuex);

const allstores = ["profile", "tests", "extra", "func", "shared"];
let storelist = {};
allstores.forEach((k, v) => {
  let storeFile = require("./stores/" + k);
  storeFile.namespaced = true;
  storelist[k] = storeFile;
});

let store = new Vuex.Store({
  modules: storelist,
  plugins: [storePlugin],
});

var app = new Vue({
  el: "#container",
  router,
  store,
  i18n,
  render: function(h) {
    return h(App);
  },
});
