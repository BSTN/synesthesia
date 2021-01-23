<template>
  <div id="md">
    <vrt
      v-if="template"
      :template="template"
    />
  </div>
</template>
<script>
export default {
  props: ["md","type"],
  data() {
    return {
      loading: false,
      template: false,
    };
  },
  watch: {
    "$store.state.profile.language": function() {
      this.load();
    },
  },
  mounted() {
    this.load();
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
};
</script>
<style lang="less" scoped>
#md {
  /deep/ #sound {
    margin: 2rem 0;
  }
}
</style>
