<template>
  <div id="paperjs">
    <canvas ref="papercanvas" id="paperjs" resize="true"></canvas>
  </div>
</template>
<script>
import paper from "paper";

export default {
  data() {
    return {
      scope: false,
      script: false,
    };
  },
  methods: {
    init() {
      const canvas = this.$refs.papercanvas;
      if (!this.scope && !this.script) {
        this.$axios.get("assets/connected.js").then((x) => {
          this.script = x.data;
          this.scope = new paper.PaperScope();
          this.scope.setup(canvas);
          this.scope.execute(this.script);
        });
      } else if (!this.scope && this.script) {
        const canvas = this.$refs.papercanvas;
        this.scope = new paper.PaperScope();
        this.scope.setup(canvas);
        this.scope.execute(this.script);
      } else {
        this.scope.execute(this.script);
      }
    },
  },
  beforeDestroy() {
    this.scope.remove();
  },
  mounted() {
    this.init();
  },
};
</script>
<style lang="less" scoped>
#paperjs {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  z-index: 0;
  canvas {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
  }
}
</style>
