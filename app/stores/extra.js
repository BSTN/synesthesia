import Vue from "vue";
import {
  clone,
  cloneDeep
} from "lodash";

const config = JSON.parse(document.getElementById("bootload-config").innerText);

export const state = () => ({
});

export const mutations = {
  set(state, content) {
    let key = content.key;
    Vue.set(state, key, cloneDeep(content.value));
  },
};

export const actions = {
  set(store, content) {
    store.commit("set", content);
  },
};

export const getters = {
  all(store) {
    return store.state
  }
}