<template>
  <div
    id="nextbutton"
    @click="go()"
  >
    <button class="link">
      {{ $t('next') }}
    </button>
  </div>
</template>
<script>
export default {
  props: ['next'],
  methods: {
    go(){
      this.$store.dispatch("tests/nextPage");
    }
  }
}
</script>
<style lang="less" scoped>
#nextbutton {
  font-family: Helvetica, sans-serif;
  text-align: center;
  margin: 2em auto;
  width: 100%;
  display:inline-block;
}
</style>