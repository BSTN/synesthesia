import Vue from "vue";
import {
  clone,
  cloneDeep
} from "lodash";

const config = JSON.parse(document.getElementById("bootload-config").innerText);

export const state = () => ({
  UID: false,
  USERID: false,
  language: clone(config.defaultlanguage),
});

export const mutations = {
  set(state, content) {
    let key = Object.keys(content)[0];
    Vue.set(state, key, cloneDeep(Object.values(content)[0]));
  },
};

export const actions = {
  set(store, content) {
    store.commit("set", content);
  },
};