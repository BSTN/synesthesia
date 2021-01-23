<template>
  <div id="r">
    <test-sound isolate :file="symbol.sound || symbol" v-if="testConfig.type === 'audio' || testConfig.type === 'imagesound'"></test-sound>
    <div id="symbol" v-else>
      {{ translatedSymbol }}
    </div>
    <div id="colors" v-if="testConfig.type !== 'imagesound'">
      <button
        id="color"
        v-for="(c, k) in valuesFiltered"
        :key="k"
        :style="{ backgroundColor: '#' + c }"
      ></button>
    </div>
    <div id="images" v-if="testConfig.type === 'imagesound'">
      <div id="im1" :style="{backgroundImage: `url(${images[0]})`}"></div>
      <div id="imagesoundresult" v-if="testConfig.type === 'imagesound'">
        <!-- {{imagesoundValues}} -->
      </div>
      <div id="im2" :style="{backgroundImage: `url(${images[1]})`}"></div>
    </div>
    <div id="nodistance" v-if="distance === false && testConfig.type !== 'imagesound'">
      {{ $t("novalue") }}
    </div>
    <div id="distance" v-if="distance !== false">
      <div id="bar"></div>
      <div id="cutoff" :style="{ width: cutoffwidth }"></div>
      <div id="value" :style="{ left: (distance / 6) * 100 + '%' }">
        {{ distance }}
      </div>
    </div>
  </div>
</template>
<script>
import { each } from "lodash";
import color from "color";
import { join } from 'path'
export default {
  props: ["symbol", "testname"],
  computed: {
    testConfig() {
      return this.$tests[this.testname];
    },
    cutoffwidth() {
      return (100 / 6) * parseFloat(this.testConfig.cutoff) + "%";
    },
    translatedSymbol() {
      if (!this.symbol || typeof this.symbol !== 'string') return false
      return this.symbol.match(/^t\:/)
        ? this.$t(this.symbol.replace(/^t\:/, ""))
        : this.symbol;
    },
    imagesoundValues () {
      let data = [];
      each(this.$store.state.tests.tests[this.testname].questions, (q) => {
        if (q.symbol === this.symbol) data.push(q.value);
      });
      return data
    },
    values() {
      let data = [];
      each(this.$store.state.tests.tests[this.testname].questions, (q) => {
        if (q.symbol === this.symbol) data.push(q.value);
      });
      // return ["ff0000", "00ff00", "0000ff"];
      // return ["ff0000", "ff0000", "ff0000"];
      return data;
    },
    valuesFiltered() {
      return this.values.filter((x) => x !== "nocolor");
    },
    images () {
      if (this.testConfig.type !== 'imagesound') return false;
      return [join(this.$configbase, 'images', this.symbol.im1), join(this.$configbase, 'images', this.symbol.im2)]
    },
    distance() {
      if (this.testConfig.type === 'imagesound') return false;
      if (this.values.indexOf("nocolor") >= 0) return false;
      if (this.values.indexOf(null) >= 0) return false;
      else {
        let all = this.valuesFiltered.map((x) =>
          color("#" + x)
            .rgb()
            .array()
            .map((xx) => {
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
    },
  },
};
</script>
<style lang="less" scoped>

@g: var(--grey);

#r {
  --bg: #fafafa;
  --fg: #555;
  --grey: #ccc;
  --syn: #0874b3;

  background: @bg;
  color: @fg;
  padding: 0.5em;
  display: flex;
  font-size: 1.5em;
  line-height: 1.5em;
  overflow: hidden;
  max-width: calc(100% - 1em);
  margin: 0 auto;
  margin-bottom: 0.125em;
  border-radius: 0.125em;

  @media (max-width: 800px) {
    font-size: 1em;
  }

  &:last-child {
    border-bottom: 1px solid @fg;
  }

  #symbol {
    min-width: 7em;
    text-align: left;
    padding: 0 0.5em;
  }

  /deep/ #sound {
    min-height: 2rem;
    width: 3rem;
    flex-grow: 0;
    flex-shrink: 0;

    #icons {
      max-height: 3rem;
      max-width: 3rem;
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
      margin: 0 0.25em 0 0;
      vertical-align: middle;
    }
  }

  #nodistance {
    opacity: 0.5;
    font-size: 0.5em;
    flex-grow: 1;
  }
}

#images {
  display: flex;
  padding: 0 1rem;
  flex-grow: 1;

  > * {
    height: 2rem;
    width: 2rem;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;

    &:first-child {
      margin-right: 1rem;
    }
  }

  #imagesoundresult {
    width: 100%;
  }
}

#distance {
  width: 100%;
  position: relative;
  border: 0.25em solid @bg;
  border-width: 0.25em 0.5em 0.25em 0;

  &::before,
  &::after {
    content: "";
    position: absolute;
    left: -0.125em;
    top: 25%;
    height: 50%;
    border-left: 0.125em solid @g;
    opacity: 1;
    border-radius: 0.125em;
  }

  &::before {
    border-left-color: @syn;
  }

  &::after {
    left: auto;
    right: -0.125em;
  }

  #bar {
    position: absolute;
    left: 0;
    width: 100%;
    top: calc(50% - 0.0625em);
    border-top: 0.125em solid @g;
    opacity: 1;
  }

  #cutoff {
    position: absolute;
    height: 0.125em;
    background: @syn;
    top: calc(50% - 0.06125em);
    // width: ((100/6) * 1.42%);
  }

  #value {
    position: absolute;
    height: 100%;
    // border-left: 4px solid @fg;
    top: 0;
    left: 0;
    padding-left: 1em;
    font-size: 11px;
    line-height: 1em;
    transition: 2s;

    &::after {
      content: "";
      position: absolute;
      left: 0;
      height: 100%;
      background: @fg;
      width: 0.25rem;
      border-radius: 0.25rem;
      margin-left: 0.125rem;
    }
  }
}

@media (max-width: 500px) {
  @s: 10px;
  @ss: @s / 2;

  #r {
    display: block;
    overflow: auto;
    border: 0.5em solid @bg;
  }

  #symbol {
    text-align: left;
    display: inline-block;
    min-width: none;
    padding: 0;
  }

  #colors {
    text-align: center;
    float: right;
    min-width: auto;
  }

  #nodistance {
    text-align: center;
    opacity: 1;
    color: @bg;
    border-radius: 0.25rem;
    position: relative;

    &::after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: @fg;
      opacity: 1;
      border-radius: 0.25em;
      z-index: -1;
    }
  }

  #distance {
    height: 3em;
    display: block;
    overflow: auto;
    border-width: 0.5em 0;

    &::before,
    &::after {
      content: none;
    }

    #bar {
      // border: 1px solid @fg;
      opacity: 1;
      background: @fg;
      top: calc(50% - @ss);
      left: 0;
      height: @s;
      width: 100%;
      border-radius: 0.25em;
    }

    #cutoff {
      border: 0;
      // border-right: 2px solid @syn;
      height: @s;
      background: transparent;
      top: calc(50% - @ss);

      &::after {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: @syn;
        border-radius: 0.25em 0 0 0.25em;
      }
    }
  }
}
</style>
