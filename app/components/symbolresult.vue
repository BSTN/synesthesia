<template>
  <div id="r">
    <div id="symbol">{{ symbol }}</div>
    <div id="colors">
      <button
        id="color"
        v-for="(c, k) in valuesFiltered"
        :key="k"
        :style="{ backgroundColor: '#' + c }"
      ></button>
    </div>
    <div id="nodistance" v-if="distance === false">
      {{ $t("novalue") }}
    </div>
    <div id="distance" v-if="distance !== false">
      <div id="bar"></div>
      <div id="cutoff"></div>
      <div id="value" :style="{ left: (distance / 6) * 100 + '%' }">
        {{ distance }}
      </div>
    </div>
  </div>
</template>
<script>
import { each } from "lodash";
import color from "color";
export default {
  props: ["symbol", "testname"],
  computed: {
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
    distance() {
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
#r {
  border-top: 1px solid @fg;
  padding: 0 0;
  display: flex;
  font-size: 1.5em;
  line-height: 1.5em;
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
  #colors {
    min-width: 5em;
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
  }
  #distance {
    width: 100%;
    position: relative;
    border: 0.25em solid @bg;
    border-width: 0.25em 0.5em 0.25em 0;
    &:before,
    &:after {
      content: "";
      position: absolute;
      left: 0;
      top: 25%;
      height: 50%;
      border-left: 2px solid @fg;
      opacity: 0.25;
    }
    &:after {
      left: auto;
      right: 0;
    }
    #bar {
      position: absolute;
      left: 0;
      width: 100%;
      top: calc(50% - 1px);
      border-top: 2px solid @fg;
      opacity: 0.25;
    }
    #cutoff {
      position: absolute;
      height: 2px;
      background: @syn;
      top: calc(50% - 1px);
      width: ((100/6) * 1.42%);
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
      &:after {
        content: "";
        position: absolute;
        left: 0;
        height: 100%;
        background: @fg;
        width: 4px;
        border-radius: 0.25em;
        margin-left: 2px;
      }
    }
  }
  @media (max-width: 500px) {
    @s: 10px;
    @ss: @s / 2;
    display: block;
    margin-bottom: 2em;
    display: block;
    overflow: auto;
    border: 0;
    &:last-child {
      border: 0;
    }
    // border: 1px solid @fg;
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
      opacity: 0.25;
      border-radius: 0.25rem;
      position: relative;
      &:after {
        content: "";
        position: absolute;
        left: 0;
        top: calc(50% - @ss);
        width: 100%;
        height: @s;
        background: @fg;
        opacity: 1;
        border-radius: 0.25em;
        z-index: -1;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
      }
    }
    #distance {
      height: 3em;
      display: block;
      overflow: auto;
      border-width: 0.5em 0;
      &:before,
      &:after {
        content: none;
      }

      #bar {
        // border: 1px solid @fg;
        opacity: 0.125;
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
        &:after {
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
}
</style>
