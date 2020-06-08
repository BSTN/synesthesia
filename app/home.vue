<template>
  <div id="home">
    <div id="links">
      <div id="top">
        <div id="name">Synesthesie Test</div>
        <div id="line"></div>
        <div id="year">NL EN SE</div>
      </div>
      <md id="text" class="section" md="intro"></md>
    </div>
    <div id="rechts">
      <md id="text" class="section" md="home"></md>
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
    };
  },
  mounted() {},
};
</script>
<style lang="less" scoped>
#home {
  padding: 1rem 0;

  > * {
    width: 100%;
    padding: 0 2rem;
    font-size: 1.75rem;
    @media (max-width: 800px) {
      padding: 0 0.5rem;
    }
    &:nth-child(2) {
      // border-left: 2px solid @fg;
    }
  }
  #links {
    #text {
      padding: 0 0;
      font-family: "Victor Serif Trial";
      // font-family: "Reckless Neue TRIAL";
      font-weight: 300;
      max-width: 15em;
      font-size: 4vw;
      font-size: 2rem;
      @media (max-width: 800px) {
        font-size: 1.5rem;
      }
      line-height: 1em;
      letter-spacing: -0.025em;
      // max-width: 12em;
      // font-size: 3rem;
      // letter-spacing: -0.025em;
      animation-name: line;
      animation-duration: 3s;
      animation-timing-function: @easeInOutExpo;
      animation-delay: 0.5s;
      animation-fill-mode: forwards;
      opacity: 0;
      @keyframes line {
        0% {
          line-height: 1.3em;
          opacity: 0;
        }
        100% {
          line-height: 1em;
          opacity: 1;
        }
      }
      /deep/ p {
        line-height: inherit;
      }
    }
  }
  #rechts {
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
  #top {
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
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
}
</style>
