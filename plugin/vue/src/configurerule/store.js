import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        ruleId: '',
        courseSetId: '',
        step: 0,
        components: ['Courses', 'RankingGroupList', 'Finished'],
        courses: {},
        rankingGroups: [],
        bundleItems: [],
        initialState: {}
    },
    getters: {
        ruleId: state => {
            return state.ruleId
        },
        courseSetId: state => {
            return state.courseSetId
        },
        courses: state => {
            return state.courses
        },
        rankingGroups: state => {
            return state.rankingGroups
        },
        rankingGroupForId: (state) => (id) => {
            return state.rankingGroups.find(group => group.id === id)
        },
        bundleItems: state => {
            return state.bundleItems
        }
    },
    mutations: {
        nextStep(state) {
            state.step++
        },
        prevStep(state) {
            state.step--
        },
        setRuleId(state, ruleId) {
            state.ruleId = ruleId
        },
        setCourseSetId(state, courseSetId) {
            state.courseSetId = courseSetId
        },
        setCourses(state, courses) {
            state.courses = courses
        },
        setCourseCapacity(state, {index, capacity}) {
            state.courses[index].capacity = capacity
        },
        setRankingGroups(state, rankingGroups) {
            state.rankingGroups = rankingGroups
        },
        setBundleItems(state, bundleItems) {
            state.bundleItems = bundleItems
        },
        addRankingGroup(state, rankingGroup) {
            state.rankingGroups.push(rankingGroup)
        },
        removeRankingGroup(state, index) {
            state.rankingGroups.splice(index, 1)
        },
        setGroupName(state, {id, name}) {
            state.rankingGroups.find(group => group.group_id === id).group_name = name
        },
        setGroupMinAmountPrio(state, {id, amount}) {
            state.rankingGroups.find(group => group.group_id === id).min_amount_prios = amount
        },
        addBundleItem(state, {groupId, itemId, seminarIds}) {
            state.bundleItems.push({
                item_id: itemId,
                group_id: groupId,
                seminar_ids: seminarIds,
                start_time: null,
                end_time: null,
                weekday: null
            })
        },
        deleteBundleItem(state, itemId) {
            state.bundleItems = state.bundleItems.filter(item => item.item_id !== itemId)
        },
        mergeBundleItems(state, {item, seminarIds}) {
            item.seminar_ids.push(...seminarIds)
        },
        setInitial(state, initial) {
            state.initialState = JSON.parse(JSON.stringify(initial))
        }
    },
    actions: {
        setInitial({commit, state}) {
            commit('setInitial', state)
        },
        nextStep(context) {
            context.commit('nextStep')
        },
        prevStep(context) {
            context.commit('prevStep')
        },
        setRuleId(context, ruleId) {
            context.commit('setRuleId', ruleId)
        },
        setCourseSetId({commit}, courseSetId) {
            commit('setCourseSetId', courseSetId)
        },
        setCourseCapacity({commit}, {index, capacity}) {
            commit('setCourseCapacity', {index, capacity})
        },
        setGroupName({commit}, {id, name}) {
            commit('setGroupName', {id, name})
        },
        setGroupMinAmountPrio({commit}, {id, amount}) {
            commit('setGroupMinAmountPrio', {id, amount})
        },
        addRankingGroup({commit, state}) {
            axios.post('/plugins.php/bundleallocationplugin/config/create_ranking_group/' + state.ruleId)
                .then(res => {
                    commit('addRankingGroup', {
                        group_id: res.data.group_id,
                        rule_id: state.ruleId,
                        group_name: 'Neue Zuteilungsgruppe',
                        min_amount_prios: 0
                    })
                })
                .catch(err => {
                    console.log(err)
                })

        },
        removeRankingGroup({commit, state}, index) {
            let group_id = state.rankingGroups[index].group_id;
            axios.delete('/plugins.php/bundleallocationplugin/config/delete_ranking_group/' + group_id)
                .then(() => {
                    commit('setBundleItems', state.bundleItems.filter(item => item.group_id !== group_id));
                    commit('removeRankingGroup', index)
                })
                .catch(err => console.log(err))

        },
        addBundleItem({commit}, {groupId, seminarIds}) {
            return new Promise(resolve => {
                axios.post('/plugins.php/bundleallocationplugin/config/create_bundleitem/' + groupId,
                    {
                        "seminar_ids": seminarIds,
                        "start_time": null,
                        "end_time": null,
                        "weekday": null
                    })
                    .then(res => {
                        commit('addBundleItem', {itemId: res.data.item_id, groupId: groupId, seminarIds: seminarIds});
                        resolve();
                    })
                    .catch(err => {
                        console.log(err)
                    })
            })

        },
        deleteBundleItem({commit}, itemId) {
            return new Promise(resolve => {

                axios.delete('/plugins.php/bundleallocationplugin/config/delete_bundleitem/' + itemId)
                    .then(() => {
                        commit('deleteBundleItem', itemId);
                        resolve();
                    })
                    .catch(err => console.log(err))

            })

        },
        mergeBundleItems({dispatch, commit, state}, itemIds) {
            let item = state.bundleItems.find(item => item.item_id === itemIds[0]);
            let seminarIds = [];
            for (let i = 1; i < itemIds.length; i++) {
                let mergingItem = state.bundleItems.find(item => item.item_id === itemIds[i])
                seminarIds.push(...mergingItem.seminar_ids)
            }
            let promises = []
            for (let i = 1; i < itemIds.length; i++) {
                promises.push(dispatch('deleteBundleItem', itemIds[i]))
            }
            Promise.all(promises)
                .then(() => {
                    commit('mergeBundleItems', {item: item, seminarIds: seminarIds});
                    axios.post('/plugins.php/bundleallocationplugin/config/update_bundleitem/' + itemIds[0],
                        state.bundleItems.find(item => item.item_id === itemIds[0]))
                        .catch(err => console.log(err))
                });

        },
        splitBundleItems({dispatch, state}, {groupId, itemId}) {
            let seminarIds = state.bundleItems.find(item => item.item_id === itemId).seminar_ids;
            dispatch('deleteBundleItem', itemId)
                .then(() => {
                    seminarIds.forEach(seminar => dispatch('addBundleItem', {groupId: groupId, seminarIds: [seminar]}));
                })


        },
        fetchCourses({commit, state}) {
            return new Promise(resolve => {
                axios.get('/plugins.php/bundleallocationplugin/config/get_courses/' + state.courseSetId)
                    .then(res => {
                        commit('setCourses', res.data);
                        resolve();
                    })
                    .catch(err => {
                        console.log(err)
                    })
            })

        },
        fetchRankingGroups({commit, state}) {
            return new Promise((resolve) => {
                axios.get('/plugins.php/bundleallocationplugin/config/ranking_groups/' + state.ruleId)
                    .then(res => {
                        commit('setRankingGroups', res.data);
                        resolve()
                    })
            })
        },
        fetchBundleItems({commit, state}) {
            let promises = [];
            state.rankingGroups.forEach(group => {
                promises.push(axios.get('/plugins.php/bundleallocationplugin/config/bundleitems/' + group.group_id)
                    .then(res => {
                        commit('setBundleItems', [...state.bundleItems, ...res.data]);
                    })
                    .catch(err => {
                        console.log(err)
                    }));
            });
            return Promise.all(promises);
        }
    }
})
