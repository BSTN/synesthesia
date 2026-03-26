import Vue from "vue";
import cloneDeep from "lodash/cloneDeep";

export const state = () => ({});

export const mutations = {
  set(state, content) {
    let key = content.key;
    Vue.set(state, key, cloneDeep(content.value));
  }
};

export const actions = {
  set(store, content) {
    store.commit("set", content);
  }
};

export const getters = {
  all(store) {
    return store.state;
  }
};
