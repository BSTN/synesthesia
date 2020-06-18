<template>
  <div id="testpage">
    <!-- <topbar>
      <template #left>
        <router-link to="/">back</router-link>
      </template>
    </topbar> -->
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
    // check test exists
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
    // get uid if not exist
    if (this.$store.state.profile.UID === null) {
      this.$axios
        .post("./api/create", { language: this.$store.state.profile.language })
        .then(async (x) => {
          if (x.data.UID && x.data.SHARED) {
            await this.$store.dispatch("profile/set", { UID: x.data.UID });
            await this.$store.dispatch("profile/set", {
              SHARED: x.data.SHARED,
            });
          } else {
            this.error("Did not receive UID or SHARED key.");
          }
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
  background: #ddd;
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
