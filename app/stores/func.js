export const state = () => ({
  start: false
});

export const mutations = {
  start(state, content, check) {
    state.start = true
  },
};