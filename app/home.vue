<template>
  <div id="home">
    <div id="top">
      <languages></languages>
      <div id="line"></div>
      <div id="name">about</div>
    </div>
    <div id="content">
      <md id="introtext" class="section" md="intro"></md>
      <md id="hometext" class="section" md="home"></md>
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
  border: 0.25rem solid @fg;
  // border-width: 0.25rem 0 0 0;
  // border-left-width: 40vw;
  padding: 0.25rem 0.75rem;
  min-height: 100vh;
  border-color: #f0f;
  border-width: 0;
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
      margin: 0 1.5em 0 0;
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

  #content {
    display: flex;
    // font-size: 1.5rem;
    align-items: center;
    > * {
      height: auto;
    }
    /deep/ p {
      max-width: 18em;
    }
  }
  #introtext {
    padding: 0 0;
    font-family: "Victor Serif Trial";
    // font-family: "Stabil Grotesk Trial";
    // font-family: "Reckless Neue TRIAL";
    font-weight: 300;
    max-width: 15em;
    max-width: 8em;
    font-size: 4vw;
    font-size: 4rem;
    line-height: 1em;
    // margin-left: 20vw;
    margin-top: 0;
    margin-top: 16rem;
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
    animation-delay: 1s;
    // animation-delay: 0s;
    // animation-duration: 0s;
    animation-timing-function: @easeInOutExpo;
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
      font-size: 1.5rem;
      letter-spacing: 0;
      word-spacing: 0;
      line-height: 1em;
      max-width: 18em;
      &:nth-child(1) {
        font-size: 3rem;
        letter-spacing: -0.025em;
      }
    }
  }
  #hometext {
    font-weight: 300;
    font-family: helvetica;
    padding-left: 2rem;
    /deep/ p {
      animation-name: pin;
      animation-duration: 3s;
      animation-delay: 1s;
      // animation-delay: 0s;
      // animation-duration: 0s;
      animation-timing-function: @easeInOutExpo;
      animation-fill-mode: forwards;
      transform-origin: top left;
      // .clip(left);
      opacity: 0;
      @keyframes pin {
        0% {
          opacity: 0;
          transform: translateY(2em);
        }
        100% {
          opacity: 1;
          transform: translateY(0em);
        }
      }
      .loop(@counter) when (@counter > 0) {
        .loop((@counter - 1)); // next iteration
        &:nth-child(@{counter}) {
          animation-delay: 2 + @counter * 0.125s;
        }
      }
      .loop(10);
    }
    // margin-top: 18rem;
    // font-family: "Victor Serif Trial";
    // font-size: 24px;
    // /deep/ p {
    //   display: inline-block;
    //   max-width: 14em;
    //   margin: 0vh 4em 10vh 4em;
    //   background: #fafafa;
    //   line-height: 1.2em;
    //   // min-height: 22em;
    //   padding: 12em 0.5em 0.5em;
    //   opacity: 0.85;
    //   position: relative;
    //   font-size: 24px;

    //   &:after {
    //     content: "";
    //     position: absolute;
    //     left: 0;
    //     top: 0;
    //     width: 100%;
    //     height: 70%;
    //     background: linear-gradient(#005, @syn, #fee, #fafafa);
    //     z-index: -1;
    //     opacity: 1;
    //     transition: 5s;
    //   }
    //   &:hover {
    //     &:after {
    //       opacity: 1;
    //     }
    //   }
    // }
  }
}
</style>
