<template>
  <div id="startbutton">
    <button class='link' @click="$store.commit('func/start')"><slot></slot></button>
  </div>
</template>
<script>
export default {}
</script>
<style lang="less" scoped>
#startbutton {
  button {
    font-weight: 300;
  }
}
</style>