<template>
  <div id="combinedresult" v-show="value">
    <slot v-bind:value="value"></slot>
    <div id="slider" v-if="value">
      <div id="value" :style="{ left: (100 / 30) * value + '%' }"></div>
      <div id="cutoff" :style="{ width: (100 / 30) * (30 - 17) + '%' }"></div>
    </div>
  </div>
</template>
<script>
export default {
  computed: {
    value() {
      if (
        ["pq1", "pq2", "pq3", "pq4", "pq5", "pq6"].filter((x) => {
          return (
            !(x in this.$store.state.extra) ||
            this.$store.state.extra[x] === null
          );
        }).length > 0
      ) {
        return false;
      }
      return (
        this.$store.state.extra.pq1 +
        this.$store.state.extra.pq2 +
        this.$store.state.extra.pq3 +
        this.$store.state.extra.pq4 +
        this.$store.state.extra.pq5 +
        this.$store.state.extra.pq6
      );
    },
  },
};
</script>
<style lang="less" scoped>
#combinedresult {
  margin: 2em auto;
  max-width: 28em;
  text-align: center;
  @s: 10px;
  @ss: 5px;
  #slider {
    position: relative;

    height: 1rem;
    width: 100%;
    overflow: hidden;
    margin-top: 1em;
    &:before {
      content: "";
      position: absolute;
      border-radius: 0.25em;
      left: 0;
      height: @s;
      top: calc(50% - @ss);
      width: 100%;
      background: @fg;
      opacity: 0.125;
    }
    #value {
      position: absolute;
      height: 100%;
      z-index: 1;
      &:before {
        content: "";
        position: absolute;
        left: -2px;
        top: 0;
        height: 100%;
        width: 4px;
        background: @fg;
        border-radius: 0.25em;
      }
    }
    #cutoff {
      background: @syn;
      position: absolute;
      border-radius: 0 0.25em 0.25em 0;
      right: 0;
      width: 100%;
      height: @s;
      top: calc(50% - @ss);
    }
  }
}
</style>
