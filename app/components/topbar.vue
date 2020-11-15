<template>
  <div id="topbar">
    <div class="left">
      <slot name="left"></slot>
    </div>
    <div id="line"></div>
    <div class="right">
      <slot name="right"></slot>
      <button @click="fs()">fullscreen</button>
      <button id="userid" v-if="$store.state.profile.USERID">ID: {{$store.state.profile.USERID}}</button>
    </div>
  </div>
</template>
<script>
import { each } from "lodash";
export default {
  methods: {
    fs () {
       document.documentElement.requestFullscreen();
    }
  },
};
</script>
<style lang="less" scoped>
#topbar {
  padding: 0.45rem 1rem;
  padding: 1rem 1.5rem;
  font-size: 0.65rem;
  position: relative;
  z-index:2;
  display: flex;
  text-transform: uppercase;
  font-weight: 400;
  top: 0.25rem;
  > * {
    flex-shrink: 0;
    flex-grow: 0;
  }
  a,
  button {
    padding: 0;
    margin-right: 1em;
    text-decoration: none;
    &:hover {
      text-decoration: none;
    }
  }
  .right {
    a,
    button {
      margin-right: 0;
      margin-left: 1em;
    }
    #userid {
      background: @fg;
      color: @bg;
      padding: 0.25em 0.5em;
      border-radius: 0.25em;
      line-height: 1em;
      font-size: 0.8em;
      box-sizing: border-box;
      transform: translateY(-0.15em);
    }
  }
  #line {
    width: 100%;
    flex-shrink: 1;
    flex-grow: 1;
    margin: 0;
    animation-name: klip;
    animation-duration: 2s;
    animation-timing-function: @easeInOutExpo;
    animation-delay: 0.5s;
    animation-fill-mode: forwards;
    transform-origin: top left;
    .clip(left);
    @keyframes klip {
      0% {
        .clip(left);
      }
      100% {
        .clip();
      }
    }
    &:after {
      content: "";
      border-top: 2px solid @fg;
      width: 100%;
      display: inline-block;
      height: 0.25em;
    }
  }
}
</style>
