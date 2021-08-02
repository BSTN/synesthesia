<template>
  <div id="score" :class="{synesthesia: hasSynesthesia, nodata: realValue === false}">
    <div id="titlebar" v-if="(title && !hasAudio) || username">
      <div id="title" v-if="title && !hasAudio">{{ title }}</div>
      <div id="username" v-if="username">{{ username }}</div>
      <!-- colors -->
      <div id="colors" v-if="hasColors">
        <button
          id="color"
          v-for="(c, k) in valuesFilteredDisplay"
          :key="k"
          :style="c"
          :class="{ nocolor: !!c.backgroundImage }"
        ></button>
      </div>
    </div>
    <div id="media" v-if="hasAudio" :class="{hasColors}">
      <!-- images -->
      <div id="images" v-if="hasImages">
        <!-- <div id="im1" :style="{ backgroundImage: `url(${images[0]})` }" :class="{active: value === images[0]}"></div> -->
        <div id="im2" :class="{active: value === images[0]}">
          <img :src="images[1]">
        </div>
      </div>
      <!-- audio -->
      <div id="audio">
        <test-sound
          isolate
          :file="data.symbol.sound || data.symbol"
          v-if="hasAudio"
        ></test-sound>
        <div id="soundfilename">{{data.symbol.sound || data.symbol}}</div>
      </div>
      <!-- images -->
      <div id="images" v-if="hasImages">
        <div id="im1" :class="{active: value === images[0]}">
          <img :src="images[0]">
        </div>
        <!-- <div id="im2" :style="{ backgroundImage: `url(${images[1]})` }" :class="{active: value === images[0]}"></div> -->
      </div>
      <!-- colors -->
      <div id="colors" v-if="hasColors">
        <button
          id="color"
          v-for="(c, k) in valuesFilteredDisplay"
          :key="k"
          :style="c"
          :class="{ nocolor: !!c.backgroundImage }"
        ></button>
      </div>
    </div>
    <div id="frame">
      <!-- {{cutoff}} -->
      <div id="slider" v-if="!isNaN(value) && realValue !== false">
        <div id="value" :style="{ left: value + '%' }"></div>
        <div id="cutoff" :style="{ width: 100 - cutoff + '%' }" v-if="cutoff" :class="{reverse}"></div>
      </div>
      <div id="text" v-if="text">{{ text }}</div>
    </div>
  </div>
</template>
<script>
import { each } from 'lodash'
import { join } from "path";
import score from '../utils/score'
export default {
  props: ["testname", "symbol", "type", "user", "data"],
  computed: {
    testType () {
      if (!this.testname) { return false }
      return this.$tests[this.testname].type
    },
    hasSynesthesia () {
      if (this.type !== 'likert') { 
        return this.value > this.cutoff
      } else {
        if (!this.realValue) return false
        /// somewhere here!
        if (!this.$tests[this.testname]) return false
        return this.realValue <= this.$tests[this.testname].cutoff
      }
    },
    cutoff () {
      // percentage
      if (this.type === 'likert') {
        return (18 / 30) * 100
      } else if (this.$tests[this.testname].type === 'imagesound') {
        return false
      } else if (this.$tests[this.testname].type === 'grapheme' && ((this.data.data && this.data.data.length === 2) || this.data.hasDoubles)) {
        // in case grapheme symbol with only 2 values
        if (this.data.hasDoubles) {
          return (this.$tests[this.testname].cutoff / Object.keys(this.data.symbols).length) * 100
        }
        return this.$tests[this.testname].cutoff * 100
      } else {
        let cutoff = this.$tests[this.testname].cutoff
        if (this.data === false) {
          return 0
        } else {  
          return ((6 - cutoff) / 6) * 100
        }
      }
    },
    reverse () {
      if (this.type === 'likert') return true
      if (this.$tests[this.testname].type === 'grapheme' && ((this.data.data && this.data.data.length === 2) || this.data.hasDoubles)) { return false }
      return !(["imagesound","grapheme","audio"].indexOf(this.$tests[this.testname].type) >= 0)
    },
    value () {
      // turn into forward percentage
      // total
      if (this.type === 'total') {
        if (this.data.total === undefined || isNaN(this.data.total)) return false
        if (this.data.hasDoubles) {
          return (this.data.total / Object.keys(this.data.symbols).length) * 100
        }
        return ((6 - this.data.total) / 6) * 100
      }
      // likert
      if (this.type === 'likert') {
        return (parseInt(this.data) / 30) * 100
      }
      // no data
      if (!this.data || !this.data.score) return false
      
      // imagesound
      if (this.$tests[this.testname].type === 'imagesound') {
        if (isNaN(this.data.score)) return false
        return this.data.score * 100
      }
      // if a symbol
      if (this.data.hasDoubles || (this.data.data && this.data.data.length === 2)) {
        return this.data.score * 100
      }
      return ((this.data.score / 6) * 100)
      // return ((6 - this.data.score) / 6) * 100
    },
    realValue () {
      if (!this.data) return false
      if (this.type === 'total') {
        if (this.data.total === undefined || isNaN(this.data.total)) {
          return false
        } else {
          if (this.data.hasDoubles) {
            return this.data.total
          }
          return this.data.total === 0 ? 0 : parseInt(this.data.total * 100) / 100
        }
      }
      // likert
      if (this.type === 'likert') {
        return parseInt(this.data)
      }
      if (this.testType === 'imagesound') {
        return this.data.score
      }
      // no data
      if (!this.data || isNaN(this.data.score)) return false
      // if a symbol
      return this.data.score
    },
    title () {
      if (this.type === 'likert') { return 'extra vragenlijst' }
      if (this.data.symbol) {
        if (typeof this.data.symbol === 'string' && this.data.symbol.startsWith('t:')) {
          return this.$t(this.data.symbol.replace('t:',''))
        }
        return this.data.symbol
      }
      if (this.testname === undefined) { return false }
      return this.$tests[this.testname].name[this.$store.state.profile.language]
    },
    username () {
      if (this.user) { return this.user.name}
      return false
    },
    text () {
      if (this.type === 'likert' && !this.data) {
        return this.$t('novalue');
      }
      if (this.realValue === false) return this.$t('novalue')
      return 'Score: ' + this.realValue
    },
    // audio
    hasAudio () {
      if (!this.data.symbol) { return false }
      if (this.testname !== undefined) {
        if (['audio', 'imagesound'].indexOf(this.$tests[this.testname].type) > -1) {
          return true
        }
      }
      return false
    },
    // images
    hasImages () {
      if (this.testname !== undefined) {
        if (['imagesound'].indexOf(this.$tests[this.testname].type) > -1) {
          return true
        }
      }
      return false
    },
    images() {
      if (!this.testname || !this.data.symbol) { return false }
      if (this.$tests[this.testname].type !== "imagesound") return false;
      return [
        join(this.$configbase, "images", this.data.symbol.im1),
        join(this.$configbase, "images", this.data.symbol.im2)
      ];
    },
    // colours
    hasColors () {
      if (this.type === 'likert') {
        return false
      }
      if (this.$tests[this.testname].type === 'imagesound') {
        return false
      }
      return true
    },
    values() {
      if (!this.data || !this.data.data) return false
      return this.data.data.map(x => x.value)
    },
    valuesFiltered() {
      return this.values.filter(x => x !== "nocolor");
    },
    valuesFilteredDisplay() {
      // return this.values.filter(x => x !== "nocolor");
      if (!this.values) return false
      return this.values.map(x => {
        if (x === "nocolor")
          return { backgroundImage: `url("./assets/nocolor.png")` };
        return { background: `#${x}` };
      });
    },
  },
  methods: {
    distance() {
      return score.distance(this.testType, this.values)
    }
  }
};
</script>
<style lang="less" scoped>
@s: 10px;
@ss: 5px;
#score {
  margin-bottom: 1rem;
  border: 1px solid @fg;
  border-radius: 0.25em;
  width: 100%;
  -webkit-print-color-adjust:exact;
  @media print {
    border: 1px solid #ccc;
    --fg: #555;
    --bg: #fff;
    break-inside: avoid;
    display:inline-block;
    max-width: 20rem;
    margin-right: 1rem;
  }
}
#titlebar {
  display: flex;
  padding: 0.25em 1em;
  background: @fg;
  color: @bg;
  #title {
    text-transform: capitalize;
    flex-grow:1;
  }
  #username {
    opacity: 0.5;
    font-size: 0.8em;
    text-transform: uppercase;
  }
  @media print {
    background: #eee;
    color: #000;
    box-shadow: inset 0 0 0 1000px #eee;
  }
}
#text {
  margin-top: 0.25em;
  font-size: 0.8em;
  @media print {
    color: #000;
  }
}
#media {
  background: @fg;
  display:flex;
  > div {
    width: 100%;
  }
  &.hasColors {
    justify-content: space-between;
    > div {
      width: auto;
    }
    > div:last-child {
      width: 5rem;
    }
  }
}
#frame {
  padding: 0.5em 1em;
}
#slider {
  position: relative;
  height: 1rem;
  width: 100%;
  overflow: visible;
  margin-top: 1em;
  &:before {
    content: "";
    position: absolute;
    border-radius: 0.25em;
    left: 0;
    height: @s;
    top: calc(50% - @ss);
    width: 100%;
    background: @fg;
    opacity: 0.125;
  }
  @media print {
    box-shadow: 0 0 5px #ccc;
  }
}
#value {
  position: absolute;
  height: 100%;
  z-index: 1;
  transition: all 2.5s @easeInOutExpo;
  transition-delay: .15s;
  &:before {
    content: "";
    position: absolute;
    left: -2px;
    top: 0;
    height: 100%;
    width: 4px;
    background: @fg;
    border-radius: 0.25em;
    @media print {
      box-shadow: inset 0 0 0 1000px #000;
    }
  }
  .synesthesia & {
    &:before {
      background: @fg;
    }
  }
}
#cutoff {
  background: @syn;
  background: @fg;
  position: absolute;
  border-radius: 0.25em 0 0 0.25em;
  left: 0;
  width: 100%;
  height: @s;
  top: calc(50% - @ss);
  opacity: 0.5;
  &.reverse {
    right: 0;
    left: auto;
    border-radius: 0 0.25em 0.25em 0;
  }
  @media print {
    background: #ccc;
    opacity: 1;
    box-shadow: inset 0 0 0 1000px #ccc;
  }
}

#audio {
  text-align: center;
  #soundfilename {
    display:none;
  }
  @media print { 
    #soundfilename {
      display:block;
      padding: 0.5em;
    }
    /deep/ #sound {
      display:none;
    }
  }
  /deep/ #sound {
    width: 4rem;
    height: 4rem;
  }
  @media (max-width: 40rem) {
    /deep/ #sound {
      margin: 0.5rem;
      min-height: auto;
      max-height: 2rem;
      min-width: none;
      max-width: 2rem;
    }
    /deep/ #icons {
      margin: 0 auto !important;
      width: 2rem !important;
      height: 2rem !important;
      border: 0.15rem solid transparent !important;
      box-shadow:
      0.025rem -0.025rem 0.05rem rgba(#000, 0.4),
      inset 0.05rem -0.05rem 0.05rem rgba(#000, 0.4),
      inset -0.05rem 0.05rem 0.05rem rgba(#fff, 0.1),
      -0.05rem 0.05rem 0.05rem rgba(#fff, 0.1) !important;
    }
  }
}

#colors {
  min-width: 5em;
  flex-grow: 0;
  flex-shrink: 0;
  text-align: center;

  button {
    width: 1rem;
    height: 1rem;
    border-radius: 100%;
    display: inline-block;
    padding: 0;
    margin: 0 0 0 0.5em;
    vertical-align: middle;
    background-size: 10px;
    background-repeat: repeat;
    &.nocolor {
      box-shadow: inset 0 0 0.125rem rgba(#000, 0.3);
    }
  }
}

#images {
  display: flex;
  padding: 0;
  flex-grow: 1;
  align-items: center;

  > * {
    height: 2rem;
    width: 100%;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: .25;
    position: relative;
    img {
      object-fit: contain;
      object-position: center;
      position:absolute;
      left:0;
      top:0;
      width:100%;
      height:100%;
    }

    // &:first-child {
    //   margin-right: 1rem;
    // }
    &.active {
      opacity: 1;
    }
  }

  #imagesoundresult {
    width: 100%;
  }
}

@synhighlight: #feffc5;
// @synhighlight: #fff;
.synesthesia {
  --fg: @synhighlight;
  --glow: rgba(@synhighlight, 0.25);
  box-shadow: 0 0 1rem var(--glow);
  @media print {
    --fg: #000;
  }
}

.nodata {
  opacity: 0.5;
}
</style>
