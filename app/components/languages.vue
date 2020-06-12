<template>
  <div id="languages" v-if="moreThanOne">
    <button
      v-for="(language, short) in $config.languages"
      :key="short"
      v-active="short === current"
      @click="setLanguage(short)"
    >
      {{ language }}
    </button>
  </div>
</template>
<script>
export default {
  computed: {
    moreThanOne() {
      return Object.keys(this.$config.languages).length > 1;
    },
    current() {
      return this.$store.state.profile.language;
    },
  },
  methods: {
    setLanguage(short) {
      this.$store.dispatch("profile/set", { language: short });
    },
  },
};
</script>
<style lang="less" scoped>
#languages {
  button {
    text-transform: uppercase;
    font-weight: 100;
    opacity: 0.5;
    transition: all 0.3s;
    padding: 0 1em 0 0;
    &:hover {
      opacity: 1;
    }
    &.active {
      opacity: 1;
    }
  }
}
</style>
