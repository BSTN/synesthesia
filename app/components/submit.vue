<template>
  <div id="submit" :class="{ disabled }">
    <button @click="submit()">{{ $t("next") }}</button>
    <div id="message" v-if="message">{{ message }}</div>
  </div>
</template>
<script>
export default {
  props: ["to", "text", "enabled"],
  data() {
    return {
      loading: false,
      message: false,
      timer: false,
    };
  },
  computed: {
    disabled() {
      if (!this.enabled) return false;
      let extravalues = this.enabled.split(",");
      let check = false;
      for (let key in extravalues) {
        if (
          !(extravalues[key] in this.$store.state.extra) ||
          this.$store.state.extra[extravalues[key]] === null
        )
          return true;
      }
      console.log("disabled:", check);
      return check;
    },
  },
  methods: {
    async submit() {
      this.loading = true;
      await this.$axios
        .post("./api/store", {
          table: "extra",
          data: { values: JSON.parse(JSON.stringify(this.$store.state.extra)) },
          UID: this.$store.state.profile.UID,
        })
        .then((x) => {
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.message = "Could not store data, please try again.";
          // make this global message
          if (this.timer) clearTimeout(this.timer);
          this.timer = setTimeout(() => {
            this.message = false;
          }, 2000);
          return false;
        });
      this.$router.push({ path: this.to });
    },
  },
};
</script>
<style lang="less" scoped>
#submit {
  margin: 2em auto;
  display: block !important;
  text-align: center;
  opacity: 1 !important;
  padding: 2em 0;
  button {
    border: 1px solid @fg;
    min-width: 8em;
    padding: 0.5em 0.5em;
    border-radius: 0.25em;
    color: @fg;
    &:hover {
      color: @bg;
      background: @fg;
    }
  }
  margin-bottom: 8rem;
  &.disabled {
    pointer-events: none;
    opacity: 0.5 !important;
  }
}
</style>
