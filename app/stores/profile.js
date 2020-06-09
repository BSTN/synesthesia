import Vue from 'vue'
import _ from 'lodash'

const config = JSON.parse(
  document.getElementById("bootload-config").innerText
);

export const state = () => ({
  profileID: false,
  userCode: false,
  language: _.clone(config.defaultlanguage)
})

export const mutations = {
  set(state, content) {
    let key = Object.keys(content)[0]
    Vue.set(state, key, _.cloneDeep(Object.values(content)[0]))
  },
}

export const actions = {
  set(store, content) {
    store.commit('set', content)
  }
}