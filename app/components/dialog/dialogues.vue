<template>
  <div id="dialogues">
    <transition-group name="page">
      <dialog-dialog
        v-for="(item, k) in list"
        :options="item"
        :key="item.id"
        :index="k"
        :style="{ zIndex: k }"
      ></dialog-dialog>
    </transition-group>
  </div>
</template>
<script>
export default {
  data () {
    return {
      list: []
    }
  },
  methods: {
    destroy() {
      this.$el.remove();
      this.$destroy();
    },
    escapeKeyListener(e){
      if (e.keyCode !== 27) return

      let index = (-1 + this.list.length)

      if(index > -1){
          this.list[index].promiseRejecter();
          this.list.splice(index,1);
      }
    }
  },
  created() {
    let self = this
    document.addEventListener('keydown', this.escapeKeyListener)
    this.$root.confirm = function(options) {
      return new Promise((resolve, reject) => {
        options.id = Date.now();
        options.type = 'confirm';
        options.promiseResolver = resolve;
        options.promiseRejecter = reject;
        self.list.push(options);
      });
    }
    this.$root.alert = function(options) {
      return new Promise((resolve, reject) => {
        options.id = Date.now();
        options.type = 'alert';
        options.promiseResolver = resolve;
        options.promiseRejecter = reject;
        self.list.push(options);
      });
    }
    this.$root.choose = function(options) {
      return new Promise((resolve, reject) => {
        options.id = Date.now();
        options.type = 'choose';
        options.promiseResolver = resolve;
        options.promiseRejecter = reject;
        self.list.push(options);
      });
    }
    this.$root.removeDialog = function(index) {
      self.list.splice(self.list[index], 1);
    }
  },
  destroyed() {
    document.removeEventListener('keydown', this.escapeKeyListener)
  },
}
</script>
<style lang="less" scoped>
#dialogues {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  overflow: auto;
  height: 0;
  z-index: 3;
}
</style>
