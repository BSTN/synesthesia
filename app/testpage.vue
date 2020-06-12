<template>
  <div id="testpage">
    <testframe v-if="exists && $store.state.profile.UID"></testframe>
    <div v-if="errormessage">Error: {{ errormessage }}</div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      exists: false,
      errormessage: null,
    };
  },
  methods: {
    error(message) {
      this.errormessage = message;
    },
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
    if (this.$store.state.profile.UID === false) {
      this.$axios
        .get("./api/uid")
        .then((x) => {
          if (x.data.UID)
            this.$store.dispatch("profile/set", { UID: x.data.UID });
          else this.error("Did not receive UID.");
        })
        .catch((err) => {
          this.error(err);
        });
    }
  },
};
</script>
<style lang="less" scoped>
#testpage {
  padding: 0.5rem;
  background: @bg;
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
