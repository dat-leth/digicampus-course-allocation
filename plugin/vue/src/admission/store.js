import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    // eslint-disable-next-line
    state: BUNDLEALLOCATION_APPLICATIONS,
    getters: {
        amountParticipants: (state => (groupId, seminarId) => {
            return state.prelim.reduce((n, user) => {
                let group = user.groups.find(group => group.group_id === groupId);
                if (group != null && group.seminar_id === seminarId) {
                    n = n + 1
                }
                return n;
            }, 0);
        }),
        allocatedCourse: state => (userId, groupId) => {
            let user = state.prelim.find(user => user.user_id === userId);
            if (user != null) {
                return user.groups.find(group => group.group_id === groupId)
            }
        }
    },
    mutations: {
        setPrelimCourse: (state, {groupId, userId, itemId, seminarId, priority}) => {

            let user = state.prelim.find(user => user.user_id === userId);
            if (user != null) {
                let group = user.groups.find(group => group.group_id === groupId);
                if (group != null) {
                    group.item_id = itemId;
                    group.seminar_id = seminarId;
                    group.priority = priority;
                } else {
                    user.groups.push({
                        group_id: groupId,
                        item_id: itemId,
                        seminar_id: seminarId,
                        priority: priority,
                        waitlist: false})
                }
            } else {
                state.prelim.push({
                    user_id: userId,
                    groups: [{
                        group_id: groupId,
                        item_id: itemId,
                        seminar_id: seminarId,
                        priority: priority,
                        waitlist: false
                    }]
                });
            }
        },
        setPrelimWaitlist: (state, {groupId, userId, waitlist}) => {
            let user = state.prelim.find(user => user.user_id === userId);
            if (user != null) {
                let group = user.groups.find(group => group.group_id === groupId);
                if (group != null) {
                    group.waitlist = waitlist;
                }
            }
        },
        resetPrelim: (state) => {
            state.prelim = [];
        }
    },
    actions: {
        setPrelimCourse({commit}, {groupId, userId, itemId, seminarId, priority}) {
            commit('setPrelimCourse', {groupId, userId, itemId, seminarId, priority})
        },
        setPrelimWaitlist({commit}, {groupId, userId, waitlist}) {
            commit('setPrelimWaitlist', {groupId, userId, waitlist})
        },
        resetPrelim({commit}) {
            commit('resetPrelim')
        }
    }
})
