<template>
  <div id="dialog">
    <div id="frame">
      <div id="message" v-html="options.message"></div>
      <div id="buttons" v-if="options.type === 'choose'">
        <button v-for="(option,k) in options.options" :key="k" @click="choose(option)">{{option}}</button>
      </div>
      <div id="buttons" v-if="options.type === 'confirm'">
        <button @click="ok()">{{$t('ok')}}</button>
        <button @click="cancel()">{{$t('cancel')}}</button>
      </div>
      <div id="buttons" v-if="options.type === 'alert'">
        <button @click="ok()">{{$t('ok')}}</button>
      </div>
    </div>
    <div id="bg" @click="cancel()"></div>
  </div>
</template>
<script>
export default {
  props: ["options","index"],
  methods: {
    ok () {
      this.options.promiseResolver({message:"done"});
      this.$root.removeDialog(this.index);
    },
    cancel () {
      this.options.promiseRejecter({message:"cancel"});
      this.$root.removeDialog(this.index);
    },
    choose(text) {
      this.options.promiseResolver(text);
      this.$root.removeDialog(this.index);
    }
  }
}
</script>
<style lang="less" scoped>
#dialog {
  position:fixed;
  top:0;
  left:0;
  width: 100%;
  height: auto;
  min-height: 100vh;
  #frame {
    margin: 2rem auto;
    width: calc(100vw - 1rem);
    background: @bg;
    padding: 1rem 1rem 0.5rem 1rem;
    position: relative;
    z-index:9;
    box-shadow: 0 0 10px rgba(#000, 0.5);
    max-width: 20rem;
    border-radius: 0.25em;
    #message {
      margin-bottom:1em;
    }
    #buttons {
      text-align:right;
      button {
        border: 1px solid @fg;
        padding: 0.25em 0.5em;
        min-width: 4em;
        border-radius: 0.25em;
        margin: 0 0.5em 0.5em 0;
        font-size: 0.75em;
        &:hover {
          background: @fg;
          color: @bg;
        }
      }
    }
  }
  #bg {
    position: absolute;
    top:0;
    left:0;
    height: 100%;
    width: 100%;
    background: #222;
    opacity: 0.8;
  }
}
</style>