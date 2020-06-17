import Vue from "vue";
import { clone, cloneDeep } from "lodash";
import { i18n } from "../utils/i18n.js";

const config = JSON.parse(document.getElementById("bootload-config").innerText);

export const state = () => ({
  UID: null,
  USERID: null,
  language: clone(config.defaultLanguage),
});

export const mutations = {
  set(state, content, check) {
    let key = Object.keys(content)[0];
    Vue.set(state, key, cloneDeep(Object.values(content)[0]));
    if (key === "language") {
      i18n.locale = Object.values(content)[0];
    }
  },
};

export const actions = {
  set(store, content) {
    store.commit("set", content);
  },
};
