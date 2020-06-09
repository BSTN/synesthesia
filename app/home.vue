<template>
  <div id="home">
    <div id="top">
      <div id="name">Synesthesie Test</div>
      <div id="line"></div>
      <languages></languages>
    </div>
    <md id="introtext" class="section" md="intro"></md>
    <md id="hometext" class="section" md="home"></md>
  </div>
</template>
<script>
import Vue from "vue";
export default {
  data() {
    return {
      homemd: false,
      contactmd: false,
    };
  },
  mounted() {},
};
</script>
<style lang="less" scoped>
#home {
  padding: 1rem 2rem;
  // > * {
  //   background: #fafafa;
  //   margin-bottom: 0.5rem;
  // }
  #top {
    font-size: 0.75rem;
    display: flex;
    text-transform: uppercase;
    font-weight: 400;
    > * {
      flex-shrink: 0;
      flex-grow: 0;
    }
    #line {
      width: 100%;
      flex-shrink: 1;
      flex-grow: 1;
      margin: 0 0.5em;
      &:after {
        content: "";
        border-top: 2px solid @fg;
        width: 100%;
        display: inline-block;
        height: 0.25em;
      }
    }
  }
  #introtext {
    padding: 0 0;
    font-family: "Victor Serif Trial";
    // font-family: "Stabil Grotesk Trial";
    // font-family: "Reckless Neue TRIAL";
    font-weight: 300;
    max-width: 15em;
    font-size: 4vw;
    font-size: 3rem;
    @media (max-width: 800px) {
      font-size: 1.5rem;
    }
    line-height: 1em;
    letter-spacing: -0.025em;
    animation-name: line;
    animation-duration: 2s;
    animation-timing-function: @easeInOutExpo;
    animation-delay: 0.5s;
    animation-fill-mode: forwards;
    transform-origin: top left;
    .clip(left);
    @keyframes line {
      0% {
        // line-height: 1.3em;
        // opacity: 0;
        // transform: scaleX(0.3);
        .clip(left);
      }
      100% {
        // line-height: 1em;
        // opacity: 1;
        // transform: scaleX(1);
        .clip();
      }
    }
    /deep/ p {
      line-height: inherit;
    }
  }
  #hometext {
    min-height: 300vh;
    font-family: helvetica;
    font-weight: 400;
    // font-size: 24px;
    /deep/ p {
      display: inline-block;
      max-width: 14em;
      margin: 30vh 4em 10vh 4em;
      background: #fafafa;
      line-height: 1.2em;
      // min-height: 22em;
      padding: 12em 0.5em 0.5em;
      opacity: 0.85;
      position: relative;
      font-size: 24px;
      &:after {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 70%;
        background: linear-gradient(#005, @syn, #fee, #fafafa);
        z-index: -1;
      }
    }
  }
}
</style>
