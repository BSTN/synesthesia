<template>
  <div id="home" class="textwrap">
    <div class="wrap">
      <h1>Synesthesia test</h1>
      <div class="section" v-html="markdown"></div>
      <div class="button">
        <router-link to="/instructions">start</router-link>
      </div>
    </div>
  </div>
</template>
<script>
import Vue from "vue";
import homemd from "./texts/home.md";
export default {
  data() {
    return {
      markdown: homemd,
    };
  },
};
</script>
<style lang="less" scoped>
#home {
  .wrap {
  }
}
</style>
