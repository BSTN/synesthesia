<template>
  <div id="likertpage" class='textpage'>
    likertpage
    {{$route.params.testname}}
    <md md="postquestions"></md>
    <div id="next">
      <router-link :class="{enabled: allAnswered()}" class='link' :to="{name: 'results', params: {testname: this.$route.params.testname || false}}">{{$t('next')}}</router-link>
    </div>
  </div>
</template>
<script>
export default {
  methods: {
    allAnswered () {
      const extras = this.$store.state.extra
      const testArray = ['pq1','pq2','pq3','pq4','pq5','pq6'].filter(x => {
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
  mounted () {
    if (this.allAnswered()) {
      this.$router.push({name: 'results', params: {testname: this.$route.params.testname || false}})
    }
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