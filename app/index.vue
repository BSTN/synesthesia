<template>
  <div id="index">
    <transition name="clipright" mode="out-in">
      <router-view class="view"></router-view>
    </transition>
  </div>
</template>
<script>
import symbols1 from "../setup/symbols1.txt";
export default {
  created() {
    let symbols = symbols1.split("\n");
    for (let k in symbols) {
      this.$store.dispatch("test/appendQuestion", {
        qnr: k,
        symbol: symbols[k],
      });
    }
  },
  watch: {
    $route() {
      setTimeout(() => {
        window.scrollTo(0, 0);
      }, 250);
    },
  },
  mounted() {
    // window.onbeforeunload = function(e) {
    //   return "Please press the Logout button to logout.";
    // };
  },
};
</script>
<style lang="less">
@import "less/main.less";
</style>

<style lang="less" scoped>
.view {
}
#footer {
  text-align: center;
  font-size: 0.6rem;
  padding: 1rem;
}
</style>
