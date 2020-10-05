<template>
  <div id="colorpicker">
    <div id="hue">
      <div
        id="hueframe"
        :style="{ backgroundColor: lightnessBackgroundColor }"
        ref="hue"
      >
        <div
          id="huebg"
          ref="huebg"
          :style="{
            opacity: lightnessOpacity,
            backgroundPosition: huebgpos,
          }"
        ></div>
        <div
          id="huehandle"
          :style="{
            left: posx * 100 + '%',
            top: posy * 100 + '%',
            borderColor: selectorColor,
          }"
        ></div>
      </div>
    </div>
    <div id="slider">
      <vue-slider
        ref="slider"
        v-model="lightness"
        v-bind="sliderOptions"
        @change="changeSlider"
      ></vue-slider>
      <div id="labels">
        <label>{{ $t("darker") }}</label>
        <label>{{ $t("lighter") }}</label>
      </div>
    </div>
    <div id="nocolor">
      <button
        @click="toggleNocolor()"
        :class="{ active: q.value === 'nocolor' }"
      >
        {{ $t("nocolor") }}
      </button>
    </div>
  </div>
</template>
<script>
import color from "color";
import { clamp } from "lodash";
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      lightness: 0.5,
      mousedown: false,
      dragging: false,
      posx: null,
      posy: null,
      offset: null,
      sliderOptions: {
        dotSize: 14,
        width: "auto",
        height: 4,
        contained: false,
        direction: "ltr",
        data: null,
        min: 0,
        max: 1,
        interval: 0.001,
        disabled: false,
        clickable: true,
        duration: 0.01,
        adsorb: false,
        lazy: false,
        tooltip: "none",
        tooltipPlacement: "top",
        tooltipFormatter: void 0,
        useKeyboard: false,
        keydownHook: null,
        dragOnClick: false,
        enableCross: true,
        fixed: false,
        minRange: void 0,
        maxRange: void 0,
        order: true,
        marks: false,
        dotOptions: void 0,
        process: true,
        dotStyle: void 0,
        railStyle: void 0,
        processStyle: void 0,
        tooltipStyle: void 0,
        stepStyle: void 0,
        stepActiveStyle: void 0,
        labelStyle: void 0,
        labelActiveStyle: void 0,
      },
    };
  },
  computed: {
    ...mapGetters({
      testdata: "tests/testdata",
      q: "tests/q",
    }),
    lightnessBackgroundColor() {
      let L = parseInt(this.lightness * 256);
      return "rgb(" + L + "," + L + "," + L + ")";
    },
    lightnessOpacity() {
      return this.lightness <= 0.5
        ? this.lightness * 2
        : (2 - this.lightness * 2) % 1;
    },
    selectorColor() {
      return this.lightness <= 0.75 ? "#ffffff" : "#333333";
    },
    clicks() {
      return parseInt(this.q.clicks);
    },
    huebgpos() {
      let w = 0;
      if (this.$refs["huebg"]) w = this.$refs["huebg"].clientWidth;
      return `${this.offset * w}px 0px`;
    },
  },
  watch: {
    "q.qnr": function(val) {
      this.lightness = 0.5;
      this.randomPos();
    },
  },
  methods: {
    randomPos() {
      this.offset = Math.random();
      this.posx = Math.random();
      this.posy = Math.random();
    },
    async setColor(val) {
      let c, h, s;
      h = (this.posx * 360 + (1 - this.offset) * 360) % 360;
      s = (1 - this.posy) * 100;
      let hex = color
        .hsl(h, s, val * 100)
        .hex()
        .replace("#", "");
      await this.$store.dispatch("tests/setValue", { value: hex });
    },
    async toggleNocolor() {
      await this.$store.dispatch("tests/setValue", {
        clicks: this.q.clicks + 1,
      });
      if (this.q.color !== "nocolor") {
        await this.$store.dispatch("tests/setValue", { value: "nocolor" });
      } else {
        let h, s, l;
        h = (this.posx * 360 + (1 - this.offset) * 360) % 360;
        s = (1 - this.posy) * 100;
        l = this.lightness;
        let hex = color
          .hsl(h, s, l * 100)
          .hex()
          .replace("#", "");
        await this.$store.dispatch("tests/setValue", { value: hex });
      }
    },
    addSliderClick() {
      this.$store.dispatch("tests/setValue", {
        clicksslider: parseInt(this.q.clicksslider) + 1,
      });
    },
    changeSlider(val) {
      this.setColor(val);
      this.addSliderClick();
    },
  },
  mounted() {
    let hueel = this.$refs.hue;
    let mousedown = false;
    const huepos = async (ev) => {
      if (ev.type === "mousedown") {
        await this.$store.dispatch("tests/setValue", {
          clicks: this.clicks + 1,
        });
        mousedown = true;
      }
      if (ev.type === "touchstart") {
        this.$store.dispatch("tests/setValue", { clicks: this.clicks + 1 });
        mousedown = true;
      }
      if (mousedown) {
        let coor = hueel.getBoundingClientRect();
        this.posx = clamp((ev.clientX - coor.x) / coor.width, 0, 1);
        this.posy = clamp((ev.clientY - coor.y) / coor.height, 0, 1);
        await this.$store.dispatch("tests/setValue", {
          position: `${this.posx},${this.posy}`,
        });
        let hex = color
          .hsl(
            (this.posx * 360 + (1 - this.offset) * 360) % 360,
            (1 - this.posy) * 100,
            this.lightness * 100
          )
          .hex()
          .replace("#", "");
        this.$store.dispatch("tests/setValue", { value: hex });
      }
      if (ev.type === "mouseup") mousedown = false;
    };
    hueel.addEventListener("mousedown", huepos);
    // window.addEventListener("mousemove", huepos);
    // hueel.addEventListener("mouseup", huepos);
    this.randomPos();
  },
};
</script>
<style lang="less" scoped>
#colorpicker {
  width: 100%;
  display: flex;
  flex-direction: column;
  border: 1.5rem solid transparent;
  border-left: 0;
  #hue {
    padding-top: 66%;
    position: relative;
    max-height: 2rem;
    overflow: hidden;
    #hueframe {
      position: absolute;
      top: 0;
      left: 0;
      width: calc(100%);
      height: calc(100%);
      border-radius: 0.25em;
      #huebg {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-image: url("~assets/hue.png");
        background-size: 100% 100%;
        background-position: left 30px top 0;
        background-repeat: repeat;
        border-radius: 0.25em;
        transition: background-position 0.2s;
      }
      #huehandle {
        position: absolute;
        top: 0;
        left: 0;
        width: 0.5rem;
        height: 0.5rem;
        border: 3px solid #fff;
        border-radius: 100%;
        transform: translate(-0.25rem, -0.25rem);
      }
    }
  }
  #slider {
    min-height: 2rem;
    position: relative;
    // see vue-slider.less for slider layout
    padding-top: 1rem;
    margin-bottom: 0.5rem;
    .vue-slider {
      margin-bottom: 0;
    }
    #labels {
      line-height: 1em;
      label {
        opacity: 0.5;
        font-size: 0.5em;
        display: inline-block;
        // text-transform: uppercase;
        float: left;
        &:nth-child(2) {
          float: right;
        }
      }
    }
  }
  #nocolor {
    // min-height: 2.5rem;
    text-align: center;
    button {
      background: #d6d6d6;
      padding: 0.25em 1em;
      border-radius: 0.25em;
      opacity: 0.5;
      border: 1px solid #ccc;
      margin: 0;
      &:hover {
        opacity: 1;
      }
      &.active {
        border: 1px solid #333;
        background: #333;
        color: #fafafa;
        opacity: 1;
        border-color: @fg;
      }
    }
  }
}
</style>
