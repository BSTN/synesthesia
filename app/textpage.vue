<template>
  <div id="textpage" class="textpage">
    {{ extra }}
    <topbar>
      <template #left>
        <router-link to="/">{{ $t("home") }}</router-link>
      </template>
      <template #right></template>
    </topbar>
    <md :md="textname" v-if="textname" ref="md"></md>
  </div>
</template>
<script>
import { each } from "lodash";
import { mapGetters } from "vuex";
export default {
  computed: {
    ...mapGetters({
      extra: "extra/all",
    }),
    textname() {
      return this.$route.params.textname || false;
    },
    url() {
      return this.$route.path;
    },
  },
  methods: {
    check() {
      if (this.$refs["md"]) {
        let children = this.$refs["md"].$el.children[0].children;
        let top = window.scrollY;
        let bottom = window.scrollY + window.innerHeight;
        each(children, (el, k) => {
          if (el.offsetTop < bottom) el.classList.add("show");
          else el.classList.remove("show");
        });
      }
    },
  },
  mounted() {
    window.addEventListener("scroll", this.check);
    setTimeout(this.check, 500);
  },
};
</script>
<style lang="less" scoped>

</style>
