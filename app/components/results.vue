<template>
  <div id="results">
    <div id="wrap">
      <div id="top">
        <button class="flex"></button>
        <button @click="fill()">fill all random</button>
      </div>
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
  computed: {
    testname() {
      return this.$store.state.tests.active || "graphemes";
    },
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
    fill() {
      this.$store.dispatch("tests/fill", this.testname);
      this.$store.dispatch("extra/set", {
        key: "pq1",
        value: Math.round(Math.random() * 6),
      });
      this.$store.dispatch("extra/set", {
        key: "pq2",
        value: Math.round(Math.random() * 6),
      });
      this.$store.dispatch("extra/set", {
        key: "pq3",
        value: Math.round(Math.random() * 6),
      });
      this.$store.dispatch("extra/set", {
        key: "pq4",
        value: Math.round(Math.random() * 6),
      });
      this.$store.dispatch("extra/set", {
        key: "pq5",
        value: Math.round(Math.random() * 6),
      });
      this.$store.dispatch("extra/set", {
        key: "pq6",
        value: Math.round(Math.random() * 6),
      });
    },
  },
};
</script>
<style lang="less" scoped>
#results {
  background: @bg;
  min-height: 100vh;
  font-family: Helvetica, sans-serif;
  #top {
    display: flex;
    margin-bottom: 2em;
    button {
      white-space: nowrap;
    }
  }
  #wrap {
    max-width: 40rem;
    margin: 2rem auto;
  }
}
</style>
