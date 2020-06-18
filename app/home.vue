<template>
  <div id="home">
    <topbar>
      <template #left>
        <languages></languages>
      </template>
      <template #right>
        <router-link to="/about" id="name">
          about
        </router-link>
      </template>
    </topbar>
    <div id="content">
      <div id="introtext" class="section" md="intro">
        <div id="md">{{ $t("intro") }}</div>
      </div>
      <transition name="hometext">
        <md id="hometext" class="section" md="home" v-if="delayed"></md>
      </transition>
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

  /deep/ #topbar {
    position: sticky;
  }

  #content {
    min-height: calc(100vh - 2rem);
    min-height: calc(var(--vh, 1vh) * 100 - 2rem);

    > * {
      height: auto;
    }
    /deep/ p {
      max-width: 18em;
    }
  }
  #introtext {
    position: fixed;
    bottom: 1rem;
    left: 1rem;

    padding: 0 0;
    font-family: "Victor";
    font-weight: 300;

    font-size: 5vw;
    line-height: 1em;
    width: 50vw;
    letter-spacing: -0.025em;
    animation-name: line;
    animation-duration: 2s;
    animation-delay: 1s;
    animation-timing-function: @easeInOutExpo;
    animation-fill-mode: forwards;
    transform-origin: top left;
    opacity: 0;
    @keyframes line {
      0% {
        opacity: 0;
        .clip(left);
      }
      99% {
        opacity: 1;
        .clip();
      }
      100% {
        opacity: 1;
        clip-path: none;
      }
    }
    #md {
      line-height: inherit;
      max-width: 15em;
      margin: 0;
    }
  }
  #hometext {
    margin-right: 1rem;
    padding: 2rem 4rem 0 2rem;

    // min-height: calc(100vh - 3rem);
    min-height: calc(var(--vh, 1vh) * 100 - 3rem);
    border-left: 1px solid @fg;
    float: right;
    width: 100%;
    max-width: 40vw;

    display: flex;
    align-items: center;
    justify-content: flex-start;

    /deep/ #md {
      align-self: center;
      margin: 0;
    }

    /deep/ p {
      animation-name: pin;
      animation-duration: 2s;
      animation-delay: 1s;
      // animation-delay: 0s;
      // animation-duration: 0s;
      animation-timing-function: @easeOutExpo;
      animation-fill-mode: forwards;
      transform-origin: top left;
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
          animation-delay: 0 + @counter * 0.125s;
        }
      }
      .loop(10);
    }
  }
  @media (max-width: 1200px) {
    #content {
      #hometext {
        padding: 2rem;
      }
    }
  }
  @media (max-width: 800px) {
    #content {
      display: block;
      #introtext {
        position: relative;
        width: 100%;
        font-size: 1.5rem;
        bottom: auto;
        left: auto;
        padding: 0 0.5rem;
        /deep/ p {
          max-width: auto;
        }
      }
      #hometext {
        border: 0;
        margin-left: 0;
        width: 70%;
        max-width: none;
        padding-top: 4rem;
      }
    }
  }
  @media (max-width: 600px) {
    #content {
      #introtext {
        display: flex;
        align-items: flex-end;
        min-height: calc(var(--vh, 1vh) * 80 - 2.5rem);
        /deep/ #md {
          align-self: flex-end;
        }
      }
      #hometext {
        padding: 2rem 0.5rem;
        float: left;
        align-items: left;
        display: block;
        width: 100%;
        max-width: none;
        /deep/ #md {
          margin: 0;
        }
        /deep/ p {
          max-width: 100%;
          width: 100%;
        }
      }
    }
  }
}
</style>
