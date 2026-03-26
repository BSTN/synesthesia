import VueRouter from "vue-router";
const home = () => import("./home.vue");
const testpage = () => import("./testpage.vue");
const textpage = () => import("./textpage.vue");
const results = () => import("./results.vue");
const likert = () => import("./likert.vue");

const routes = [];

routes.push({
  path: "/",
  component: home
});

routes.push({
  path: "/results",
  name: "results",
  component: results
});

routes.push({
  path: "/extra",
  name: "likert",
  component: likert
});

routes.push({
  path: "/test/:testname",
  component: testpage
});

routes.push({
  path: "/:textname",
  component: textpage
});

// set routes
const router = new VueRouter({
  routes: routes,
  mode: "history"
});

export default router;
