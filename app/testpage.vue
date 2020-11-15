<template>
  <div id="testpage">
    <div id="loading" v-if="loading">
      LOADING...
    </div>
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
      if (this.testdata.pretest) return !!(this.testdata.pageCount == this.testdata.pretest.length)
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
      if (this.testdata.posttest && (this.testdata.pageCount > this.testdata.pretest.length)) return true
      return false
    }
  },
  methods: {
    next() {

    },
    done(){
      this.$store.dispatch('tests/nextPage');
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
      await this.$root.alert({message:"This test does not exist."})
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
  padding: 0.5rem;
  // background: @bg;
  // background: #ddd;
  // color: #222;
  #loaded {
    #testframecontainer {
      min-height: 100vh;
      display: flex;
      align-content: center;
      align-items: center;
    }
  }
  @media (max-width: 600px) {
    padding: 0;
    #testframe {
      border-radius: 0;
    }
  }
}
#loading {
  max-width: 8rem;
  background: #00f;
  margin:0 auto;
  font-size: 0.5rem;
  padding: 0 1em;
  border-radius: 0.5em;
  color: #ccc;
}
#pretest, #posttest, #results {
  margin: 0 auto;
  max-width: 32em;
  padding: 0 1em;
  font-family: 'Victor';
  @media (max-width: 800px) {
    margin: 1rem auto;
  }
  
}
</style>
