<template>
  <div id="index">
    <paperjs></paperjs>
    <transition name="clipright" mode="out-in">
      <router-view class="view"></router-view>
    </transition>
  </div>
</template>
<script>
import { each, sample, debounce, isEmpty } from "lodash";
export default {
  created() {
    if (!isEmpty(this.$route.query)) {
      if (this.$route.query.id)
        this.$store.dispatch("profile/set", { USERID: this.$route.query.id });
      if (this.$route.query.lang)
        this.$store.dispatch("profile/set", {
          language: this.$route.query.lang,
        });
    }
    // pick a set randomly and prepare store with questions
    each(this.$tests, (v, name) => {
      let setname = sample(Object.keys(v.sets));
      this.$store.dispatch("tests/setQuestions", {
        name: name,
        setname: setname,
        questions: v.sets[setname],
      });
    });
  },
  watch: {
    $route() {
      setTimeout(() => {
        window.scrollTo(0, 0);
      }, 250);
    },
  },
  mounted() {
    // VH fix for mobile
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
    window.addEventListener(
      "resize",
      debounce(() => {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty("--vh", `${vh}px`);
      }),
      500
    );
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
