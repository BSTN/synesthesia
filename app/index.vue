<template>
  <div id="index">
    <transition name="page" mode="out-in">
      <router-view class="view" :key="$route.path"></router-view>
    </transition>
    <dialog-dialogues></dialog-dialogues>
  </div>
</template>
<script>
// don't scroll to top on history back
window.history.scrollRestoration = "manual";
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
  methods: {
    openAlert() {
      this.$root.confirm({message: "May I ask you a question, please?", options: ["yes", "no", "again" ,"something else"]}).then(x => {
        console.log(x);
      }).catch(x => {
        console.error(x);
      })
    },
    setTheme(i) {
      each(document.body.classList, (x) => {
        if (x.match(/^theme/)) document.body.classList.remove(x);
      });
      document.body.classList.add("theme" + i);
      document.body.dispatchEvent(new CustomEvent("changetheme"));
    },
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
    // window.addEventListener(
    //   "resize",
    //   debounce(() => {
    //     let vh = window.innerHeight * 0.01;
    //     document.documentElement.style.setProperty("--vh", `${vh}px`);
    //   }),
    //   10
    // );
    var i = 0;
    window.addEventListener("keydown", (ev) => {
      if (ev.keyCode === 39) i++;
      if (ev.keyCode === 37) i--;
      if (i < 0) i = 0;
      if (i > 7) i = 7;
      if (ev.keyCode === 37 || ev.keyCode === 39) this.setTheme(i);
      // if (ev.keyCode === 13) {
      //   this.openAlert();
      // }
    });
    this.setTheme(2);
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
