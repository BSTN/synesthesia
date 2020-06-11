<template>
  <div id="testpage">
    <testframe v-if="exists"></testframe>
  </div>
</template>
<script>
export default {
  data() {
    return {
      exists: false,
    };
  },
  mounted() {
    if (
      !this.$route.params.testname ||
      Object.keys(this.$store.state.tests.tests).length < 1 ||
      !(this.$route.params.testname in this.$store.state.tests.tests)
    ) {
      console.warn("test does not exists");
    } else {
      this.exists = true;
      this.$store.commit("tests/setActive", this.$route.params.testname);
    }
  },
};
</script>
<style lang="less" scoped>
#testpage {
  padding: 0.5rem;
  background: @bg2;
  min-height: 100vh;
  display: flex;
  align-content: center;
  align-items: center;
  @media (max-width: 600px) {
    padding: 0;
    #testframe {
      border-radius: 0;
    }
  }
}
</style>
