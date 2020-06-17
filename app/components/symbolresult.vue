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
    <div id="distance">{{ distance }}</div>
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
      return data;
    },
    valuesFiltered() {
      return this.values.filter((x) => x !== "nocolor");
    },
    distance() {
      if (this.values.indexOf("nocolor") >= 0) return false;
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
        return distance;
      }
      // var c1 = rgb0(hex2rgb(colors[0])); // convert hex to list of [0.0 - 1.0] rgb values
      // var c2 = rgb0(hex2rgb(colors[1])); // convert hex to list of [0.0 - 1.0] rgb values
      // var c3 = rgb0(hex2rgb(colors[2])); // convert hex to list of [0.0 - 1.0] rgb values
      // var r = Math.abs(c1[0]-c2[0]) + Math.abs(c2[0]-c3[0]) + Math.abs(c3[0]-c1[0]);
      // var g = Math.abs(c1[1]-c2[1]) + Math.abs(c2[1]-c3[1]) + Math.abs(c3[1]-c1[1]);
      // var b = Math.abs(c1[2]-c2[2]) + Math.abs(c2[2]-c3[2]) + Math.abs(c3[2]-c1[2]);
      // return [r,g,b,(r+g+b)];
    },
  },
};
</script>
<style lang="less" scoped>
#r {
  border-top: 1px solid @fg;
  padding: 0.25em 0;
  display: flex;
  &:last-child {
    border-bottom: 1px solid @fg;
  }
  #symbol {
    min-width: 3em;
    text-align: center;
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
}
</style>
