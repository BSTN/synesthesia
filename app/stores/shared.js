import Vue from 'vue'
import { cloneDeep } from "lodash";

const emptyProfile = {
  name: '',
  tests: [],
  results: []
}

// store
export const state = () => ({
  profiles: {},
  active: null,
});

export const mutations = {
  setProfile(state, content) {
    let key = Object.keys(content)[0];
    Vue.set(state.profiles, key, cloneDeep(Object.values(content)[0]));
  },
  setActive(state, content) {
    Vue.set(state, 'active', content);
  },
  remove(state, id) {
    Vue.delete(state.profiles, id);
  }
}

export const actions = {
  setProfile(store, content) {
    store.commit('setProfile', content)
  },
  remove(store, id) {
    store.commit('remove', id)
  }
}


export const getters = {
  active(state, content) {
    if (!state.active || !(state.active in state.profiles)) {
      return false;
    }
    return state.profiles[state.active]
  }
}