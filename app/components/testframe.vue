<template>
  <div id="testframe">
    <div id="frame">
      <!-- <div id="top">
        <div class="flex"></div>
        <button>help</button>
      </div> -->
      <div id="mid">
        <transition name="symbol" mode="out-in">
          <div id="symbol" :key="testdata.position">
            <div id="symbolframe">
              <div
                id="colorholder"
                :style="{ background: background }"
                :class="{
                  nocolor: q.value === 'nocolor',
                  empty: q.value === null,
                }"
              ></div>
              <div id="sym">
                <div>{{ q.symbol }}</div>
              </div>
            </div>
          </div>
        </transition>
        <div id="color">
          <colorpicker v-if="config.type === 'colorpicker'"></colorpicker>
          <colorgrid v-if="config.type === 'colorgrid'"></colorgrid>
        </div>
      </div>
      <div id="bottom">
        <!-- <button id="helpbutton">help</button> -->
        <div class="flex"></div>
        <div id="help" v-if="!enabled">
          {{ $t("colorpickerhelp") }}
        </div>
        <button id="nextbutton" @click="next()" v-if="enabled">
          {{ $t("next") }} {{ q.time }}
        </button>
        <div class="flex"></div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import { now, filter } from "lodash";
export default {
  data() {
    return {
      startTime: false,
    };
  },
  computed: {
    ...mapGetters({
      testdata: "tests/testdata",
      q: "tests/q",
    }),
    config() {
      return this.$tests[this.$route.params.testname];
    },
    testname() {
      return this.$route.params.testname;
    },
    setname() {
      return this.$store.state.tests.tests[this.testname].setname;
    },
    position() {
      return this.testdata.position;
    },
    background() {
      if (this.q.value === null) return "none";
      else if (this.q.value === "nocolor") return "url('assets/nocolor.png')";
      else return "#" + this.q.value;
    },
    enabled() {
      return this.q.value !== null;
    },
  },
  methods: {
    async next() {
      // set timing
      await this.storeTime();

      // send data to server
      let data = JSON.parse(JSON.stringify(this.q));
      data.testname = this.testname;
      data.setname = this.setname;
      let err = await this.$axios
        .post("./api/store", {
          table: "questions",
          UID: this.$store.state.profile.UID,
          data: data,
        })
        .catch((err) => {
          return { error: "Could not store data", err: err.response.data };
        });

      if (err.error) {
        console.warn("Could not store data", err.err);
        return false;
      }

      // scroll to top (for mobile)
      window.scrollTo(0, 0);
      if (Object.keys(this.testdata.questions).length - 1 == this.position) {
        // set finished to true
        await this.$store.dispatch("tests/setFinished");
        // go to next page
        this.$router.push({ path: this.config.continue });
      } else {
        this.$store.dispatch("tests/next");
      }
    },
    async storeTime() {
      await this.$store.dispatch("tests/setValue", {
        timing: now() - this.startTime,
      });
    },
    checkFinished() {
      let undone = filter(this.testdata.questions, (q) => {
        return q.color === null && !q.timing === null;
      });
      if (Object.keys(undone).length < 1) {
        // redirect!
        console.log("finished, redirect please...");
      } else {
      }
    },
  },
  watch: {
    "q.qnr": function() {
      // reset timer
      this.startTime = now();
    },
  },
  mounted() {
    this.checkFinished();
    this.startTime = now();
    window.addEventListener("keydown", (ev) => {
      if (ev.keyCode === 13 && this.q.value !== null) this.next();
    });
  },
};
</script>
<style lang="less" scoped>
#testframe {
  background: @bg;
  background: #fafafa;
  color: #000;

  height: auto;
  // min-height: 25rem;
  max-width: 40rem;
  width: 100%;
  margin: 0 auto;
  position: relative;
  display: block;
  border-radius: 0.5em;
  overflow: hidden;
  user-select: none;
  // box-shadow: 0 0 0.25em rgba(#000, 0.2);
  @media (max-width: 800px) {
    min-height: 100vh;
  }
  #frame {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    width: 100%;
    #top {
      background: #d6d6d6;
      background: #e9e9e9;
      font-size: 0.75em;
      padding: 0.5em 0.5em;
      display: flex;
      button {
        opacity: 0.5;
        &:hover {
          opacity: 1;
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
          border-radius: 0.25rem;
          font-family: "Roboto Mono";
          #colorholder {
            position: absolute;
            left: 0;
            top: 0;
            width: calc(100% - 3rem);
            height: calc(100% - 3rem);
            margin: 1.5rem;
            z-index: 1;
            border-radius: 0.5em;
          }
          &.nocolor {
            border: 1px solid #ddd;
            #sym {
              border: 1px solid #ddd;
            }
          }
          #sym {
            z-index: 2;
            font-size: 4rem;
            display: flex;
            align-content: center;
            align-items: center;
            justify-content: center;
            padding: 0.5em 1rem;
            background: @bg;
            background: #fafafa;
            margin: 2rem;
            min-width: calc(100% - 8rem);
            min-height: calc(100% - 8rem);
            border-radius: 0.25rem;
          }
        }

        @media (max-width: 800px) {
          max-height: 4rem;
          font-size: 1.5em;
          #symbolframe {
            border-radius: 0.25em;
            display: flex;
            position: relative;
            margin: 2rem 0;
            justify-content: stretch;
            align-items: stretch;
            box-shadow: 0 0 0.125rem #ccc;
            #colorholder {
              content: "";
              position: relative;
              background: inherit;
              width: 2em;
              height: 2em;
              margin: 0;
              flex-shrink: 0;
              flex-grow: 0;
              display: inline-block;
              z-index: 2;
              box-sizing: border-box;
              border-radius: 0.25em 0 0 0.25em;
            }
            #sym {
              background: transparent;
              position: relative;
              z-index: 3;
              text-align: left;
              width: 100%;
              flex-shrink: 1;
              flex-grow: 1;
              font-size: 1em;
              display: block;
              border-radius: 0;
              margin: 0;
              padding: 0 1em;
              // border: 1px solid #ddd;
              // border-width: 1px 1px 1px 0px !important;
              border-radius: 0 0.25em 0.25em 0;
              line-height: calc(2em - 2px);
            }
          }
        }
        @media (max-width: 500px) {
          font-size: 1em;
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
      padding: 0 1.5em;
      background: #d6d6d6;
      background: #e9e9e9;
      background: #f3f3f3;
      #help {
        opacity: 0.25;
        font-style: italic;
        pointer-events: none;
        line-height: 1.25em;
        font-size: 0.75em;
        min-width: 80%;
        flex-shrink: 1;
        flex-grow: 1;
        text-align: center;
      }
      button {
        padding: 0.5em 1em 0.35em;
        border-radius: 0.25em;
        cursor: pointer;
        opacity: 1;
        pointer-events: auto;
        background: #eee;
        color: #999;
        &:hover {
          background: #fff;
          color: #222;
        }
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
        background: transparent;
        #nextbutton {
          margin-top: 1rem;
          margin-bottom: 1rem;
          padding: 0.75rem 1.5em;
          border: 1px solid #ddd;
          background: transparent;
        }
      }
    }
  }
}
</style>
