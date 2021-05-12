<template>
  <div id="tests">
    <router-link
      v-for="(test,name) in testlist"
      id="testlink"
      :key="name"
      :to="'/test/' + name"
      :class="{'finished': $store.state.profile.finishedtests.indexOf(name) >= 0}"
    >
      <div id="image"></div>
      <span>{{ test.name[$store.state.profile.language] }}</span>
      {{$store.state.profile.finishedtests.indexOf(name) >= 0 ? $t('done') : '' }}
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
  },
  methods: {
    async isFinished (test) {
      return await this.$store.dispatch('tests/isFinished', test)
    }
  }
}
</script>
<style lang="less" scoped>
#tests {
  text-align: center;
  #testlink {
    display:block;
    // border: 1px solid @fg;
    // width: 10rem;
    min-width: 10rem;
    max-width: calc(100% - 2rem);
    height: 6rem;
    margin: 1rem;
    // color: @fg;
    text-decoration: none;
    position: relative;
    transition: all 0.5s;
    border-radius: 0.25em;
    // background: @fg;
    // background: url('~assets/kleuren.png');
    height: auto;
    background-position: center;
    background-size: 180%;
    background-repeat: no-repeat;
    font-family: Helvetica;
    box-sizing: border-box;
    overflow: hidden;
    display:inline-flex;
    flex-direction: column;
    vertical-align: middle;
    --c1: #fdfdb4;
    --c2: #ccccff;
    &:nth-child(2) {
      --c1: #ffbf94;
      --c2: #d5ddd9;
    }
    &:nth-child(3) {
      --c1: #dbf1b6;
      --c2: #ffa7e9;
    }
    #image {
      font-size: 2em;
      left:0;
      top:0;
      width:100%;
      padding-top: 80%;
      border-radius: 0.25rem;
      background: radial-gradient(var(--c1), var(--c2));
      color: transparent;
      cursor:pointer;
      // background: @fg;
      // color: #fff;
      // filter: blur(5px);
      display:none;
    }
    span {
      color: @fg;
      padding: .5em 1em; 
      // font-size: 0.8rem;
      border: 1px solid @fg;
      border-radius: 0.25rem;
      background: @bg;
      display: block;
    }
    &:hover {
      // background: @fg;
      color: @bg;
      background-size: 100%;
      // transform: scale(1.05);
      span {
        background: @fg;
        color: @bg;
      }
    }
    &.finished {
      background: @fg;
      border: 5px solid @fg;
      color: @bg;
      font-size: 0.75rem;
      span {
        // margin-bottom: 0.5rem;
        // font-size: 1rem;
      }
    }
  }
}
</style>