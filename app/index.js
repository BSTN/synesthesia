import Vue from "vue";
import Vuex from "vuex";
import VueRouter from "vue-router";
import App from "./index.vue";
import home from "./home.vue";
import axios from "axios";
import vueSlider from "vue-slider-component";
import VRuntimeTemplate from "v-runtime-template";

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

// import all pages as routes
const pages = require.context("./pages", true, /\.vue$/);
pages.keys().forEach((key) => {
  let newkey = key.replace(/(\.\/|\.vue)/g, "");
  routes.push({
    path: "/" + newkey,
    component: pages(key).default,
  });
});

// import all components
const components = require.context("./components", true, /\.vue$/);
components.keys().forEach((key) => {
  let newkey = key.replace(/(\.\/|\.vue)/g, "");
  Vue.component(newkey, components(key).default);
});

// import all directives
const directives = require.context("./directives", true, /\.vue$/);
directives.keys().forEach((key) => {
  let newkey = key.replace(/(\.\/|\.vue)/g, "");
  Vue.directive(newkey, directives(key).default);
});

// set routes
const router = new VueRouter({
  routes: routes,
  mode: "history",
});

Vue.use(VueRouter);

// stores
Vue.use(Vuex);

const allstores = ["profile", "tests"];
let storelist = {};
allstores.forEach((k, v) => {
  let storeFile = require("./stores/" + k);
  storeFile.namespaced = true;
  storelist[k] = storeFile;
});

let store = new Vuex.Store({
  modules: storelist,
  plugins: [],
});

var app = new Vue({
  el: "#container",
  router,
  store,
  render: function(h) {
    return h(App);
  },
});
