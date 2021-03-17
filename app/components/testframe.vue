<template>
  <div id="testframe" :class="config.type + '-type'">
    <div id="frame">
      <div id="mid">
        <div id="leftframe" class='grapheme' v-if="config.type === 'grapheme'">
          <test-symbol :q="q" :background="background" :testdata="testdata" :symbol="symbol"></test-symbol>
        </div>
        <div id="leftframe" class='audio' v-if="config.type === 'audio'">
          <test-sound :background="background" :file="symbol" autoplay></test-sound>
        </div>
        <test-imagesound :config="config" :q="q" :testdata="testdata"></test-imagesound>
        <div id="color" v-if="config.type !== 'imagesound'">
          <test-colorpicker v-if="config.selector === 'colorpicker'"></test-colorpicker>
          <test-colorgrid v-if="config.selector === 'colorgrid'"></test-colorgrid>
        </div>
      </div>
      <div id="bottom">
        <div id="progress"><div id="bar" :style="{width: (progress * 100) + '%'}"></div></div>
        <div id="help">{{ config.help[$store.state.profile.language] }}</div>
        <button id="nextbutton" @click="next()" v-active="q.value !== null">
          {{ $t("next") }}
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import { now, filter } from "lodash";
import {join} from 'path'
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
      return (this.testdata.position + 1) / Object.keys(this.testdata.questions).length;
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
  },
  watch: {
    "q.qnr": function() {
      // reset timer
      this.startTime = now();
    },
  },
  mounted() {
    const self = this
    // this.checkFinished();
    this.startTime = now();
    const keydown = function (ev) {
      if (ev.key === "Enter" && self.q.value !== null) self.next();
    }
    window.addEventListener("keydown", keydown);
    this.$on('hook:beforeDestroy', () => {
      window.removeEventListener("keydown", keydown)
    })
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
  max-width: 40rem;
  width: 100%;
  margin: 1rem auto;
  position: relative;
  display: block;
  border-radius: 0.5em;
  overflow: hidden;
  user-select: none;
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
  #leftframe.audio {
    border: 1.5rem solid transparent;
  }
}

#bottom {
  min-height: 3.5rem;
  flex-shrink: 0;
  flex-grow: 0;
  display: flex;
  align-content: center;
  align-items: center;
  padding: 1rem 1.5rem 1.5rem;
  background: #d6d6d6;
  background: #e9e9e9;
  background: #f3f3f3;
  position: relative;
  #progress {
    position:absolute;
    top:0;
    width: calc(100% - 3rem);
    left: 1.5rem;
    height: 2px;
    border-radius: 0.2rem;
    overflow: hidden;
    background: #ddd;
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
    // font-style: italic;
    pointer-events: none;
    line-height: 1.25em;
    font-size: 0.75em;
    width: 100%;
    flex-shrink: 1;
    flex-grow: 1;
    text-align: left;
    padding-right: 1.5rem;
    color: #999;
  }
  #nextbutton {
    flex-grow:0;
    flex-shrink:0;
    padding: 1em 1.5em .7em;
    border-radius: 0.25em;
    cursor: pointer;
    opacity: 1;
    pointer-events: auto;
    color: #fafafa;
    color: #bbb;
    pointer-events: none;
    line-height: 1;
    font-size: 0.75rem;
    // border: 2px solid #eee;
    color: #ddd;
    text-shadow: 0.025rem -0.025rem 0.0125rem rgba(#000,0.3), -0.025rem 0.025rem 0.0125rem rgba(#fff,0.9);
    box-shadow: 
      0.05rem -0.05rem 0.05rem rgba(#000,0.1), 
      inset -0.05rem 0.05rem 0.05rem rgba(#fff,.7),
      inset 0.05rem -0.05rem 0.05rem rgba(#000,0.1), 
      -0.05rem 0.05rem .05rem rgba(#fff,.7);
    text-transform: uppercase;
    white-space: nowrap;
    transition: all 0.15s @easeInOutExpo;
    &.active {
      pointer-events: auto;
      background: #fafafa;
      background: linear-gradient(to bottom left, #fafafa, #eee);
      color: #999;
      text-shadow:-0.025rem 0.025rem 0.0125rem rgba(#fff,0.9);
      box-shadow: 
        0.05rem -0.05rem 0.05rem rgba(#000,0.1), 
        inset 0.05rem -0.05rem 0.05rem rgba(#000,0.1), 
        inset -0.05rem 0.05rem 0.05rem rgba(#fff,.7),
        -0.05rem 0.05rem 0.05rem rgba(#fff,.7);

      &:hover {
        background: #fff;
        background: linear-gradient(to bottom left, #fff, #eee);
        color: #555;
        box-shadow: 
          0.05rem -0.05rem 0.05rem rgba(#000,0.15), 
          inset 0.05rem -0.05rem 0.05rem rgba(#000,0.15), 
          inset -0.05rem 0.05rem 0.05rem rgba(#fff,.7),
          -0.05rem 0.05rem 0.05rem rgba(#fff,.7);
      }
      &:active {
        background: #ddd;
        box-shadow: 
          0.05rem -0.05rem 0.05rem rgba(#000,0.4), 
          inset 0.05rem -0.05rem 0.05rem rgba(#000,0.15), 
          inset 0rem 0rem 0.25rem rgba(#000,.3),
          -0.05rem 0.05rem 0.05rem rgba(#fff,1);
      }
    }
  }
}

@media (max-width: 50rem) {
  #testframe {
    max-width: 25rem !important;
    min-height: 100vh;
    border-radius: 0.5rem;
    margin: 1rem auto;
  }
  #mid {
    font-size: 1.5em;
    // background: #fafafa;
    flex-direction: column;
    max-height: none;
    .audio-type & {
    }
    #color {
      /deep/ #colorpicker {
        border: 1rem solid transparent !important;
      }
    }
    #leftframe.audio {
      width: 100%;
      border: 0;
      margin-top: 1rem;
    }
  }
  #bottom {
    flex-direction: column;
    padding: 1rem;
    #progress {
      left: 1rem; 
      width: calc(100% - 2rem);
    }
    #help {
      padding:0;
    }
    #nextbutton {
      margin-top: 1.5rem;
      margin-bottom: .5rem;
    }
  }
}
@media (max-width: 38rem) {
  #testframe {
    margin: 0 auto;
    border-radius: 0;
  }
}
@media (max-width: 30rem) {
  #mid {
    font-size: 1em;
  }
}
</style>
