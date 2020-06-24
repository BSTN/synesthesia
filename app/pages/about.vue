<template>
  <div id="about">
    <topbar>
      <template #left><router-link to="/">BACK</router-link></template>
    </topbar>
    <md md="about" ref="md"></md>
  </div>
</template>
<script>
import { each } from "lodash";
export default {
  methods: {
    check() {
      let children = this.$refs["md"].$el.children[0].children;
      let top = window.scrollY;
      let bottom = window.scrollY + window.innerHeight;
      each(children, (el, k) => {
        if (el.offsetTop < bottom) el.classList.add("show");
        else el.classList.remove("show");
      });
    },
  },
  mounted() {
    window.addEventListener("scroll", this.check);
    setTimeout(this.check, 500);
  },
};
</script>
<style lang="less" scoped>
#about {
  #md {
    padding: 4rem;
    font-family: "Victor Serif Trial";
    animation: delayfade;
    animation-delay: 1s;
    animation-duration: 1s;
    animation-direction: forwards;
    animation-fill-mode: forwards;
    opacity: 0;
    @keyframes delayfade {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }
  }
  /deep/ #md > #md > * {
    opacity: 0;
    transition: all 3s @easeOutExpo;
    &.show {
      opacity: 1;
    }
  }
  /deep/ p {
    &:nth-child(1) {
      font-size: 2em;
      line-height: 1em;
      margin-bottom: 2em;
      max-width: 24em;
    }
  }
  /deep/ h1 {
    margin-top: 2em;
    margin-bottom: 1em;
    margin: 2em auto 1em auto;
    font-size: 1.5em;
    max-width: 16em;
    text-align: center;
    text-indent: none;
  }
  /deep/ p {
    text-indent: 2em;
    max-width: 28em;
    width: 100%;
    margin: 0 auto 1em auto;
    // margin: 0 auto 2em auto;
  }
}
</style>
