<template>
  <div id="md">
    <div id="loading" v-if="loading"></div>
    <vrt :template="markdown" v-if="markdown"></vrt>
  </div>
</template>
<script>
import marked from "marked";
export default {
  props: ["md"],
  data() {
    return {
      loading: false,
      markdown: false,
    };
  },
  methods: {
    load() {
      this.loading = true;
      if (this.$cache && this.$cache[this.md])
        this.markdown = this.$cache[this.md];
      else {
        this.$axios
          .get("./texts/" + this.md + ".md")
          .then((x) => {
            let markdownText = marked(x.data);
            this.markdown = `<div id="md">${markdownText}</div>`;
            this.loading = false;
          })
          .catch((err) => {
            console.warn(`Could not load texts/${this.md}.md markdown`);
            this.loading = false;
          });
      }
    },
  },
  mounted() {
    this.load();
  },
};
</script>
<style lang="less" scoped></style>
