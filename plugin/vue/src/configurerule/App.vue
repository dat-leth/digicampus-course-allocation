<template>
    <div id="app">
        <keep-alive>
            <component v-bind:is="currentComponent" v-bind="currentProps"></component>
        </keep-alive>
    </div>
</template>

<script>
    import NotSavedWarning from "./components/NotSavedWarning";
    import Courses from "./components/Courses";
    import RankingGroupList from "./components/RankingGroupList";
    import Finished from "./components/Finished";

    import axios from 'axios'
    import {mapState} from 'vuex'

    export default {
        name: 'app',
        components: {
            Courses,
            RankingGroupList,
            NotSavedWarning,
            Finished
        },
        created() {
            // eslint-disable-next-line
            this.$store.dispatch('setRuleId', BUNDLEALLOCATION.rule_id);
            // eslint-disable-next-line
            this.$store.dispatch('setCourseSetId', BUNDLEALLOCATION.courseset_id);
            if (this.$store.getters.courseSetId !== '') {
                let promises = [];
                promises.push(this.$store.dispatch("fetchCourses"));
                promises.push(new Promise(resolve => {
                    this.$store.dispatch("fetchRankingGroups")
                        .then(() => this.$store.dispatch("fetchBundleItems"))
                        .then(() => resolve())
                }));
                Promise.all(promises)
                    .then(() => {
                        this.$store.dispatch("setInitial")
                    })
                    .catch(err => console.log(err))
            }
        },
        mounted() {
            // wait until dialog has rendered
            (async () => {
                setTimeout(() => {
                    // eslint-disable-next-line
                    $('button.ui-dialog-titlebar-close').on('click', () => {
                        this.sendInitialState()
                    });
                    // eslint-disable-next-line
                    $('div.ui-widget-overlay.ui-front').on('click', () => {
                        this.sendInitialState()
                    });
                    // eslint-disable-next-line
                    $('button.cancel.ui-button.ui-corner-all.ui-widget').on('click', () => {
                        this.sendInitialState()
                    });
                    // eslint-disable-next-line
                    $('button.accept').on('click', () => {
                        this.sendCurrentState()
                    })
                }, 1);
            })();

        },
        computed: {
            ...mapState(["courseSetId", "initialState", "courses", "ruleId", "rankingGroups", "bundleItems"]),
            currentComponent: function () {
                // eslint-disable-next-line
                if ($('.hidden-alert').is(':visible') || this.$store.state.courseSetId === '') {
                    return 'NotSavedWarning'
                } else {
                    return this.$store.state.components[this.$store.state.step]
                }
            },
            currentProps: function () {
                return {}
            }
        },
        methods: {
            sendInitialState: function () {
                if (this.courseSetId !== '') {
                    let coursedata = this.initialState.courses.map(course => ({
                        seminar_id: course.seminar_id,
                        capacity: course.capacity
                    }));
                    axios.post('/plugins.php/bundleallocationplugin/config/courses_capacity/', coursedata)
                        .catch(err => console.log(err));
                    axios.post('/plugins.php/bundleallocationplugin/config/ranking_groups/' + this.initialState.ruleId, this.initialState.rankingGroups)
                        .catch(err => console.log(err));
                    this.initialState.rankingGroups.forEach(group => {
                        axios.post('/plugins.php/bundleallocationplugin/config/bundleitems/' + group.group_id,
                            this.initialState.bundleItems.filter(bundleItem => bundleItem.group_id === group.group_id))
                    });
                }
            },
            sendCurrentState: function () {
                if (this.courseSetId !== '') {
                    let coursedata = this.courses.map(course => ({
                        seminar_id: course.seminar_id,
                        capacity: course.capacity
                    }));
                    axios.post('/plugins.php/bundleallocationplugin/config/courses_capacity/', coursedata)
                        .catch(err => console.log(err));
                    axios.post('/plugins.php/bundleallocationplugin/config/ranking_groups/' + this.ruleId, this.rankingGroups)
                        .catch(err => console.log(err));
                    this.rankingGroups.forEach(group => {
                        axios.post('/plugins.php/bundleallocationplugin/config/bundleitems/' + group.group_id,
                            this.bundleItems.filter(bundleItem => bundleItem.group_id === group.group_id))
                            .catch(err => console.log(err))
                    });
                }
            }
        }
    }
</script>

<style>
    @media (min-width: 768px) {
        #app {
            width: 75vw;
        }
    }
</style>