import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    // eslint-disable-next-line
    state: BUNDLEALLOCATION,
    getters: {
        bundleItems: state => {
            let items = {};
            state.courses.forEach(course => {
                if (!Array.isArray(items[course.item_id])) {
                    items[course.item_id] = [course];
                } else {
                    items[course.item_id].push(course);
                }
            });
            return items;
        }
    },
    mutations: {
        addToRanking: (state, itemId) => {
            state.ranking.push(itemId)
        },
        removeFromRanking: (state, itemId) => {
            state.ranking = state.ranking.filter(ranked => ranked !== itemId)
        },
        setRanking: (state, payload) => {
            state.ranking = payload
        }
    },
    actions: {
        addToRanking: ({commit}, itemId) => {
            commit('addToRanking', itemId)
        },
        removeFromRanking: ({commit}, itemId) => {
            commit('removeFromRanking', itemId)
        },
        setRanking: ({commit}, ranking) => {
            commit('setRanking', ranking)
        }
    }
})
