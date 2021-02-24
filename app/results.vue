<template>
  <div id="results">
    <compare :open.sync="compare"></compare>
    <topbar>
      <template #left>
        <router-link to="/">home</router-link>
      </template>
      <template #right>
        <button @click="print">print</button>
      </template>
    </topbar>
    <div id="wrap">
      <div id="tabs">
        <button
          v-for="(tab, testname) in $tests"
          :key="'tab' + testname"
          id="tab"
          :class="{ active: testname === currentTab }"
          @click="currentTab = testname"
        >{{ tab.name[$store.state.profile.language] }}</button>
      </div>

      <div id="mainresults">
        <div id="description" v-if="$tests[testname].results">
          <div id="result">
            <score :testname="testname" type="total"></score>
          </div>
          <md :md="$tests[testname].results"></md>
        </div>
        <div id="description" v-if="$tests[testname].results && testinfo.likert">
          <score type="likert"></score>
          <div>{{ $t("likert") }}</div>
        </div>
      </div>
      
      <button @click="compare = true">{{ $t("compare") }}</button>

      <div id="resultlist">
        <!-- <symbolresult
          v-for="s in symbols"
          :key="JSON.stringify(s)"
          :symbol="s"
          :testname="testname"
        /> -->
        <score
          v-for="s in symbols"
          :key="JSON.stringify(s)"
          :symbol="s"
          :testname="testname"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { each } from 'lodash';
export default {
  data() {
    return {
      currentTab: "graphemes",
      compare: false
    };
  },
  computed: {
    testname() {
      return this.currentTab;
      // return this.$store.state.tests.active || "graphemes";
    },
    testinfo () {
      return this.$tests[this.currentTab]
    },
    testnameLang () {
      return this.$tests[this.testname].name[this.$store.state.profile.language]
    },
    testresults () {
      return {
        value: 20
      }
    },
    likertresults () {
      let result = 0
      for(let i in this.$store.state.extra) {
        if(!isNaN(this.$store.state.extra[i])) {
          result = result + this.$store.state.extra[i]
        }
      }
      return result / 34 * 100
    },
    scale () {
      return 6
    },
    totalScore () {
      // calculate score
      return (3.4 / this.scale) * 100
    },
    cutoff () {
      return (4.5 / this.scale) * 100
    },
    symbols() {
      let symbols = [];
      each(this.$store.state.tests.tests[this.currentTab].questions, (v, k) => {
        if (symbols.indexOf(v.symbol) < 0) symbols.push(v.symbol);
      });
      return symbols.sort();
    },
    tabs() {
      const tabs = [];
      each(this.$tests, (v, k) => {
        // if (v.hidden)
        tabs.push(v.name[this.$store.state.profile.language]);
      });
      return tabs;
    }
  },
  mounted() {
    window.addEventListener("keydown", ev => {
      if (ev.key === "r") {
        this.fill();
      }
    });
  },
  methods: {
    print() {
      window.print();
    },
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
        value: Math.round(Math.random() * 6)
      });
      this.$store.dispatch("extra/set", {
        key: "pq2",
        value: Math.round(Math.random() * 6)
      });
      this.$store.dispatch("extra/set", {
        key: "pq3",
        value: Math.round(Math.random() * 6)
      });
      this.$store.dispatch("extra/set", {
        key: "pq4",
        value: Math.round(Math.random() * 6)
      });
      this.$store.dispatch("extra/set", {
        key: "pq5",
        value: Math.round(Math.random() * 6)
      });
      this.$store.dispatch("extra/set", {
        key: "pq6",
        value: Math.round(Math.random() * 6)
      });
    }
  }
};
</script>

<style lang="less" scoped>
#results {
  background: @bg;
  min-height: 100vh;
  font-family: Helvetica, sans-serif;
  padding-bottom: 5rem;
}

#wrap {
  // width: 40rem;
  // margin: 2rem auto;
  max-width: calc(100% - 3rem);
  margin: 0 auto;
}

#tabs {
  text-align: center;
  padding: 2em 0 3em;

  #tab {
    text-transform: capitalize;
    position: relative;
    padding: 0.15em 0 0.2em;
    display: inline-block;
    margin: 0 1em 0.5em;
    opacity: 0.5;

    &:last-child {
      &::after {
        display: none;
      }
    }

    &:hover,
    &.active {
      // background: @fg;
      // color: @bg;
      // border-radius: 0.25em;
      opacity: 1;
      // border-bottom: 2px solid @fg;
    }
  }
}
#mainresults {
  display: flex;
  justify-content: center;
  
}
#description {
  font-size: 0.75rem;
  width: 16rem;
  max-width: 100%;
  margin: 0 3rem;
  margin-bottom: 3rem;
  line-height: 1.5em;

  /deep/ h1,
  /deep/ h2,
  /deep/ h3,
  /deep/ h4,
  /deep/ h5,
  /deep/ h6 {
    margin: 0 auto 1em;
    text-align: center;
    max-width: 70%;
  }
}
#inframe {
  display: flex;
  display: grid;
  grid-template-columns: 1fr 4fr;
  grid-gap: 1rem;
  padding: 0;
  width: 100%;
  #description {
    flex-shrink: 0;
  }
}
#resultlist {
  // min-width: 50%;
  display: grid;
  grid-gap: 1rem;
  grid-template-columns: 1fr;
  font-size: 0.75rem;
  width: 20rem;
  margin: 0 auto;
  max-width: calc(100% - 2rem);
}

@media (max-width: 40rem) {
  #mainresults {
    flex-direction: column;
  }
  #description {
    margin: 0 0 2rem;
  }
}
</style>
