<template>
  <div id="index">
    <transition name="page" mode="out-in">
      <router-view :key="$route.path" class="view" />
    </transition>
    <dialog-dialogues />
  </div>
</template>
<script>
// don't scroll to top on history back
window.history.scrollRestoration = "manual";
import { each, sample, debounce, isEmpty } from "lodash";
import moment from "moment";
const chalk = require("chalk");
export default {
  watch: {
    $route() {
      setTimeout(() => {
        window.scrollTo(0, 0);
      }, 250);
    },
  },
  created() {
    // pick a set randomly and prepare store with questions
    each(this.$tests, (v, name) => {
      let setname = sample(Object.keys(v.sets));
      this.$store.dispatch("tests/setQuestions", {
        name: name,
        setname: setname,
        questions: v.sets[setname],
        pretest: v.pretest,
        posttest: v.posttest,
      });
    });
  },
  mounted() {
    // VH fix for mobile
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
  },
  methods: {
    openAlert() {
      this.$root
        .confirm({
          message: "May I ask you a question, please?",
          options: ["yes", "no", "again", "something else"],
        })
        .then((x) => {
          console.log(x);
        })
        .catch((x) => {
          console.error(x);
        });
    },
    setTheme(i) {
      each(document.body.classList, (x) => {
        if (x.match(/^theme/)) document.body.classList.remove(x);
      });
      document.body.classList.add("theme" + i);
      document.body.dispatchEvent(new CustomEvent("changetheme"));
    },
  },
};
</script>
<style lang="less">
@import "less/main.less";
</style>

<style lang="less" scoped>
#index {
  position: relative;
  z-index: 1;
}
.view {
  position: relative;
  z-index: 2;
  max-width: 1440px;
  margin: 0 auto;
}
#footer {
  text-align: center;
  font-size: 0.6rem;
  padding: 1rem;
}
</style>
