<template>
  <div id="tests">
    <router-link id="testlink" v-for="(test,name) in testlist" :key="name" :to="'/test/' + name">
      <span>{{name}}</span>
    </router-link>
  </div>
</template>
<script>
export default {
  props: ['list'],
  computed: {
    testlist () {
      let names = this.list.split(',');
      let testlist = {}
      for(name in names) {
        testlist[names[name]] = this.$tests[names[name].trim()] || false
      }
      return testlist;
    }
  }
}
</script>
<style lang="less" scoped>
#tests {
  text-align: center;
  #testlink {
    display:inline-block;
    border: 1px solid @fg;
    width: 10rem;
    max-width: calc(100% - 2rem);
    height: 6rem;
    margin: 1rem;
    color: @fg;
    text-decoration: none;
    position: relative;
    transition: all 0.5s;
    border-radius: 0.25em;
    span {
      position:absolute;
      bottom:.5em;
      width: 100%;
      left:0;
    }
    &:hover {
      background: @fg;
      color: @bg;
    }
  }
}
</style>