import Vue from "vue";
import _ from "lodash";

// defaults
const emptyTest = {
  questions: {},
  position: 0,
  finished: false,
  setname: null,
};
const emptyItem = {
  symbol: null,
  value: null,
  clicks: 0,
  clicksslider: null,
  timing: null,
  qnr: null,
  interrupted: false,
};

// store
export const state = () => ({
  tests: {},
  active: null,
});

export const mutations = {
  set(state, content) {
    let key = Object.keys(content)[0];
    Vue.set(state, key, _.cloneDeep(Object.values(content)[0]));
  },
  setActive(state, name) {
    state.active = name;
  },
  setTest(state, name) {
    Vue.set(state.tests, name, _.cloneDeep(emptyTest));
  },
  setFinished(state, name) {
    Vue.set(state.tests[name], "finished", true);
  },
  setSetName(state, content) {
    Vue.set(state.tests[content.name], "setname", content.setname);
  },
  setQuestions(state, content) {
    _.each(content.questions, (v, k) => {
      var notempty = _.cloneDeep(emptyItem);
      let item = {};
      item.symbol = _.clone(v);
      Vue.set(
        state.tests[content.name].questions,
        k,
        Object.assign(notempty, item)
      );
    });
  },
  appendQuestion(state, content) {
    var notempty = _.cloneDeep(emptyItem);
    Vue.set(state.questions, content.qnr, Object.assign(notempty, content));
  },
  next(state, content) {
    state.tests[state.active].position++;
  },
  prev(state, content) {
    state.tests[state.active].position--;
  },
  setValue(state, content) {
    for (let k in content.values) {
      Vue.set(
        state.tests[content.name].questions[state.tests[content.name].position],
        k,
        content.values[k]
      );
    }
  },
};

export const actions = {
  set(store, content) {
    store.commit("set", content);
  },
  setQuestions(store, content) {
    // content: {name: 'testname', setname: 'setname', questions: [] }
    store.commit("setTest", content.name);
    store.commit("setSetName", content);
    store.commit("setQuestions", content);
  },
  appendQuestion(store, content) {
    store.commit("appendQuestion", content);
  },
  setFinished(store) {
    if (store.active === null) return false;
    store.commit("setFinished", store.state.active);
  },
  next(store) {
    store.commit("next");
  },
  prev(store) {
    store.commit("prev");
  },
  setValue(store, content) {
    if (store.state.active === null) return false;
    store.commit("setValue", { name: store.state.active, values: content });
  },
};

export const getters = {
  testdata(state) {
    if (state.active === null) return false;
    return state.tests[state.active];
  },
  q(state) {
    if (state.active === null) return false;
    if (state.tests[state.active].finished === true) return false;
    return state.tests[state.active].questions[
      state.tests[state.active].position
    ];
  },
};