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
  }
}

export const actions = {
  setProfile(store, content) {
    store.commit('setProfile', content)
  }
}