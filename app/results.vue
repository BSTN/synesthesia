<template>
  <div id="results">
    <compare :open.sync="compare"></compare>
    <topbar>
      <template #left>
        <router-link to="/">home</router-link>
      </template>
      <template #right>
        <button @click="compare = true">{{ $t("compare") }}</button>
        <button @click="print">print</button>
      </template>
    </topbar>
    <!-- <pre>{{$store.state.shared.profiles}}</pre> -->
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
      <!-- <pre>{{score}}</pre> -->

      <div id="dothetest" v-if="!score.total || score.total === null">
        <router-link :to="'/test/' + currentTab">
          {{$t('notdone')}}
        </router-link>
      </div>

      <div id="mainresults">
        <div id="description" v-if="$tests[testname].results && ((score.total && score.total !== null) || activeSharedProfile)">
          <md :md="$tests[testname].results"></md>
          <label v-if="score.total && score.total !== null">Jouw score voor deze test:</label>
          <score :testname="testname" type="total" :data="score" v-if="score.total && score.total !== null"></score>
          <label v-if="activeSharedProfile">De score van {{activeSharedProfile.name}}:</label>
          <score v-if="activeSharedProfile" :testname="testname" type="total" :data="sharedScore"></score>
        </div>
        <div id="description" v-if="$tests[testname].results && testinfo.likert && (likertScore || likertSharedScore)">
          <div id="md" v-if="testinfo.likert">{{ $t("likert") }}</div>
          <label v-if="likertScore">Jouw score voor de extra vragenlijst:</label>
          <score type="likert" :data="likertScore" v-if="likertScore"></score>
          <label v-if="activeSharedProfile">De score van {{activeSharedProfile.name}}:</label>
          <score v-if="activeSharedProfile" type="likert" :data="likertSharedScore"></score>
        </div>
      </div>

      <div id="detailed">
        <div id="resultlist" v-if="score.total && score.total !== null">
          <label>Jouw score per item:</label>
          <score
            v-for="(s, symbol) in score.symbols"
            :key="testname + symbol"
            :data="s"
            :testname="testname"
          />
        </div>
        <div id="resultlist" v-if="sharedScore.total && sharedScore.total !== null">
          <label>De score per item van {{activeSharedProfile.name}}:</label>
          <score
            v-for="(s, symbol) in sharedScore.symbols"
            :key="testname + symbol"
            :data="s"
            :testname="testname"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import score from './utils/score'
import { each } from 'lodash';
import { mapGetters } from 'vuex';
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
    ...mapGetters({
      activeSharedProfile: "shared/active"
    }),
    sharedData () {
      if (this.$store.state.shared.active && (this.$store.state.shared.active in this.$store.state.shared.profiles)) {
        return this.$store.state.shared.profiles[this.$store.state.shared.active]
      }
      return false
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
    likertScore () {
      return score.likert(this.$store.state.extra)
    },
    likertSharedScore () {
      if (!this.sharedData || !this.sharedData.data) return false
      return score.likert(this.sharedData.data['_extra'])
    },
    score () {
      let questions = this.$store.state.tests.tests[this.testname].questions
      return score.all(this.$tests[this.testname], questions)
    },
    sharedScore () {
      if (!this.$store.state.shared.profiles[this.$store.state.shared.active]) { return false }
      const questions = this.$store.state.shared.profiles[this.$store.state.shared.active].data[this.testname]
      return score.all(this.$tests[this.testname], questions)
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
    if (this.$route.params.testname) {
      this.currentTab = this.$route.params.testname
    }
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

#dothetest {
  text-align: center;
  border: 2px solid @fg;
  padding: 1rem;
  max-width: 20rem;
  margin: 0 auto 4rem;
  border-radius: 0.5rem;
  a {
    text-decoration: none;
  }
}

#mainresults {
  display: flex;
  justify-content: space-around;
  max-width: 40rem;
  margin: 0 auto;
  label {
    margin-bottom:0.5rem;
  }
}
#description {
  font-size: 0.75rem;
  width: 16rem;
  max-width: 100%;
  margin: 0 1rem;
  margin-bottom: 3rem;
  line-height: 1.5em;
  > div {
    max-width: 16rem;
  }
  #md {
    min-height: 9rem;
  }

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
#detailed {
  display: flex;
}
#resultlist {
  // min-width: 50%;
  display: grid;
  grid-gap: 1rem;
  grid-template-columns: 1fr;
  font-size: 0.75rem;
  width: 20rem;
  margin: 0 auto;
  max-width: 100%;
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
