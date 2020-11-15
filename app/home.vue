<template>
  <div id="home">
    <paperjs v-if="$config.animation"></paperjs>
    <topbar>
      <template #left>
        <languages></languages>
      </template>
      <template #right>
        <!-- <router-link to="/about" id="name">
          {{ $t("about") }}
        </router-link> -->
      </template>
    </topbar>
    <div id="content">
      <transition name="hometext">
        <md id="hometext" class="section" md="home"></md>
      </transition>
      <div id="introtext" class="section" md="intro">
        <div id="md">{{ $t("intro") }}</div>
        <md id="abouttext" class="section" md="about"></md>
      </div>
    </div>
  </div>
</template>
<script>
import Vue from "vue";
export default {
  data() {
    return {
      homemd: false,
      contactmd: false,
      delayed: false,
    };
  },
  mounted() {
    setTimeout(() => {
      this.delayed = true;
    }, 2000);
  },
};
</script>
<style lang="less" scoped>
#home {
  min-height: 100vh;
  min-height: calc(var(--vh, 1vh) * 100);
  border-width: 0;

  // /deep/ #topbar {
  //   position: sticky;
  // }

#content {
  padding: 1rem 1.75rem;
  position: relative;
  z-index:2;
  #hometext {
    font-family: "Victor";
    font-weight: 300;
    font-size: 3rem;
    letter-spacing: -0.035em;
    text-align: center;
    max-width: 12em;
    margin: 1em auto;
    /deep/ p {
      line-height: 1em;
    }
    /deep/ .link {
      font-size: 1rem;
    }
    @media (max-width: 1000px){
      font-size: 2rem;
    }
    @media (max-width: 500px){
      font-size: 1.5rem;
      letter-spacing: -0.01;
    }
  }
  #introtext {
    border-top: 2px solid @fg;
    border-bottom: 2px solid @fg;
    padding: 1rem 0;
    font-size: 1em;
    font-family: "Victor";
    font-weight: 500;
    columns:3;
    column-gap: 1rem;
    #md {
      margin-bottom: 1em;
      font-size: 1rem;
    }
    /deep/ p {
      text-indent: 1.5rem;
    }
    @media (max-width: 1000px){
      columns: 1;
      > div {
        max-width: 24em;
        margin: 0 auto;
      }
    }
    @media (max-width: 500px){
      columns: 1;
    }
  }
}
      

}
</style>
