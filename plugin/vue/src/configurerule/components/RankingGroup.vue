<template>
    <article class="">
        <header>
            <h1>
                <a href="#">
                    {{ group.group_name }}
                </a>
            </h1>
            <nav>
                <a v-on:click.prevent="deleteGroup" data-dialog="size=auto;">
                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                         shape-rendering="geometricPrecision" fill="#24437c">
                        <path d="M9.759 2.438V1.03H6.24v1.408H2.717v1.917h10.565V2.438H9.759zM3.661 14.97h8.645V5.388H3.661v9.582zm6.163-8.075h1.016v6.567H9.824V6.895zm-2.35 0h1.017v6.567H7.474V6.895zm-2.349 0h1.016v6.567H5.125V6.895z"/>
                    </svg>
                </a>
            </nav>
        </header>
        <section>
            <label for="name" class="required">Name</label>
            <div class="length-hint-wrapper" style="width: 666.6px;">
                <div class="length-hint" style="display: none;">
                    Zeichen verbleibend: <span class="length-hint-counter">255</span>
                </div>
                <input type="text" id="name" size="75" maxlength="255" required aria-required="true"
                       :value="group.group_name" @input="updateGroupName">
            </div>
            <label class="required">Minimale Anzahl einzureichende Prioritäten</label>
            <input type="number" min="0" :max="bundleItems.length" :value="group.min_amount_prios" @input="updateMinAmountPrios"/>
            <label class="required">Zugeordnete Veranstaltungen</label>
            <table class="default">
                <thead>
                <tr>
                    <th></th>
                    <th>Veranstaltung</th>
                    <th>Regelmäßige Termine</th>
                    <th>max. Teilnehmendenanzahl</th>
                </tr>
                </thead>
                <colgroup>
                    <col width="35"/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <tbody>
                <tr v-if="bundleItems.length === 0">
                    <td></td>
                    <td colspan="3"><em>keine zugeordneten Veranstaltungen</em></td>
                </tr>
                <template v-for="(bundle) in bundleItems">
                    <tr v-for="(course, j) in courseDetails(bundle.seminar_ids)" :key="course.seminar_id">
                        <td :rowspan="bundle.seminar_ids.length" v-if="j === 0"
                            :style="{ borderRight: (bundle.seminar_ids.length > 1) ? '3px solid #e7ebf1' : '3px none'}">
                            <input type="checkbox" v-model="checkboxed[bundle.item_id]"/>
                        </td>
                        <td>{{ course.name }}</td>
                        <td><span v-html="course.times_rooms"></span></td>
                        <td>{{ course.capacity }}</td>
                    </tr>
                </template>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4">
                        <select class="select-action" v-model="action" :disabled="selectedBundleItems.length === 0">
                            <option selected disabled value="">Aktionen</option>
                            <option value="delete" v-if="selectedBundleItems.length > 0">Zuordnung löschen</option>
                            <option value="merge" v-if="selectedBundleItems.length > 1">Zusammenfassen</option>
                            <option value="split" v-if="splittableBundleItem">Trennen</option>
                        </select>
                        <button class="button" @click.prevent="delegateAction">Ausführen</button>
                    </td>
                </tr>
                </tfoot>
            </table>
            <label><strong>Keiner Zuteilungsgruppe zugeordnet</strong></label>
            <table class="default">
                <thead>
                <tr>
                    <th></th>
                    <th>Veranstaltung</th>
                    <th>Regelmäßige Termine</th>
                    <th>max. Teilnehmendenanzahl</th>
                </tr>
                </thead>
                <colgroup>
                    <col width="35"/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <tbody>
                <tr v-if="notAssignedCourses.length === 0">
                    <td></td>
                    <td colspan="3"><em>keine zuzuordnenden Veranstaltungen</em></td>
                </tr>
                <tr v-for="course in notAssignedCourses" :key="course.seminar_id">
                    <td>
                        <button class="add-button" v-on:click.prevent="addCourseToGroup(course.seminar_id)">
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                                 shape-rendering="geometricPrecision" fill="#24437c">
                                <path d="M8 1.001a7 7 0 1 0 .003 14 7 7 0 0 0-.003-14zm4.016 8.024H9.023l.002 2.943-2.048.001.001-2.943H3.984l-.001-2.05h2.994V4l2.047-.001v2.976l2.993.001-.001 2.049z"/>
                            </svg>
                        </button>
                    </td>
                    <td>{{ course.name }}</td>
                    <td><span v-html="course.times_rooms"></span></td>
                    <td>{{ course.capacity }}</td>
                </tr>
                </tbody>
            </table>
        </section>
    </article>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "RankingGroup",
        data: () => {
            return {
                checkboxed: {},
                action: ''
            }
        },
        props: ['index', 'group'],
        computed: {
            ...mapGetters(["courses"]),
            bundleItems() {
                let items = this.$store.getters.bundleItems.filter(bundleItem => bundleItem.group_id === this.group.group_id);
                items.forEach(item => {
                    item.seminar_ids.sort((a, b) => {
                        let courseA = this.courses.find(course => course.seminar_id === a);
                        let courseB = this.courses.find(course => course.seminar_id === b);
                        return courseA.name.localeCompare(courseB.name)
                    })
                });
                items.sort((a, b) => {
                    let courseA = this.courses.find(course => course.seminar_id === a.seminar_ids[0]);
                    let courseB = this.courses.find(course => course.seminar_id === b.seminar_ids[0]);
                    return courseA.name.localeCompare(courseB.name)
                });
                return items
            },
            notAssignedCourses() {
                let assignedSeminarIds = [];
                this.$store.getters.bundleItems.forEach(item => {
                    assignedSeminarIds.push(...item.seminar_ids)
                });
                return this.courses.filter(course => !assignedSeminarIds.includes(course.seminar_id))
            },
            selectedBundleItems() {
                return Object.keys(this.checkboxed).filter(itemId => this.checkboxed[itemId] === true)
            },
            splittableBundleItem() {
                if (this.selectedBundleItems.length === 1) {
                    let bundleItem = this.bundleItems.find(item => item.item_id === this.selectedBundleItems[0]);
                    return bundleItem.seminar_ids.length > 1;
                }
                return false;
            }
        },
        methods: {
            deleteGroup: function () {
                this.$store.dispatch("removeRankingGroup", this.index)
            },
            courseDetails: function (seminarIds) {
                return seminarIds.map(id => this.courses.find(course => course.seminar_id === id))
            },
            updateGroupName: function (event) {
                this.$store.dispatch("setGroupName", {id: this.group.group_id, name: event.target.value})
            },
            updateMinAmountPrios: function (event) {
                this.$store.dispatch("setGroupMinAmountPrio", {id: this.group.group_id, amount: parseFloat(event.target.value)})
            },
            addCourseToGroup: function (seminarId) {
                this.$store.dispatch("addBundleItem", {groupId: this.group.group_id, seminarIds: [seminarId]})
            },
            delegateAction: function () {
                if (this.action === 'delete') {
                    this.selectedBundleItems.forEach(itemId => this.$store.dispatch("deleteBundleItem", itemId))
                } else if (this.action === 'merge') {
                    this.$store.dispatch("mergeBundleItems", this.selectedBundleItems)
                } else if (this.action === 'split') {
                    this.$store.dispatch("splitBundleItems", {
                        groupId: this.group.group_id,
                        itemId: this.selectedBundleItems[0]
                    })
                }
                this.checkboxed = {};
                this.action = '';
            }
        }
    }
</script>

<style scoped>
    ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    section.contentbox > article {
        margin-left: 0;
        margin-right: 0;
    }

    section.contentbox > article.open > section {
        margin: 10px;
    }

    .add-button {
        border: none;
        background: none;
        height: 26px;
        width: 28px;
    }

    select.select-action {
        width: 200px;
    }
</style>