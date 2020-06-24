<template>
  <div id="next">
    <router-link class="link" :to="to">
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
  a {
    margin: 0;
  }
}
</style>
