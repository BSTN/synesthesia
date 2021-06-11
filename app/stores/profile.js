import Vue from "vue";
import { clone, cloneDeep, merge, each } from "lodash";
import { i18n } from "../utils/i18n.js";
import axios from 'axios';

const config = JSON.parse(document.getElementById("bootload-config").innerText);

export const state = () => ({
  UID: null,
  USERID: null,
  SHARED: null,
  language: clone(config.defaultLanguage),
  finishedtests: []
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
    if (content.USERID) {
      store.dispatch('upload')
    }
  },
  finished(store, value) {
    const finishedtests = store.state.finishedtests
    if (finishedtests.indexOf(value) === -1) {
      finishedtests.push(value)
      store.commit("set", { finishedtests: finishedtests }); 
    }
  },
  async upload(store) {
    let data = {};
    data = merge(data, cloneDeep(store.state));
    data.finishedtests = data.finishedtests.join(',');
    delete data.SHARED;
    if (store.state.USERID || config.storeall) {
      let success = await axios
          .post("./api/store", {
            table: "profile",
            UID: store.state.UID,
            data: data,
          })
          .catch((err) => {
            return { error: "Could not store data", err: err.response.data };
          });
      }
  }
};

export const getters = {
  downloadString (state, getters, rootState) {
    let data = {}
    if(rootState.tests.tests && state.finishedtests) {
      state.finishedtests.map(x => {
        if (x && rootState.tests.tests[x]) {
          data[x] = rootState.tests.tests[x]
        }
      })
    }
    if(Object.keys(rootState.extra).length > 0) {
      data._extra = cloneDeep(rootState.extra)
    }
    return "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
  }
}
