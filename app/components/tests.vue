<template>
  <div id="tests">
    <router-link
      v-for="(test,name) in testlist"
      id="testlink"
      :key="name"
      :to="'/test/' + name"
    >
      <span>{{ test.name[$store.state.profile.language] }}</span>
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
    background: @fg;
    // background: url('~assets/kleuren.png');
    height: 12rem;
    background-position: center;
    background-size: 180%;
    background-repeat: no-repeat;
    font-family: Helvetica;
    box-sizing: border-box;
    span {
      position:absolute;
      bottom:0;
      width: 100%;
      left:0;
      color: @bg;
      padding: .5rem;
      box-sizing: border-box;
    }
    &:hover {
      // background: @fg;
      color: @bg;
      background-size: 100%;
      transform: scale(1.05);
    }
  }
}
</style>