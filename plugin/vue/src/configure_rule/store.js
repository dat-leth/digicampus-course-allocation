import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        rule: {},
        course_infos: {},
        selected_course_ids: [],
    },
    getters: {
        assignedCourses: state => {
            let assigned = [];
            for (const group_id in state.rule.groups) {
                if (state.rule.groups.hasOwnProperty(group_id)) {
                    let group = state.rule.groups[group_id];
                    for (const item_id in group.bundleItems) {
                        if (group.bundleItems.hasOwnProperty(item_id)) {
                            assigned.push(...Object.keys(group.bundleItems[item_id].courses));
                        }
                    }
                }
            }
            return assigned;
        },
        notAssignedCourses: (state, getters) => {
            return state.selected_course_ids.filter(id => !getters.assignedCourses.includes(id));
        },
        notAssignedCourseDetails: (state, getters) => {
            let details = [];
            getters.notAssignedCourses.forEach(course_id => {
                if (state.course_infos[course_id] !== undefined) {
                    details.push({...state.course_infos[course_id]});
                }
            });
            details.sort((a, b) => a.name.localeCompare(b.name));
            return details;
        }
    },
    mutations: {
        setRule(state, newRule) {
            state.rule = newRule;
        },
        setSelectedCourseIds(state, ids) {
            state.selected_course_ids = ids;
        },
        setCourseInfos(state, infos) {
            state.course_infos = infos;
        },
        setCourseInfoById(state, {course_id, info}) {
            state.course_infos[course_id] = info;
        },
        addGroup(state, group) {
            Vue.set(state.rule.groups, group.id, group);
        },
        deleteGroup(state, {group_id}) {
            Vue.delete(state.rule.groups, group_id);
        },
        addBundleItem(state, {group_id, item}) {
            Vue.set(state.rule.groups[group_id].bundleItems, item.id, item);
        },
        deleteBundleItem(state, {group_id, item_id}) {
            Vue.delete(state.rule.groups[group_id].bundleItems, item_id);
        }
    },
    actions: {
        initialState({commit}) {
            // eslint-disable-next-line
            let rule = JSON.parse(BUNDLEALLOCATION_DATA);


            let course_ids = [...document.querySelectorAll('#instcourses input[name="courses[]"]:checked')].map(item => item.value);

            commit('setRule', rule);
            commit('setSelectedCourseIds', course_ids);

            axios.post('/plugins.php/bundleallocationplugin/rule/course_infos', {
                'course_ids': course_ids
            })
                .then(res => {
                    for (const course_id in res.data) {
                        if (res.data.hasOwnProperty(course_id) === true) {
                            if (rule.course_capacity !== null && typeof rule.course_capacity[course_id] !== undefined) {
                                res.data[course_id].capacity = rule.course_capacity[course_id]
                            }
                        }
                    }
                    commit('setCourseInfos', res.data)
                })
                .catch(err => console.error(err))
        },
        updateCapacity({commit, state}, {course_id, capacity}) {
            let info = state.course_infos[course_id];
            info.capacity = capacity;
            commit('setCourseInfoById', {course_id: course_id, info: info});
        },
        addGroup({commit, state}) {
            let formData = new FormData();
            formData.append('rule_id', state.rule.rule_id);
            formData.append('group_name', 'Neue Zuteilungsgruppe');
            axios.post('/plugins.php/bundleallocationplugin/rule/add_ranking_group', formData)
                .then(res => {
                    commit('addGroup', res.data)
                })
                .catch(err => console.error(err))
        },
        deleteGroup({commit}, {group_id}) {
            commit('deleteGroup', {group_id: group_id})
        },
        addBundleItem({commit}, {group_id, course_ids}) {
            let formData = new FormData();
            formData.append('group_id', group_id);
            axios.post('/plugins.php/bundleallocationplugin/rule/add_bundle_item', formData)
                .then(res => {
                    course_ids.forEach(id => {
                        res.data.courses[id] = true;
                    });
                    commit('addBundleItem', {group_id: group_id, item: res.data});
                })
        },
        deleteBundleItems({commit}, {group_id, item_ids}) {
            item_ids.forEach(item_id => {
                commit('deleteBundleItem', {group_id: group_id, item_id: item_id});
            });
        },
        mergeBundleItems({dispatch, state}, {group_id, item_ids}) {
            let merged_course_ids = [];
            item_ids.forEach(item_id => {
                merged_course_ids.push(...Object.keys(state.rule.groups[group_id].bundleItems[item_id].courses))
            });
            dispatch('addBundleItem', {group_id: group_id, course_ids: merged_course_ids})
                .then(() => {
                    dispatch('deleteBundleItems', {group_id: group_id, item_ids: item_ids})
                })
        },
        splitBundleItem({dispatch, state}, {group_id, item_id}) {
            Object.keys(state.rule.groups[group_id].bundleItems[item_id].courses).forEach(course_id => {
                dispatch('addBundleItem', {group_id: group_id, course_ids: [course_id]})
            });
            dispatch('deleteBundleItems', {group_id: group_id, item_ids: [item_id]})
        }
    }
})
