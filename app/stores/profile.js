import Vue from 'vue'
import _ from 'lodash'

export const state = () => ({
  profileID: false,
  userCode: false,
})

export const mutations = {
  set(state, content) {
    let key = Object.keys(content)[0]
    Vue.set(state, key, _.cloneDeep(Object.values(content)[0]))
  }
}

export const actions = {
  set(store, content) {
    store.commit('set', content)
  }
}