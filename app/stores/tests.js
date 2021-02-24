import Vue from "vue";
import color from "color";
import { each, clone, cloneDeep } from "lodash";
import router from 'vue-router'

const TESTS = JSON.parse(document.getElementById("bootload-tests").innerText)

// defaults
const emptyTest = {
  questions: {},
  position: 0,
  pretest: false,
  posttest: false,
  pageCount: 0,
  totalPages: 0,
  testing: false,
  finished: false,
  setname: null,
};
const emptyItem = {
  symbol: null,
  value: null,
  clicks: 0,
  clicksslider: 0,
  position: null,
  timing: null,
  qnr: null,
};

// store
export const state = () => ({
  tests: {},
  active: null,
});

export const mutations = {
  set(state, content) {
    let key = Object.keys(content)[0];
    Vue.set(state, key, cloneDeep(Object.values(content)[0]));
  },
  setActive(state, name) {
    state.active = name;
  },
  setTest(state, name) {
    Vue.set(state.tests, name, cloneDeep(emptyTest));
  },
  setFinished(state, name) {
    Vue.set(state.tests[name], "finished", true);
  },
  setSetName(state, content) {
    Vue.set(state.tests[content.name], "setname", content.setname);
  },
  setQuestions(state, content) {
    each(content.questions, (v, k) => {
      var notempty = cloneDeep(emptyItem);
      let item = {};
      item.symbol = clone(v);
      item.qnr = k;
      Vue.set(
        state.tests[content.name].questions,
        k,
        Object.assign(notempty, item)
      );
    });
    Vue.set( state.tests[content.name], "pretest", content.pretest );
    Vue.set( state.tests[content.name], "posttest", content.posttest );
  },
  appendQuestion(state, content) {
    var notempty = cloneDeep(emptyItem);
    Vue.set(state.questions, content.qnr, Object.assign(notempty, content));
  },
  next(state, content) {
    state.tests[state.active].position++;
  },
  setValue(state, content) {
    for (let k in content.values) {
      Vue.set(
        state.tests[content.name].questions[state.tests[content.name].position],
        k,
        content.values[k]
      );
    }
  }
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
  next(store, content) {
    // check if finished?
    store.commit("next");
  },
  setValue(store, content) {
    if (store.state.active === null) return false;
    store.commit("setValue", {
      name: store.state.active,
      values: content,
    });
  },
  fill(store, testname) {
    each(store.state.tests, (test, testname) => {
      each(test.questions, (q) => {
        const type = TESTS[testname].type
        if (type === 'imagesound') {
          q.value = Math.random() > 0.5 ? q.symbol.im1 : q.symbol.im2
        } else {
          if (Math.random() > 0.9) q.value = "nocolor";
          else
            q.value = color
              .rgb(Math.random() * 256, Math.random() * 256, Math.random() * 256)
              .hex()
              .replace("#", "");
        }
      });
    });
  },
  async nextPage(store) {
    const currentTest = store.state.tests[store.state.active]
    // set totalpages again
    let pretestlength = currentTest.pretest ? currentTest.pretest.length : 0
    let posttestlength = currentTest.posttest ? currentTest.posttest.length : 0
    currentTest.totalPages = pretestlength + 1 + posttestlength
    // add page to pageCount
    if (currentTest.pageCount < currentTest.totalPages) currentTest.pageCount++
    if (currentTest.pageCount === currentTest.totalPages) {
      await store.dispatch("profile/finished", store.state.active, { root: true })
      await store.dispatch("profile/upload", {}, { root: true });
      return "done"
    }
  }
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
