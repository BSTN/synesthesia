<template>
  <div id="next">
    <router-link :to="to">
      <slot></slot>
    </router-link>
  </div>
</template>
<script>
export default {
  props: ["to"],
};
</script>
<style lang="less" scoped>
#next {
  font-family: Helvetica, sans-serif;
  text-align: center;
  padding: 1em;
  border: 1px solid @fg;
  border-radius: .25em;
  display:inline-block;
  min-height: 12em;
  max-width: 10em;
  width: 100%;
  vertical-align: bottom;
  a {
    margin: 0;
  }
}
</style>
