<template>
  <div
    id="sound"
    :style="{background: background}"
    @click="playAudio()"
  >
    <div id="icons" :class='{playing}'>
      <svg
        v-if="playing"
        width="500px"
        height="500px"
        viewBox="0 0 500 500"
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
      >
        <g
          id="pause"
          stroke="none"
          stroke-width="1"
          fill="none"
          fill-rule="evenodd"
        >
          <rect
            id="Rectangle"
            fill="#222222"
            x="135"
            y="95"
            width="85"
            height="311"
            rx="10"
          />
          <rect
            id="Rectangle-Copy"
            fill="#222222"
            x="282"
            y="95"
            width="84"
            height="311"
            rx="10"
          />
        </g>
      </svg>
      <svg
        v-if="!playing"
        width="500px"
        height="500px"
        viewBox="0 0 500 500"
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
      >
        <g
          id="play"
          stroke="none"
          stroke-width="1"
          fill="none"
          fill-rule="evenodd"
        >
          <path
            id="Rectangle"
            d="M121.392483,46.0704638 L442.181895,251.480184 C446.832944,254.45837 448.189067,260.643087 445.210881,265.294135 C444.35089,266.637188 443.18215,267.755077 441.802195,268.554513 L121.012783,454.394516 C116.23394,457.163 110.115623,455.533282 107.347139,450.754438 C106.464701,449.231211 106,447.50203 106,445.741655 L106,54.491932 C106,48.9690845 110.477153,44.491932 116,44.491932 C117.911416,44.491932 119.78279,45.0397362 121.392483,46.0704638 Z"
            fill="#222222"
          />
        </g>
      </svg>
    </div>
  </div>
</template>
<script>
import { join } from 'path'
import globalAudio from '../../utils/audio'
export default {
  props: ["background", "file", "autoplay", "isolate"],
  data() {
    return {
      playing: false,
      error: false,
      audio: false
    }
  },
  watch: {
    "file": function () {
      this.loadAudio();
    }
  },
  mounted(){
    this.loadAudio();
  },
  methods: {
    loadAudio () {
      let self = this;
      if (!this.$configbase) {
        console.warn('No path for github files defined ($configbase)')
        this.error = "Could not load file."
        return
      }
      if (!this.file) {
        console.warn('No inputfile defined.')
        this.error = "Could not load file."
        return
      }
      // define audiofile
      const audiofile = join(this.$configbase, 'audio', this.file)
      // define audio object
      this.audio = this.isolate !== undefined ? new Audio() : globalAudio

      this.audio.autoplay = this.autoplay !== undefined
      if (this.audio.src !== audiofile) this.audio.src = audiofile
      this.audio.addEventListener('playing', function () {
        self.playing = true;
      })
      this.audio.addEventListener('ended', function () {
        self.playing = false;
      })
    },
    playAudio () {
      this.audio.play();
    }
  }
}
</script>
<style lang="less" scoped>
#sound {
  background: transparent;
  min-height: 4rem;
  margin: 0 auto;
  border-radius: 0.25em;
  cursor: pointer;
  position: relative;
  display: flex;
  align-content: center;
  align-items: center;
  text-align: center;
  width: 100%;
  height: 100%;

  #icons {
    position: relative;
    width: 70%;
    background: #f3f3f3;
    border-radius: 100%;
    margin: 0 auto;
    border: 0;
    max-width: 6rem;
    max-height: 6rem;
    flex-grow: 0;
    flex-shrink: 0;
    transition: all 0.25s;
    text-align: center;
    box-shadow: 0 0.1rem 0.25rem rgba(#000, 0.4);
    border: 0.2rem solid transparent;
    background: rgba(#000, 0.1);
    overflow: hidden;
    box-shadow:
      0.025rem -0.025rem 0.05rem rgba(#000, 0.4),
      inset 0.05rem -0.05rem 0.05rem rgba(#000, 0.4),
      inset -0.05rem 0.05rem 0.05rem rgba(#fff, 0.1),
      -0.05rem 0.05rem 0.05rem rgba(#fff, 0.1);

    &::before {
      content: "";
      display: block;
      padding-top: 100%;
      width: 0;
    }

    &::after {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: #fafafa;
      border-radius: 100%;
      box-shadow:
        0.05rem -0.05rem 0.05rem rgba(#000, 0.3),
        inset 0.05rem -0.05rem 0.05rem rgba(#000, 0.3),
        inset -0.05rem 0.05rem 0.05rem rgba(#fff, 0.7),
        -0.05rem 0.05rem 0.05rem rgba(#fff, 0.7);
    }

    svg {
      width: 50%;
      height: 50%;
      position: absolute;
      left: 25%;
      top: 25%;
      z-index: 1;

      path,
      rect {
        transition: all 0.25s;
        fill: #999;
      }
    }

    &:hover, &.playing {
      @c: #555;

      background: @c;
      box-shadow:
        0.025rem -0.025rem 0.05rem rgba(#000, 0.3),
        inset 0.025rem -0.025rem 0.05rem rgba(#000, 0.3),
        inset 0 0 0.25rem rgba(#000, 0.8),
        inset -0.05rem 0.05rem 0.05rem rgba(#fff, 0.1),
        -0.05rem 0.05rem 0.05rem rgba(#fff, 0.1);

      svg {
        path,
        rect {
          fill: @c;
        }
      }
    }
  }

  &:hover {
    #icons {
      // background: #fff;
    }
  }

  @media (max-width: 40rem) {
    margin: 0 1rem;
    width: 100%;
    height: 100%;

    #icons {
      width: 4rem;
      height: 4rem;
      padding: 0;
      margin: 1rem auto;
      box-shadow: 0 0 4px rgba(#000, 0.3);
    }
  }
}
</style>