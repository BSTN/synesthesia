import Vue from "vue";
import _ from "lodash";

const empty = {
  symbol: null,
  color: null,
  clicks: 0,
  timing: null,
  qnr: null,
  interrupted: false,
};

export const state = () => ({
  questions: {},
  position: 0,
});

export const mutations = {
  set(state, content) {
    let key = Object.keys(content)[0];
    Vue.set(state, key, _.cloneDeep(Object.values(content)[0]));
  },
  appendQuestion(state, content) {
    var notempty = _.cloneDeep(empty);
    Vue.set(state.questions, content.qnr, Object.assign(notempty, content));
  },
  next(state, content) {
    state.position++;
  },
  prev(state, content) {
    state.position--;
  },
  setValue(state, content) {
    for (let k in content) {
      state.questions[state.position][k] = content[k];
    }
  }
};

export const actions = {
  set(store, content) {
    store.commit("set", content);
  },
  appendQuestion(store, content) {
    store.commit("appendQuestion", content);
  },
  next(store) {
    store.commit("next");
  },
  prev(store) {
    store.commit("prev");
  },
  setValue(store, content) {
    store.commit("setValue", content);
  }
};
