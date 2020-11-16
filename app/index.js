import Vue from "vue";
import Vuex from "vuex";
import VueRouter from "vue-router";
import App from "./index.vue";
import home from "./home.vue";
import testpage from "./testpage.vue";
import textpage from "./textpage.vue";
import axios from "axios";
import vueSlider from "vue-slider-component";
import VRuntimeTemplate from "v-runtime-template";
import storePlugin from "./utils/storePlugin";
import { i18n } from "./utils/i18n";

Vue.prototype.$axios = axios;
Vue.prototype.$cache = {};

// load config
Vue.prototype.$config = JSON.parse(
  document.getElementById("bootload-config").innerText
);

// load tests
Vue.prototype.$tests = JSON.parse(
  document.getElementById("bootload-tests").innerText
);

Vue.component("vue-slider", vueSlider);
Vue.component("vrt", VRuntimeTemplate);

Vue.component("mdc", {
  template: "#markdowncontact",
});

// Vue.directive('bgimg', bgimg)
// Vue.component('loadimg', loadimg)

const routes = [];

routes.push({
  path: "/",
  component: home,
});

routes.push({
  path: "/test/:testname",
  component: testpage,
});

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

routes.push({
  path: "/:textname",
  component: textpage,
});

// set routes
const router = new VueRouter({
  routes: routes,
  mode: "history",
});

Vue.use(VueRouter);

// stores
Vue.use(Vuex);

const allstores = ["profile", "tests", "extra", "func"];
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
