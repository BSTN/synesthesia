<template>
  <div id="colorgrid">
    <div id="wrap">
      <div id="grid">
        <button
          v-for="(color, k) in colors"
          :key="color"
          @click="setColor(color, k)"
          :style="{ backgroundColor: '#' + color }"
        ></button>
      </div>
      <div id="nocolor">
        <button
          id="nocolor"
          @click="setNocolor()"
          v-active="q.value === 'nocolor'"
        >
          {{ $t("nocolor") }}
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import { shuffle } from "lodash";
import { mapGetters } from "vuex";
const colors = [
  "fcd731",
  "d80916",
  "915311",
  "a1d255",
  "e26c22",
  "92173d",
  "136825",
  "4db2fc",
  "083d8a",
  "6b3497",
  "0e6b78",
  "f373aa",
  "ffffff",
  "cbcbcb",
  "595959",
  "000000",
];
export default {
  data() {
    return {
      colors: [],
    };
  },
  computed: {
    ...mapGetters({
      testdata: "tests/testdata",
      q: "tests/q",
    }),
  },
  methods: {
    shuffle() {
      this.colors = shuffle(colors);
    },
    async setColor(color, gridposition) {
      console.log(gridposition);
      await this.$store.dispatch("tests/setValue", {
        gridposition: gridposition,
      });
      await this.$store.dispatch("tests/setValue", {
        clicks: this.q.clicks + 1,
      });
      await this.$store.dispatch("tests/setValue", { value: color });
    },
    async setNocolor() {
      await this.$store.dispatch("tests/setValue", {
        clicks: this.q.clicks + 1,
      });
      await this.$store.dispatch("tests/setValue", { value: "nocolor" });
    },
  },
  watch: {
    "q.qnr": function() {
      this.shuffle();
    },
  },
  mounted() {
    this.shuffle();
  },
};
</script>
<style lang="less" scoped>
#colorgrid {
  padding-top: 100%;
  position: relative;
  width: 100%;
  #wrap {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    #grid {
      flex-grow: 1;
      flex-shrink: 1;
      height: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      grid-template-rows: 1fr 1fr 1fr 1fr;
      grid-gap: 1em;
      padding: 1.5em 1.5em 0.5em 0em;
      button {
        width: 100%;
        height: 100%;
        border-radius: 0.25em;
        transition: transform 0.1s;
        &:hover {
          transform: scale(1.1);
        }
      }
    }
    #nocolor {
      flex-grow: 0;
      flex-shrink: 0;
      padding: 1em 0 2em 0;
      text-align: center;
      button {
        margin: 0 auto;
        padding: 0.25em 1em;
        border-radius: 0.25em;
        opacity: 0.5;
        border: 1px solid #ccc;
        margin: 0;
        &:hover {
          opacity: 1;
        }
        &.active {
          background: #333;
          border: 1px solid #333;
          color: #fafafa;
          opacity: 1;
          border-color: @fg;
        }
      }
    }
  }
}
</style>
