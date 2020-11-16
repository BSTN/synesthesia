<template>
  <div id="testframe">
    <div id="frame">
      <div id="mid">
        <div id="leftframe" class='grapheme' v-if="config.type === 'grapheme'">
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
                <div
                  id="sym"
                  :class="{ mid: symbol.length > 1, long: symbol.length > 3 }"
                >
                  <div>{{ symbol }}</div>
                </div>
              </div>
            </div>
          </transition>
        </div>
        <div id="leftframe" class='audio' v-if="config.type === 'audio'">
          <div id="audio" @click="playAudio()" ref="audio" :style="{ background: background }">
            <div id="icons">
              <svg v-if="playing" width="500px" height="500px" viewBox="0 0 500 500" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g id="pause" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect id="Rectangle" fill="#222222" x="135" y="95" width="85" height="311" rx="10"></rect>
                      <rect id="Rectangle-Copy" fill="#222222" x="282" y="95" width="84" height="311" rx="10"></rect>
                  </g>
              </svg>
              <svg  v-if="!playing" width="500px" height="500px" viewBox="0 0 500 500" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g id="play" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <path d="M121.392483,46.0704638 L442.181895,251.480184 C446.832944,254.45837 448.189067,260.643087 445.210881,265.294135 C444.35089,266.637188 443.18215,267.755077 441.802195,268.554513 L121.012783,454.394516 C116.23394,457.163 110.115623,455.533282 107.347139,450.754438 C106.464701,449.231211 106,447.50203 106,445.741655 L106,54.491932 C106,48.9690845 110.477153,44.491932 116,44.491932 C117.911416,44.491932 119.78279,45.0397362 121.392483,46.0704638 Z" id="Rectangle" fill="#222222"></path>
                  </g>
              </svg>
            </div>
          </div>
        </div>
        <div id="color">
          <colorpicker v-if="config.selector === 'colorpicker'"></colorpicker>
          <colorgrid v-if="config.selector === 'colorgrid'"></colorgrid>
        </div>
      </div>
      <div id="bottom">
        <div id="progress"><div id="bar" :style="{width: (progress * 100) + '%'}"></div></div>
        <!-- <button id="helpbutton">help</button> -->
        <div class="flex"></div>
        <div id="help">{{ config.help[$store.state.profile.language] }}</div>
        <button id="nextbutton" @click="next()" v-active="q.value !== null">
          {{ $t("next") }}
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
      audio: false,
      playing: false
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
    total() {
      return Object.keys(this.testdata.questions).length;
    },
    progress() {
      return this.testdata.position / Object.keys(this.testdata.questions).length;
    },
    background() {
      if (this.q.value === null) return "none";
      else if (this.q.value === "nocolor") return "url('assets/nocolor.png')";
      else return "#" + this.q.value;
    },
    enabled() {
      return this.q.value !== null;
    },
    symbol() {
      return this.q.symbol.match(/^t\:/)
        ? this.$t(this.q.symbol.replace(/^t\:/, ""))
        : this.q.symbol;
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
        // done
        this.$emit("done");
      } else {
        // next question please
        this.$store.dispatch("tests/next");
      }
    },
    async storeTime() {
      await this.$store.dispatch("tests/setValue", {
        timing: now() - this.startTime,
      });
    },
    loadAudio () {
      let self = this;
      if (this.config.type === 'audio') {
        this.audio = new Audio('./data/audio/klinkers_rsa/' + this.symbol);
        this.audio.addEventListener('playing', function () {
          self.playing = true;
        })
        this.audio.addEventListener('ended', function () {
          self.playing = false;
        })
      }
    },
    playAudio () {
      if (this.config.type === 'audio') {
        this.audio.play();
      }
      // store audioclicks?
    }
  },
  watch: {
    "q.qnr": function() {
      // reset timer
      this.startTime = now();
      
      this.loadAudio();
      this.playAudio();
    },
  },
  mounted() {
    // this.checkFinished();
    this.startTime = now();
    this.loadAudio();
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
  background: #f3f3f3;
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
    border-radius: 0;
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
      #leftframe.grapheme {
        display: flex;
        flex-direction: row;
        min-height: 100%;
        flex-grow: 1;
        flex-shrink: 1;
        align-items: stretch;
      }
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
        width: 100%;
        height: 100%;
        #symbolframe {
          width: calc(100% - 3rem);
          height: calc(100% - 3rem);
          width: 100%;
          height: 100%;
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
            flex-direction: row;
            align-content: center;
            align-items: center;
            justify-content: center;
            padding: 0rem 1rem;
            background: @bg;
            background: #fafafa;
            margin: 2rem;
            min-width: calc(100% - 8rem);
            min-height: calc(100% - 8rem);
            border-radius: 0.25rem;
            &.mid {
              font-size: 2rem;
            }
            &.long {
              font-size: 1rem;
            }
          }
        }

        @media (max-width: 800px) {
          max-height: 4rem;
          font-size: 1.5em;
          #leftframe {
            width: calc(100% - 2rem);
            outline: 2px solid #00f;
          }
          #symbolframe {
            border-radius: 0.25em;
            display: flex;
            position: relative;
            margin: 2rem;
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
              // display: block;
              border-radius: 0;
              margin: 0;
              padding: 0 1em;
              // border: 1px solid #ddd;
              // border-width: 1px 1px 1px 0px !important;
              border-radius: 0 0.25em 0.25em 0;
              line-height: calc(2em - 2px);
              > div {
                width: 100%;
              }
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
      padding: 0 .75em;
      background: #d6d6d6;
      background: #e9e9e9;
      background: #f3f3f3;
      position: relative;
      #progress {
        position:absolute;
        top:0;
        width: 100%;
        left:0;
        height: 2px;
        #bar {
          position:absolute;
          left:0;
          top:0;
          width:0%;
          height:100%;
          background: #555;
          transition: all 0.25s;
        }
      }
      #help {
        opacity: 0.25;
        // font-style: italic;
        pointer-events: none;
        line-height: 1.25em;
        font-size: 0.75em;
        min-width: 80%;
        flex-shrink: 1;
        flex-grow: 1;
        text-align: left;
        padding-right: 1em;
      }
      #nextbutton {
        padding: 0.5em 1em 0.35em;
        border-radius: 0.25em;
        cursor: pointer;
        opacity: 1;
        pointer-events: auto;
        background: #eee;
        color: #999;
        pointer-events: none;
        line-height: 1;
        &.active {
          pointer-events: auto;
          color: #fafafa;
          background: #999;
        }
        &:hover {
          background: #555;
          color: #fafafa;
        }
      }
    }
  }
}

@media (max-width: 800px) {
  #testframe {
    #frame {
      #mid {
        background: #fafafa;
        flex-direction: column;
        #symbol {
          #symbolframe {
            border: 0;
            margin: 1rem 1rem 0 1rem;
            width: calc(100% - 2rem);
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
        // background: transparent;
        #nextbutton {
          margin-top: 1rem;
          margin-bottom: 1rem;
          padding: 0.75rem 1.5em;
        }
      }
    }
  }
}

#audio {
  background: #00f;
  width: calc(100% - 3rem);
  height: calc(100% - 3rem);
  min-height: 4rem;
  margin: 1.5rem;
  border-radius: 0.25em;
  cursor:pointer;
  position: relative;
  display: flex;
  align-content: center;
  align-items: center;
  text-align: center;
  border: 1px solid #eee;
  #icons {
    position:relative;
    width: 70%;
    padding-top: 70%;
    background: #f3f3f3;
    border-radius: 100%;
    margin: 0 auto;
    border:0;
    box-shadow: 0 0 .5rem rgba(#000,0.2);
    svg {
      width: 60%;
      height: 60%;
      position:absolute;
      left:20%;
      top:20%;
      path, rect {
          fill: #555;
        }
    }
  }
  &:hover {
    #icons {
      background: #fff;
      svg {
        path, rect {
          fill: #000;
        }
      }
    }
  }
  @media (max-width: 800px) {
    margin: 1rem 1rem 0 1rem;
    width: calc(100% - 2rem);
    height: calc(100% - 2rem);
    #icons {
      width: 4rem;
      height: 4rem;
      padding: 0;
      margin: 1rem auto;
      box-shadow: 0 0 4px rgba(#000,0.3);
    }
  }
}
</style>
