import VueRouter from "vue-router";
import home from "./home.vue";
import testpage from "./testpage.vue";
import textpage from "./textpage.vue";
import results from "./results.vue";

const routes = [];

routes.push({
  path: "/",
  component: home,
});

routes.push({
  path: "/results",
  component: results,
});

routes.push({
  path: "/test/:testname",
  component: testpage,
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

export default router