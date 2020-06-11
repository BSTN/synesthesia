<template>
  <div id="index">
    <div id="overlay"></div>
    <transition name="clipright" mode="out-in">
      <router-view class="view"></router-view>
    </transition>
  </div>
</template>
<script>
// import symbols1 from "../setup/symbols1.txt";
import _ from "lodash";
export default {
  created() {
    _.each(this.$tests, (v, name) => {
      // pick a set randomly
      let setname = _.sample(Object.keys(v.sets));
      this.$store.dispatch("tests/setQuestions", {
        name: name,
        setname: setname,
        questions: v.sets[setname],
      });
    });
    // let symbols = symbols1.split("\n");
    // for (let k in symbols) {
    //   this.$store.dispatch("tests/appendQuestion", {
    //     qnr: k,
    //     symbol: symbols[k],
    //   });
    // }
  },
  watch: {
    $route() {
      setTimeout(() => {
        window.scrollTo(0, 0);
      }, 250);
    },
  },
  mounted() {},
};
</script>
<style lang="less">
@import "less/main.less";
</style>

<style lang="less" scoped>
#index {
  position: relative;
  z-index: 1;
  #overlay {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
    background: linear-gradient(-45deg, #fffadf, #dbdbdb);
    background: linear-gradient(-45deg, #fffadf, #f8f8f8);
    // background: linear-gradient(-45deg, #252523, #1e1f27);
    // mix-blend-mode: overlay;
    opacity: 0.5;
  }
}
.view {
  position: relative;
  z-index: 2;
}
#footer {
  text-align: center;
  font-size: 0.6rem;
  padding: 1rem;
}
</style>
