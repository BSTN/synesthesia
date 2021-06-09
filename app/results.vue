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
    <div id="name" v-if="$store.state.func.name">
      {{$store.state.func.name}}
    </div>
    <!-- <pre>{{score}}</pre> -->
    <div id="wrap">
      <div id="tabs">
        <button
          v-for="(tab, testname) in tablist"
          :key="'tab' + tab.id"
          id="tab"
          :class="{ active: tab.id === currentTab }"
          @click="currentTab = tab.id"
        >{{ tab.name[$store.state.profile.language] }}</button>
      </div>
      <!-- <pre>{{score}}</pre> -->

      <div id="dothetest" v-if="isNaN(score.total) || score.total === null">
        <router-link :to="'/test/' + currentTab">
          {{$t('notdone')}}
        </router-link>
      </div>

      <div id="mainresults">
        <div id="description" v-if="((!isNaN(score.total) && score.total !== null) || activeSharedProfile)">
          <!-- <md :md="$tests[testname].results"></md> -->
          <div id="md" v-if="testinfo.resulttext">{{ testinfo.resulttext[this.$store.state.profile.language] }}</div>
          <label v-if="!isNaN(score.total) && score.total !== null">{{$t('yourscorethistest')}}</label>
          <score :testname="testname" type="total" :data="score" v-if="!isNaN(score.total) && score.total !== null"></score>
          <label v-if="activeSharedProfile">{{$t("sharedscorethistest", {name: activeSharedProfile.name})}}</label>
          <score class='shared'  v-if="activeSharedProfile" :testname="testname" type="total" :data="sharedScore"></score>
        </div>
        <div id="description" v-if="testinfo.likert && (likertScore || likertSharedScore)">
          <div id="md" v-if="testinfo.likert">{{ $t("likert") }}</div>
          <label v-if="likertScore">{{$t('yourscorelikerttest')}}</label>
          <score type="likert" :data="likertScore" v-if="likertScore"></score>
          <label v-if="activeSharedProfile">{{$t('sharedscorelikerttest', {name: activeSharedProfile.name})}}</label>
          <score class='shared' v-if="likertSharedScore" type="likert" :data="likertSharedScore"></score>
        </div>
      </div>

      <div id="detailed">
        <div id="resultlist" v-if="!isNaN(score.total) && score.total !== null">
          <label>Jouw score per item:</label>
          <score
            v-for="(s, symbol) in score.symbols"
            :key="testname + symbol"
            :data="s"
            :testname="testname"
          />
        </div>
        <div id="resultlist" v-if="!isNaN(sharedScore.total) && sharedScore.total !== null">
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
      compare: false,
      tablist: []
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
      // todo: move this to config?
      let template = document.getElementById(`templatetests`);
      let tabs = {}
      if (template) {
        template = template.innerHTML
        let dom = new DOMParser().parseFromString(template, 'text/html');
        let new_element = dom.querySelector('tests');
        let list = new_element.getAttribute('list').split(',')
        for(let i in list) {
          tabs[list[i]] = this.$tests[list[i]]
        }
      }
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
    // set testlist  --------- bit complicated, but this way you can have a different list for every language

    let name = "tests"
    if (this.$store.state.profile.language !== this.$config.defaultLanguage) {
        name = name + "." + this.$store.state.profile.language;
      }
    let template = document.getElementById(`template${name}`);
    if (template) {
      let txt = template.innerHTML
      let found = txt.match(/(?<=<tests list=\")(.*?)(?=\")/g)
      if (found[0]) {
        let list = found[0].split(',')
        this.tablist = list.map(x => {
          return {
            id: x,
            name: this.$tests[x].name
          }
        })
      }
    }

    // let names = this.list.split(',');
    // let testlist = {}
    // for(name in names) {
    //   testlist[names[name]] = this.$tests[names[name].trim()] || false
    // }
    // return testlist;
  },
  methods: {
    print() {
      this.$root.input({inputvalue: this.$store.state.func.name + '', label: this.$t('yourname')}).then(async x => {
        await this.$store.commit('func/setName', x.inputvalue);
        setTimeout(() => {
          window.print();
        }, 500)
      }).catch(x => {
        // do nothing on cancel name input
      });
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

#name {
  font-size: 2rem;
  text-align: center;
  display:none;
  padding: 0.5em;
  @media print {
    display: block;
  }
}

#wrap {
  // width: 40rem;
  // margin: 2rem auto;
  max-width: calc(100% - 3rem);
  margin: 0 auto;
  @media (max-width: 30rem) {
    max-width: calc(100% - 1rem);
  }
}

#tabs {
  text-align: center;
  padding: 1em 0 3em;

  #tab {
    text-transform: capitalize;
    position: relative;
    padding: 0.5em 1em 0.4em;
    display: inline-block;
    margin: 0 .5em 0.5em;
    opacity: 0.5;
    border-radius: 0.25em;
    border: 1px solid @fg;
    color: @fg;

    color:@fg;
    background: @bg;

    &:last-child {
      &::after {
        display: none;
      }
    }

    &:hover,
    &.active {
      color:@bg;
      background: @fg;
      // background: @fg;
      // color: @bg;
      // border-radius: 0.25em;
      opacity: 1;
      // border-bottom: 2px solid @fg;
    }
    @media print {
      display:none;
      &.active {
        margin: 0 auto;
        display:block;
        background: 0;
        border: 1px solid @fg;
      }
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
  &:hover {
    background: @fg;
    color: @bg;
    a {
      color:inherit;
    }
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
  @media print {
    display:block;
    text-align: center;
    max-width: 100%;
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
  @media print {
    display:inline-block;
    text-align: left;
    margin: 0 2rem !important;
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
  @media (max-width: 50rem) {
    display:block;
  }
}
#resultlist {
  // min-width: 50%;
  // display: grid;
  // grid-gap: 1rem;
  // grid-template-columns: 1fr;
  font-size: 0.75rem;
  width: 20rem;
  margin: 0 auto;
  max-width: 100%;
  label {
    opacity: 1;
    padding: 2rem 1rem;
    text-align: center;
    font-size: 1rem;
  }
  @media print {
    width: 100%;
  }
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
