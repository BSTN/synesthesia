<template>
  <div id="score">
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
    <div id="media" v-if="hasAudio || hasAudio">
      <!-- audio -->
      <div id="audio">
        <test-sound
          isolate
          :file="symbol.sound || symbol"
          v-if="hasAudio"
        ></test-sound>
      </div>
      <!-- images -->
      <div id="images" v-if="hasImages">
        <div id="im1" :style="{ backgroundImage: `url(${images[0]})` }" :class="{active: value === images[0]}"></div>
        <div id="im2" :style="{ backgroundImage: `url(${images[1]})` }" :class="{active: value === images[0]}"></div>
      </div>
    </div>
    <div id="frame">
      <div id="slider" v-if="!isNaN(value)">
        <div id="value" :style="{ left: value + '%' }"></div>
        <div id="cutoff" :style="{ width: 100 - cutoff + '%' }"></div>
      </div>
      <div id="text" v-if="text">{{ text }}</div>
    </div>
  </div>
</template>
<script>
import { each } from 'lodash'
import { join } from "path";
import color from "color";
export default {
  props: ["testname", "symbol", "type", "user"],
  computed: {
    testType () {
      if (!this.testname) { return false }
      return this.$tests[this.testname].type
    },
    cutoff () {
      return 70
    },
    value () {
      if (this.testType === 'grapheme') {
        let dist = this.distance()
        if (dist) return 100 - ((dist / 6) * 100)
      }
      return 0
    },
    results () {
      if (this.user) {
        return false
      }
      if (this.symbol) {
        let data = []
        each(this.$store.state.tests.tests[this.testname].questions, q => {
          if (q.symbol === this.symbol) data.push(q.value);
        });
        return data
      }
      return false
    },
    realValue () {
      if (!this.symbol) return false
      return 'this.results'
    },
    title () {
      if (this.type === 'likert') { return 'extra vragen' }
      if (this.symbol) return this.symbol
      if (this.testname === undefined) { return false }
      return this.$tests[this.testname].name[this.$store.state.profile.language]
    },
    username () {
      if (this.user) { return this.user.name}
      return false
    },
    text () {
      return 'Je score is: ' + this.value
    },
    // audio
    hasAudio () {
      if (!this.symbol) { return false }
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
      if (!this.testname || !this.symbol) { return false }
      if (this.$tests[this.testname].type !== "imagesound") return false;
      return [
        join(this.$configbase, "images", this.symbol.im1),
        join(this.$configbase, "images", this.symbol.im2)
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
      let data = [];
      if (!this.symbol) {
        return false
      }
      each(this.$store.state.tests.tests[this.testname].questions, q => {
        if (q.symbol === this.symbol) data.push(q.value);
      });
      // return ["ff0000", "00ff00", "0000ff"];
      // return ["ff0000", "ff0000", "ff0000"];
      return data;
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
      if (this.testType === "imagesound") return false;
      if (!this.values) return false;
      if (this.values.indexOf("nocolor") >= 0) return false;
      if (this.values.indexOf(null) >= 0) return false;
      else {
        let all = this.valuesFiltered.map(x =>
          color("#" + x)
            .rgb()
            .array()
            .map(xx => {
              return parseInt(xx) / 255;
            })
        );
        let distance = 0;
        each(all, (v, k) => {
          let r = Math.abs(v[0] - all[(parseInt(k) + 1) % all.length][0]);
          let g = Math.abs(v[1] - all[(parseInt(k) + 1) % all.length][1]);
          let b = Math.abs(v[2] - all[(parseInt(k) + 1) % all.length][2]);
          distance = distance + r + g + b;
        });
        distance = Math.round(distance * 100) / 100;
        return distance;
      }
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
}
#text {
  margin-top: 0.25em;
  font-size: 0.8em;
  opacity: 0.5;
}
#media {
  background: @fg;
  display:flex;
  > div {
    width: 100%;
  }
}
#frame {
  padding: 0.5em 1em;
}
#slider {
  position: relative;
  height: 1rem;
  width: 100%;
  overflow: hidden;
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
}
#value {
  position: absolute;
  height: 100%;
  z-index: 1;
  transition: all .5s @easeInOutExpo;
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
  }
}
#cutoff {
  background: @syn;
  position: absolute;
  border-radius: 0 0.25em 0.25em 0;
  right: 0;
  width: 100%;
  height: @s;
  top: calc(50% - @ss);
}

#audio {
  text-align: center;
  /deep/ #sound {
    width: 4rem;
    height: 4rem;
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
  padding: 0 1rem;
  flex-grow: 1;
  align-items: center;

  > * {
    height: 2rem;
    width: 100%;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: .25;

    &:first-child {
      margin-right: 1rem;
    }
    &.active {
      opacity: 1;
    }
  }

  #imagesoundresult {
    width: 100%;
  }
}
</style>
