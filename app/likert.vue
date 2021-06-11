<template>
  <div id="likertpage" class='textpage'>
    likertpage
    {{$route.params.testname}}
    <md :md="markdownfile"></md>
    <div id="next">
      <router-link :class="{enabled: allAnswered()}" class='link' :to="{name: 'results', params: {testname: this.$route.params.testname || false}}">{{$t('next')}}</router-link>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loaded: false
    }
  },
  computed: {
    markdownfile () {
      return this.$tests[this.$route.params.testname].likertmarkdown || "postquestions"
    }
  },
  methods: {
    allAnswered () {
      const extras = this.$store.state.extra
      
      /* 
      
      get questionslist by 
      - reading the markdown (likertmarkdown in config), 
      - listing the q="1" attributes
      - naming the questions (this.$route.params.testname + q attribute)

      */
      let name = this.markdownfile
      if (this.$store.state.profile.language !== this.$config.defaultLanguage) {
        name = name + "." + this.$store.state.profile.language;
      }
      let template = document.getElementById(`template${name}`);
      let list = []
      if (template) {
        let txt = template.innerHTML
        let found = txt.match(/(?<=q=\")(.*?)(?=\")/g)
        list = found.map(x => {
          return this.$route.params.testname + x
        })
      }
      const testArray = list.filter(x => {
        if (extras && (x in extras) && !isNaN(extras[x])) {
          return false
        }
        return true
      })
      // check if all questions answered
      return testArray.length === 0
    }
  },
  beforeRouteEnter (to, from, next) {
    if (from.name === 'results') {
      next("/")
    } else {
      next()
    }
  },
  beforeRouteLeave (to, from, next) {
    // upload extra data!
    if (this.$store.state.profile.UID) {
      this.$axios.post("./api/store", {
        table: "extra",
        data: { values: JSON.parse(JSON.stringify(this.$store.state.extra)) },
        UID: this.$store.state.profile.UID,
      }).then(x => {
        next()
      }).catch(x => {
        this.$root.alert('Error uploading data, please try again.')
      })
    } else {
      next()
    }
  },
  mounted () {
    if (this.allAnswered()) {
      this.$router.push({name: 'results', params: {testname: this.$route.params.testname || false}})
    }
    this.loaded = true
  }
}
</script>
<style lang="less" scoped>
#next {
  display:block;
  text-align: center;
  margin-bottom: 8rem;
  a {
    pointer-events: none;
    opacity: 0.5;
    &.enabled {
      pointer-events: auto;
      opacity: 1;
    }
  }
}
</style>