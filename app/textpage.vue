<template>
  <div id="textpage">
    <topbar>
      <template #left>
        <router-link to="/">home</router-link>
      </template>
    </topbar>
    <md :md="textname" v-if="textname"></md>
  </div>
</template>
<script>
export default {
  computed: {
    textname() {
      return this.$route.params.textname || false;
    },
  },
};
</script>
<style lang="less" scoped>
#textpage {
  background: @bg;
  /deep/ #md {
    max-width: 32rem;
    margin: 2rem auto;
  }
}
</style>
