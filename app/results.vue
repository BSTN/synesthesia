<template>
  <div id="results">
    <topbar>
      <template #left>
        <router-link to="/">
          home
        </router-link>
      </template>
      <template #right>
        <router-link to="/">
          print
        </router-link>
      </template>
    </topbar>
    <div id="wrap">
      <h1>Mijn Resultaten</h1>
      <div id="tabs">
        <button v-for="(tab, testname) in $tests" :key="'tab' + testname" id="tab" :class="{active: testname === currentTab}" @click="currentTab = testname">
          {{ tab.name[$store.state.profile.language] }}
        </button>
      </div>
      <div id="top">
        <button class="flex" />
        <!-- <button @click="fill()">
          fill all random
        </button> -->
      </div>
      <!-- <button @click="download()">download</button> -->
      <symbolresult
        v-for="s in symbols"
        :key="JSON.stringify(s)"
        :symbol="s"
        :testname="testname"
      />
    </div>
  </div>
</template>
<script>
import { each } from "lodash";
export default {
  data() {
    return {
      currentTab: "graphemes"
    }
  },
  computed: {
    testname() {
      return this.currentTab
      return this.$store.state.tests.active || "graphemes";
    },
    symbols() {
      let symbols = [];
      each(this.$store.state.tests.tests[this.currentTab].questions, (v, k) => {
        if (symbols.indexOf(v.symbol) < 0) symbols.push(v.symbol);
      });
      return symbols.sort();
    },
    tabs () {
      const tabs = []
      each(this.$tests, (v, k) => {
        // if (v.hidden)
        tabs.push(v.name[this.$store.state.profile.language])
      })
      return tabs
    }
  },
  mounted() {
    window.addEventListener('keydown', (ev) => {
      if (ev.key === 'r') {
        this.fill();
      }
    })
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
  padding-bottom: 5rem;
}

h1 {
  text-align: center;
}

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

#tabs {
  text-align: center;
  padding: 2em 0;

  #tab {
    position: relative;
    padding: 0 1em;
    opacity: 0.5;
    display: inline-block;

    // &::after {
    //   content: "";
    //   position: absolute;
    //   border-right: 1px solid @fg;
    //   height: 100%;
    //   right: 0;
    //   top: 0;
    //   opacity: 0.25;
    // }

    &:last-child {
      &::after {
        display: none;
      }
    }

    &:hover,
    &.active {
      opacity: 1;
    }
  }
}

</style>
