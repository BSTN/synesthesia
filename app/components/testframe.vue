<template>
  <div id="testframe">
    <div id="frame">
      <div id="top">
        <transition name="symbol" mode="out-in">
          <div id="symbol" :key="$store.state.test.position">
            <div id="symbolframe" :style="{ background: background }">
              <!-- <div id="info">
              Försök att så instinktivt som möjligt välja den färg du förknippar
              med, eller känner passar bäst ihop med:
            </div> -->
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
        <button id="helpbutton">help</button>
        <div class="flex"></div>
        <button id="midbutton" class="inactive" v-if="q.color === null">
          Försök att så instinktivt som möjligt välja den färg du förknippar
          med, eller känner passar bäst ihop med: {{ q.symbol }}
        </button>
        <button id="midbutton" @click="next()" v-if="q.color !== null">
          next
        </button>
        <div class="flex"></div>
        <div id="count">{{ position + 1 }}/49</div>
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
  },
  methods: {
    next() {
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
  display: block;
  border-radius: 0.5em;
  overflow: hidden;
  user-select: none;
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
      display: flex;
      flex-direction: row;
      min-height: 100%;
      flex-grow: 1;
      flex-shrink: 1;
      align-items: stretch;
      @media (orientation: portrait) {
        flex-direction: column;
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
        #symbolframe {
          width: calc(100% - 2rem);
          height: calc(100% - 2rem);
          display: flex;
          align-content: center;
          align-items: center;
          justify-content: center;
          // background: @bg3;
          border-radius: 0.25rem;
          #info {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1rem;
            font-size: 0.7em;
            line-height: 1.25em;
            text-align: center;
            color: @fg2;
          }
          #sym {
            font-size: 4rem;
            display: flex;
            align-content: center;
            align-items: center;
            justify-content: center;
            padding: 0.5em 1rem;
            background: @bg;
            margin: 2rem;
            min-width: calc(100% - 8rem);
            min-height: calc(100% - 8rem);
            border-radius: 0.25rem;
          }
        }
      }
      #color {
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
      padding: 0 1em;
      background: @bg3;

      button {
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
</style>
