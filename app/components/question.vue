<template>
  <div id="question">
    <slot></slot>
    <div id="options">
      <button
        v-for="(item, k) in options"
        @click="answer = item.value"
        v-active="answer === item.value"
        :key="k"
      >
        {{ item.text }}
      </button>
    </div>
  </div>
</template>
<script>
export default {
  props: ["type", "name"],
  computed: {
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
            text: "enigszins mee oneens",
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
            text: "enigszins mee oneens",
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
  margin-bottom: 4em;
  #options {
    margin-top: 1em;
    button {
      padding: 0.5em;
      line-height: 1em;
      margin: 0 0.25em 0.5em 0;
      border-radius: 0.25em;
      border: 1px solid @fg;
      opacity: 0.5;
      &:hover {
        opacity: 1;
      }
      &.active {
        opacity: 1;
        background: @fg;
        color: @bg;
      }
    }
  }
}
</style>
