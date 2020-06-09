<template>
  <div id="md">
    <div id="loading" v-if="loading"></div>
    <vrt :template="template" v-if="template"></vrt>
  </div>
</template>
<script>
export default {
  props: ["md"],
  data() {
    return {
      loading: false,
      template: false,
    };
  },
  methods: {
    load() {
      this.loading = true;
      if (this.$cache && this.$cache[this.md])
        this.template = this.$cache[this.md];
      else {
        let template = document.getElementById(`template${this.md}`);
        if (!template) console.warn("Missing template:", this.md);
        else {
          template = template.innerHTML;
          this.$cache[this.md] = template;
          this.template = template;
        }
      }
    },
  },
  mounted() {
    this.load();
  },
};
</script>
<style lang="less" scoped></style>
