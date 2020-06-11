<template>
  <div id="home">
    <div id="top">
      <div id="name">Synesthesia</div>
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
  border: 0.25rem solid @fg;
  // border-width: 0.25rem 0 0 0;
  // border-left-width: 40vw;
  padding: 0.25rem 0.75rem;
  min-height: 100vh;
  @media (max-width: 600px) {
    padding: 0.5rem;
  }
  // > * {
  //   background: #fafafa;
  //   margin-bottom: 0.5rem;
  // }
  #top {
    font-size: 0.65rem;
    display: flex;
    text-transform: uppercase;
    font-weight: 400;
    margin-bottom: 1rem;
    margin-bottom: 8rem;
    position: sticky;
    top: 0.5rem;
    > * {
      flex-shrink: 0;
      flex-grow: 0;
    }
    #line {
      width: 100%;
      flex-shrink: 1;
      flex-grow: 1;
      margin: 0 1.5em;
      animation-name: klip;
      animation-duration: 2s;
      animation-timing-function: @easeInOutExpo;
      animation-delay: 0.5s;
      animation-fill-mode: forwards;
      transform-origin: top left;
      .clip(left);
      @keyframes klip {
        0% {
          .clip(left);
        }
        100% {
          .clip();
        }
      }
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
    font-weight: 100;
    max-width: 15em;
    max-width: 9em;
    font-size: 4vw;
    font-size: 4rem;
    line-height: 1em;
    margin-left: 30vw;
    @media (max-width: 1200px) {
      margin-left: 0.25rem;
    }
    @media (max-width: 800px) {
      font-size: 2rem;
    }
    letter-spacing: -0.025em;
    // letter-spacing: -0.05em;
    // word-spacing: 0.2em;
    animation-name: line;
    animation-duration: 3s;
    animation-timing-function: @easeInOutExpo;
    animation-delay: 1s;
    animation-fill-mode: forwards;
    transform-origin: top left;
    // .clip(left);
    opacity: 0;
    @keyframes line {
      0% {
        // line-height: 1.3em;
        opacity: 0;
        .clip(top);
      }
      100% {
        // line-height: 1em;
        opacity: 1;
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
      margin: 0vh 4em 10vh 4em;
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
        opacity: 1;
        transition: 5s;
      }
      &:hover {
        &:after {
          opacity: 1;
        }
      }
    }
  }
}
</style>
