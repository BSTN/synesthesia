<template>
  <div id="question">
    <slot></slot>
    <div
      id="likertoptions"
      v-if="type === 'likert' || type === 'likert-reverse'"
    >
      <div
        v-for="(item, k) in options"
        @click="answer = item.value"
        v-active="answer === item.value"
        :key="k"
      >
        <button
          v-if="
            (answer === null && (k === 0 || k === 5)) || answer === item.value
          "
        >
          {{ item.text }}
        </button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["type", "q"],
  computed: {
    name() {
      return this.$route.params.testname + '' + this.q
    },
    answer: {
      get() {
        if (!(this.name in this.$store.state.extra)) return null;
        return this.$store.state.extra[this.name];
      },
      set(val) {
        this.$store.dispatch("extra/set", { key: this.name, value: val });
      },
    },
    options() {
      if (this.type === "likert") {
        return [
          {
            text: "sterk mee oneens",
            value: 0,
          },
          {
            text: "mee oneens",
            value: 1,
          },
          {
            text: "enigszins mee oneens",
            value: 2,
          },
          {
            text: "enigszins mee eens",
            value: 3,
          },
          {
            text: "mee eens",
            value: 4,
          },
          {
            text: "sterk mee eens",
            value: 5,
          },
        ];
      }
      if (this.type === "likert-reverse") {
        return [
          {
            text: "sterk mee oneens",
            value: 5,
          },
          {
            text: "mee oneens",
            value: 4,
          },
          {
            text: "enigszins mee oneens",
            value: 3,
          },
          {
            text: "enigszins mee eens",
            value: 2,
          },
          {
            text: "mee eens",
            value: 1,
          },
          {
            text: "sterk mee eens",
            value: 0,
          },
        ];
      }
      return [{ text: "hello", value: null }];
    },
  },
};
</script>
<style lang="less" scoped>
#question {
  font-family: Helvetica, sans-serif;
  margin-top: 2em;
  margin-bottom: 5em;
  text-indent: 0;
  // border-top: 1px solid @fg;
  padding-top: 0.5em;
  max-width: 24em;
  margin-left: auto;
  margin-right: auto;
  #likertoptions {
    position: relative;
    margin-top: 2em;
    text-indent: 0;
    display: flex;
    flex-direction: row;
    width: 100%;
    // border-top: 1px solid @fg;
    &:before {
      content: "";
      position: absolute;
      left: 100% / 12;
      top: 0;
      width: (100 - (100 / 6)) * 1%;
      height: 0%;
      border-top: 1px solid @fg;
    }
    div {
      position: relative;
      width: 100%;
      margin: 0;
      padding: 0;
      font-size: 0.5em;
      &:before {
        content: "";
        @s: 1.5em;
        width: @s;
        height: @s;
        background: @bg;
        border-radius: 100%;
        position: absolute;
        left: 50%;
        margin-left: @s * -0.5;
        top: @s * -0.5;
        border: 2px solid @fg;
        box-sizing: border-box;
        cursor: pointer;
      }
      &:hover {
        &:before {
          background: @syn;
        }
      }
      &.active {
        &:before {
          background: @fg;
        }
      }
      button {
        padding: 0;
        margin-top: 2em;
        display: block;
        width: 100%;
        position: absolute;
      }
    }
  }
}
@media (max-width: 40rem) {
  #question {
    font-size: 0.8rem;
    line-height: 1.3em; 
  }
}
</style>
