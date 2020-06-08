<template>
  <div id="testframe">
    <div id="frame">
      <div id="top">
        <div class="flex"></div>
        <button>help</button>
      </div>
      <div id="mid">
        <transition name="symbol" mode="out-in">
          <div id="symbol" :key="$store.state.test.position">
            <div
              id="symbolframe"
              :style="{ background: background }"
              :class="{ nocolor: q.color === 'nocolor' }"
            >
              <div id="sym">
                <div>{{ q.symbol }}</div>
              </div>
            </div>
          </div>
        </transition>
        <div id="color">
          <colorpicker></colorpicker>
        </div>
      </div>
      <div id="bottom">
        <!-- <button id="helpbutton">help</button> -->
        <button id="midbutton" class="inactive">
          Try to instinctively choose the color you associate, or feel fits best
          with the character/word/image/sound above.
        </button>
        <div class="flex"></div>
        <button id="nextbutton" @click="next()" :disabled="disabled">
          next
        </button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  computed: {
    position() {
      return this.$store.state.test.position;
    },
    q() {
      return this.$store.state.test.questions[this.position];
    },
    background() {
      if (this.q.color === null) return "none";
      else if (this.q.color === "nocolor") return "url('assets/nocolor.png')";
      else return this.q.color;
    },
    disabled() {
      return this.q.color === null;
    },
  },
  methods: {
    next() {
      // set timing
      //
      window.scrollTo(0, 0);
      if (
        Object.keys(this.$store.state.test.questions).length - 1 ==
        this.position
      ) {
        this.$router.push({ path: "/results" });
      } else {
        this.$store.dispatch("test/next");
      }
    },
  },
  mounted() {
    window.addEventListener("keydown", (ev) => {
      if (ev.keyCode === 13 && this.q.color !== null) this.next();
    });
  },
};
</script>
<style lang="less" scoped>
#testframe {
  height: auto;
  // min-height: 25rem;
  max-width: 40rem;
  width: 100%;
  margin: 0 auto;
  position: relative;
  background: @bg;
  background: #eee;
  display: block;
  border-radius: 0.5em;
  overflow: hidden;
  user-select: none;
  // box-shadow: 0 0 0.25em rgba(#000, 0.2);
  #frame {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    width: 100%;
    > * {
      // border: 1px solid @bg2;
    }
    #top {
      background: @bg3;
      background: #e9e9e9;
      font-size: 0.75em;
      color: @fg2;
      padding: 0.5em 0.5em;
      display: flex;
      button {
        opacity: 0.5;
        &:hover {
          opacity: 1;
          color: @fg1;
        }
      }
    }
    #mid {
      display: flex;
      flex-direction: row;
      min-height: 100%;
      flex-grow: 1;
      flex-shrink: 1;
      align-items: stretch;

      > * {
        position: relative;
        // height: 100%;
        width: 100%;
        flex-grow: 1;
        flex-shrink: 1;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
      }
      #symbol {
        // outline: 1px solid #000;
        #symbolframe {
          width: calc(100% - 3rem);
          height: calc(100% - 3rem);
          display: flex;
          align-content: center;
          align-items: center;
          justify-content: center;
          // background: @bg3;
          border-radius: 0.25rem;
          &.nocolor {
            border: 1px solid #ddd;
            #sym {
              border: 1px solid #ddd;
            }
          }
          #sym {
            font-size: 4rem;
            display: flex;
            align-content: center;
            align-items: center;
            justify-content: center;
            padding: 0.5em 1rem;
            background: @bg;
            background: #eee;
            margin: 2rem;
            min-width: calc(100% - 8rem);
            min-height: calc(100% - 8rem);
            border-radius: 0.25rem;
          }
        }
      }
      #color {
        // outline: 1px solid #000;
      }
    }
    #bottom {
      min-height: 3.5rem;
      flex-shrink: 0;
      flex-grow: 0;
      display: flex;
      align-content: center;
      align-items: center;
      justify-content: flex-end;
      padding: 0 1.5em;
      background: @bg3;
      background: #e9e9e9;
      button {
        &[disabled="disabled"] {
          opacity: 0.5;
          font-style: italic;
          pointer-events: none;
        }
        &:hover {
          text-decoration: underline;
        }
        &#midbutton {
          white-space: nowrap;
          &.inactive {
            color: @fg2;
            pointer-events: none;
            font-size: 0.75em;
            opacity: 0.5;
            white-space: normal;
            min-width: 60%;
            padding: 0;
            text-align: left;
          }
        }
      }
      #helpbutton,
      #count {
        color: @fg2;
      }
    }
  }
}

@media (max-width: 800px) {
  #testframe {
    #frame {
      #mid {
        flex-direction: column;
        #symbol {
          #symbolframe {
            border: 0;
            margin: 1rem 1rem 0 1rem;
            width: 100%;
            height: 100%;
          }
        }
        #color {
          /deep/ #colorpicker {
            border: 1rem solid transparent !important;
          }
        }
      }
      #bottom {
        flex-direction: column;
        padding: 1rem;
        #nextbutton {
          margin-top: 1rem;
          padding: 1rem;
        }
      }
    }
  }
}
</style>
