<template>
  <div id="results">
    RESULTS
    <pre>{{ $store.state.testsquestions }}</pre>
  </div>
</template>
<script>
export default {};
</script>
<style lang="less" scoped>
#results {
  max-width: 40rem;
  margin: 2rem auto;
}
</style>
