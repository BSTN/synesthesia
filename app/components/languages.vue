<template>
  <div id="languages" v-if="moreThanOne">
    <button
      v-for="(language, short) in $config.languages"
      :key="short"
      v-active="short === current"
      @click="setLanguage(short)"
    >{{ small ? short : language }}</button>
  </div>
</template>
<script>
export default {
  data() {
    return {
      small: true
    };
  },
  computed: {
    moreThanOne() {
      return Object.keys(this.$config.languages).length > 1;
    },
    current() {
      return this.$store.state.profile.language;
    }
  },
  methods: {
    setLanguage(short) {
      this.$store.dispatch("profile/set", { language: short });
    },
    setSize() {
      this.small = window.innerWidth < 800 ? true : false;
    }
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.setSize();
    });
    this.setSize();
  }
};
</script>
<style lang="less" scoped>
#languages {
  button {
    text-transform: uppercase;
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
