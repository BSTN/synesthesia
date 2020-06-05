<template>
  <div id="home">
    <div id="title" class="top">
      <button id="icon"></button>
      <h1>Synesthesie test</h1>
      <button>about</button>
    </div>

    <div class="textwrap">
      <div class="wrap">
        <md id="text" class="section" md="home"></md>
      </div>
    </div>
    <div class="footer">
      <md id="text" class="section" md="contact"></md>
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
  #title {
    padding: 2rem 2rem;
    > * {
      display: inline-block;
    }
    h1 {
      font-size: 1rem;
    }
    #icon {
      @s: 10rem;
      width: @s;
      height: @s;
      border-radius: 50%;
      background: @link;
      margin: 0 0.5rem;
      vertical-align: 27%;
      filter: blur(2em);

      animation-name: colors;
      animation-duration: 15s;
      animation-direction: forward;
      animation-iteration-count: infinite;
      animation-timing-function: linear;
      @keyframes colors {
        0% {
          background: #555;
        }
        20% {
          background: #00f;
        }
        40% {
          background: #555;
        }
        60% {
          background: #f00;
        }
        80% {
          background: #555;
        }
        100% {
          background: #555;
        }
      }
    }
  }
  #text {
    // position: absolute;
    // right: 0;
    // top: 0;
    // width: 50%;
  }
  .textwrap {
  }
  .footer {
    font-size: 0.75em;
    padding: 3rem;
    opacity: 0.5;
    /deep/ p {
      margin: 0;
      // line-height: 1.5em;
    }
  }
}
</style>
