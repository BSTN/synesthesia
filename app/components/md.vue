<template>
  <div id="md">
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
      let name = this.md;
      if (this.$store.state.profile.language !== this.$config.defaultLanguage) {
        name = name + "." + this.$store.state.profile.language;
      }
      let template = document.getElementById(`template${name}`);
      // otherwise get default
      if (!template) template = document.getElementById(`template${this.md}`);
      // or give error
      if (!template) console.warn("Missing template:", this.md);
      else {
        template = template.innerHTML;
        this.$cache[this.md] = template;
        this.template = template;
      }
    },
  },
  watch: {
    "$store.state.profile.language": function() {
      this.load();
    },
  },
  mounted() {
    this.load();
  },
};
</script>
<style lang="less" scoped>
</style>
