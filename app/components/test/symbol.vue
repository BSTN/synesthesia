<template>
  <transition
    name="symbol"
    mode="out-in"
  >
    <div
      id="symbol"
      :key="testdata.position"
    >
      <div id="symbolframe">
        <div
          id="colorholder"
          :style="{ background: background }"
          :class="{
            nocolor: q.value === 'nocolor',
            empty: q.value === null,
          }"
        />
        <div
          id="sym"
          :class="{ mid: symbol.length > 1, long: symbol.length > 3 }"
        >
          <div>{{ symbol }}</div>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  props: ["q", "background", "testdata", "symbol"]
}
</script>
<style lang="less" scoped>
#symbol {
  width: 100%;
  height: 100%;
  #symbolframe {
    width: 100%;
    height: 100%;
    display: flex;
    align-content: center;
    align-items: center;
    justify-content: center;
    border-radius: 0.25rem;
    font-family: "Roboto Mono";
    #colorholder {
      position: absolute;
      left: 0;
      top: 0;
      width: calc(100% - 3rem);
      height: calc(100% - 3rem);
      margin: 1.5rem;
      z-index: 1;
      border-radius: 0.5rem;
    }
    &.nocolor {
      border: 1px solid #ddd;
      #sym {
        border: 1px solid #ddd;
      }
    }
    #sym {
      z-index: 2;
      font-size: 4rem;
      display: flex;
      flex-direction: row;
      align-content: center;
      align-items: center;
      justify-content: center;
      padding: 0rem 1rem;
      background: @bg;
      background: #fafafa;
      margin: 0;
      min-width: calc(100% - 8rem);
      min-height: calc(100% - 8rem);
      border-radius: 0.25rem;
      &.mid {
        font-size: 2rem;
      }
      &.long {
        font-size: 1rem;
      }
    }
  } 
}

@media (max-width:50rem) {
  #symbol {
    #symbolframe {
      border-radius: 0.25em;
      display: flex;
      position: relative;
      margin: 0;
      justify-content: stretch;
      align-items: stretch;
      align-content: stretch;
      border-bottom:0;
      margin: 1rem 1rem 0;
      width: calc(100% - 2rem);
      box-shadow: 0 0 0.1rem rgba(#000, 0.2);
      #colorholder {
        position: relative;
        background: inherit;
        margin: 0;
        flex-shrink: 1;
        flex-grow: 1;
        z-index: 2;
        box-sizing: border-box;
        border-radius: 0.25em 0 0 0.25em;
        max-width: 3rem;
        max-height: none;
        height: 3rem;
        background: #00f;
        box-shadow: inset 0 0 2rem rgba(#000, 0.1);
      }
      #sym {
        background: transparent;
        position: relative;
        z-index: 3;
        text-align: left;
        font-size: 1em;
        border-radius: 0;
        padding: 0 1em;
        border-radius: 0 0.25em 0.25em 0;
        line-height: calc(2em - 2px);
        > div {
          width: 100%;
        }
      }
    }
  }
}
@media (max-width:30rem) {
  #symbol {
    #symbolframe {
      #colorholder {
        height: 2rem;
        max-width: 2rem;
      }
    }
  }
}
</style>