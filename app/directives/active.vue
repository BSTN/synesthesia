<script>
export default {
  bind(el, binding, vnode) {
    let val = binding.value;
    if (val) el.classList.add("active");
    else el.classList.remove("active");
  },
  update(el, binding, vnode) {
    let val = binding.value;
    if (val) el.classList.add("active");
    else el.classList.remove("active");
  },
};
</script>
