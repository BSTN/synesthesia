<template>
  <div id="results">
    <topbar>
      <template #left>
        <router-link to="/">home</router-link>
      </template>
    </topbar>
    <div id="wrap">
      RESULTS
      <button @click="$store.dispatch('tests/fill', testname)">fill</button>
      <!-- <button @click="download()">download</button> -->
      <symbolresult
        v-for="s in symbols"
        :key="s"
        :symbol="s"
        :testname="testname"
      ></symbolresult>
    </div>
  </div>
</template>
<script>
import { each } from "lodash";
export default {
  data() {
    return {
      testname: "graphemes",
    };
  },
  computed: {
    symbols() {
      let symbols = [];
      each(this.$store.state.tests.tests[this.testname].questions, (v, k) => {
        if (symbols.indexOf(v.symbol) < 0) symbols.push(v.symbol);
      });
      return symbols.sort();
    },
  },
  methods: {
    download() {
      window.open(
        "data:text/json;charset=utf-8," +
          encodeURIComponent(JSON.stringify(this.$store.state))
      );
    },
  },
  mounted() {},
};
</script>
<style lang="less" scoped>
#results {
  #wrap {
    max-width: 40rem;
    margin: 2rem auto;
  }
}
</style>
