<template>
  <div
    v-if="config.type === 'imagesound'"
    id="imagesound"
  >
    <div id="images">
      <div
        id="leftimage"
        :style="bg1"
        @click="set(1)"
        :class="im1class"
      />
      <div
        id="rightimage"
        :style="bg2"
        @click="set(2)"
        :class="im2class"
      />
    </div>
    <div id="soundframe">
      <test-sound :file="q.symbol.sound" autoplay></test-sound>
    </div>
  </div>
</template>
<script>
import {join} from 'path'
export default {
  props: [ 'config', 'q', 'testdata'],
  computed: {
    bg1 () {
      return { backgroundImage: `url(${join(this.$configbase, 'images', this.q.symbol.im1)})` }
    },
    bg2 () {
      return { backgroundImage: `url(${join(this.$configbase, 'images', this.q.symbol.im2)})` }
    },
    im1class () {
      if (!this.q.value || this.q.value === undefined || this.q.value === '') return ''
      if (this.q.value === this.q.symbol.im1) return 'active'
      else return 'inactive'
    },
    im2class () {
      if (!this.q.value || this.q.value === undefined || this.q.value === '') return ''
      if (this.q.value === this.q.symbol.im2) return 'active'
      else return 'inactive'
    }
  },
  methods: {
    async set(val) {
      if (val === 1) {
        await this.$store.dispatch("tests/setValue", { value: this.q.symbol.im1 });
      }
      if (val === 2) {
        await this.$store.dispatch("tests/setValue", { value: this.q.symbol.im2 });
      }
      await this.$store.dispatch("tests/setValue", {
        clicks: this.q.clicks + 1,
      });
    }
  }
}
</script>
<style lang="less" scoped>
#imagesound {
  height: 20rem;
  display: flex;
  flex-direction: column;
  align-content: stretch;
}
#images {
  display: flex;
  height: 100%;
  width: 100%;
  flex-grow:1;
  flex-shrink: 1;
  border: 1.5rem solid transparent;
  > * {
    border: 1rem solid transparent;
    width: 50%;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    position: relative;
    box-sizing:border-box;
    flex-grow: 0;
    flex-shrink: 0;
    cursor:pointer;
    transition: all 0.5s @easeInOutExpo;
    &.active {
      opacity: 1 !important;
      transform: scale(1);
    }
    &.inactive {
      opacity: 0.5 !important;
      transform: scale(.75);
    }
  }
  &:hover {
    > * {
      opacity: 0.5;
      &:hover {
        opacity: 1;
      }
    }
  }
}
#soundframe {
  flex-grow: 0;
  flex-shrink: 0;
  height: 5rem;
  width: 5rem;
  margin-bottom: 0.5rem;
  border: 0 solid transparent;
  // position:absolute;
  bottom:0;
  /deep/ #sound {
    margin:0;
  }
}

@media (max-width: 50rem) {
  #imagesound {
    height: auto;
  }
  #images {
    min-height: 70vw;
  }
  #soundframe {
    position: relative;
    margin-bottom: 1rem;
  }
}

@media (max-width: 30rem) {
  #imagesound {
    min-height: 50vh;
  }
  #images {
    border: 0;
    > * {
    }
  }
}
</style>