export const state = () => ({
  start: false,
  name: ''
});

export const mutations = {
  start(state, content, check) {
    state.start = true
  },
  setName (state, content) {
    state.name = content
  }
};