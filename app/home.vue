<template>
  <div id="home">
    <paperjs v-if="$config.animation"></paperjs>
    <topbar>
      <template #left>
        <languages></languages>
      </template>
      <template #right>
        <router-link to="/about" id="name">
          {{ $t("about") }}
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

  video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: fill;
  }

  #content {
    position: relative;
    z-index: 2;
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
    pointer-events: none;
    position: fixed;
    top: 3rem;
    left: 0.75rem;

    padding: 0 0;
    font-family: "Victor";
    font-weight: 300;

    font-size: 3.5vw;
    line-height: 1.1em;
    width: 40vw;
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
      // &:after {
      //   content: "";
      //   width: 2em;
      //   height: 1em;
      //   // background: ;
      //   float: left;
      //   border-radius: 100%;
      // }
    }
  }
  #hometext {
    z-index: 2;
    margin-right: 1rem;
    padding: 2rem 4rem 0 4rem;

    font-family: "Victor";
    font-weight: 500;
    font-size: 1rem;

    // min-height: calc(100vh - 3rem);
    min-height: calc(var(--vh, 1vh) * 100 - 3rem);
    border-left: 1px solid @fg;
    float: right;
    width: 100%;
    max-width: 50vw;

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
        max-width: 50vw;
      }
    }
  }
  @media (max-width: 800px) {
    #content {
      display: block;
      padding: 0;
      #introtext {
        margin: 0;
        position: relative;
        width: 100%;
        bottom: auto;
        left: auto;
        padding: 0 .5rem;
        display: block;
        top: auto;
        // display: flex;
        // align-items: flex-end;
        min-height: calc(var(--vh, 1vh) * 50 - 2.5rem);
        min-height: auto;
        font-size: 1.5rem;
        /deep/ p {
          max-width: auto;
        }
      }
      #hometext {
        border: 0;
        margin-left: 0;
        width: 100%;
        max-width: none;
        padding-top: 4rem;
        // float: left;
        padding: 1rem;
        padding-left: 1rem;
        float: none;
        margin-top: 3rem;
        text-align: center;
        /deep/ #md {
          width: 100%;
          p {
            margin: 0 auto 1.5rem;
          }
        }
      }
    }
  }
  @media (max-width: 600px) {
    #content {
      #introtext {
        font-size: 1.25rem;
        // display: flex;
        // align-items: flex-end;
        // min-height: calc(var(--vh, 1vh) * 50 - 2.5rem);
        /deep/ #md {
          align-self: flex-end;
        }
      }
      #hometext {
        padding: 3.5rem 0.5rem 2rem 1rem;
        float: left;
        align-items: left;
        display: block;
        width: 100%;
        max-width: none;
        /deep/ #md {
          margin: 0;
        }
        /deep/ p {
          max-width: 18em;
          width: 100%;
        }
      }
    }
  }
}
</style>
