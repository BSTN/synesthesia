<template>
  <div id="userid">
    <input type="text" v-model="userid" />
  </div>
</template>
<script>
export default {
  computed: {
    userid: {
      get() {
        return this.$store.state.profile.USERID;
      },
      set(val) {
        this.$store.dispatch("profile/set", { USERID: val });
      },
    },
  },
};
</script>
<style lang="less" scoped>
#userid {
}
</style>
