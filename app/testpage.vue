<template>
  <div id="testpage">
    <transition name="fade">
      <div id="loading" v-if="loading">
        LOADING...
      </div>
    </transition>
    <div id="loaded" v-if="!loading">
      <div id="pretest" v-if="showPretest" class="textpage">
        <div v-for="(item,k) in config.pretest" :key="k" v-if="k === pretestCount">
          <md :md="item"></md>
        </div>
      </div>
      <div id="testframecontainer" v-if="showTestframe">
        <testframe @done="done()"></testframe>
      </div>
      <div id="posttest" v-if="showPosttest" class="textpage">
        <div v-for="(item,k) in config.posttest" :key="k" v-if="k === posttestCount">
          <md :md="item"></md>
        </div>
      </div>
      <div id="results" v-if="$store.state.profile.finishedtests.indexOf($route.params.testname) > -1" class="textpage">
        <topbar>
          <template #left>
            <router-link to="/">{{$t('home')}}</router-link>
          </template>
          <template #right></template>
        </topbar>
        <md :md="'results'"></md>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      loading: true,
      finished: false,
      results: false
    };
  },
  computed: {
    ...mapGetters({
      testdata: "tests/testdata",
      q: "tests/q",
    }),
    config() {
      return this.$tests[this.$route.params.testname];
    },
    showTestframe() {
      if (this.testdata.pretest) return !!(this.testdata.pretest && this.testdata.pageCount == this.testdata.pretest.length)
      return this.testdata.pageCount == 0
    },
    pretestCount () {
      return this.testdata.pageCount
    },
    posttestCount () {
      if (this.testdata.pretest) return this.testdata.pageCount - this.testdata.pretest.length - 1;
      return 0;
    },
    showPretest () {
      if (this.testdata.pretest && this.testdata.pageCount < this.testdata.pretest.length) return true
      return false
    },
    showPosttest () {
      if (this.testdata.posttest && this.$store.state.profile.finishedtests.indexOf(this.$route.params.testname) < 0) {
        if (!this.testdata.pretest && this.testdata.pageCount > 0) return true
        if (this.testdata.pretest && this.testdata.pageCount > this.testdata.pretest.length) return true
      }
      return false
    }
  },
  methods: {
    next() {

    },
    async done(){
      let data = await this.$store.dispatch('tests/nextPage').catch(err => {
        console.warn("Error going to nextpage:", err)
      });
      if ( data ) console.log(data);
    }
  },
  watch: {
    'testdata.pageCount': function () {
      window.scrollTo(0, 0);
    }
  },
  async mounted() {
    /* check if this test exists */
    if (
      !this.$route.params.testname ||
      Object.keys(this.$store.state.tests.tests).length < 1 ||
      !(this.$route.params.testname in this.$store.state.tests.tests)
    ) {
      await this.$root.alert({message:"Sorry, this test does not exist (anymore)."})
      this.$router.push({ path: "/" });
    } else {
      this.$store.commit("tests/setActive", this.$route.params.testname);
    }
    /* get uid */
    if (this.$store.state.profile.UID === null) {
      let x = false;
      try {
        x = await this.$axios.post("./api/create", { language: this.$store.state.profile.language })
      } catch (err) {
        await this.$root.alert({message:"Error testpage #1."})
        this.$router.push({path:"/"});
      }
      if (x && x.data.UID && x.data.SHARED) {
        await this.$store.dispatch("profile/set", { UID: x.data.UID });
        await this.$store.dispatch("profile/set", { SHARED: x.data.SHARED });
      } else {
        await this.$root.alert({message:"Error testpage #2."});
        this.$router.push({path:"/"});
      }
    }
    /* LOADING */
    this.loading = false;
  }
};
</script>
<style lang="less" scoped>
#testpage {
  // padding: 0.5rem;
  // background: @bg;
  // background: #ddd;
  // color: #222;
  #loaded {
    #testframecontainer {
      min-height: 100vh;
      display: flex;
      align-content: center;
      align-items: center;
      @media (min-width: 800px) {
        padding: 0 1em;
      }
    }
  }
}
#loading {
  max-width: 8rem;
  background: @fg;
  margin:0 auto;
  font-size: 0.5rem;
  padding: 0 1em;
  border-radius: 0.5em;
  color: @bg;
  text-align: center;
  position:fixed;
  z-index:9;
  right: 0.5em;
  display:none;
}
#pretest #md, #posttest #md, #results #md {
  margin: 0 auto;
  max-width: 32em;
  padding: 0 .5em;
  font-family: 'Victor';
  @media (max-width: 800px) {
    margin: 1rem auto;
  }
  
}
</style>
