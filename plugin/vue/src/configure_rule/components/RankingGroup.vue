<i18n>
{
	"de_DE": {
		"group_name": "Name",
		"length_hint": "Zeichen verbleibend: ",
		"min_prios": "Minimale Anzahl einzureichende Prioritäten",
		"assigned_courses": "Zugeordnete Veranstaltungen",
		"course": "Veranstaltung",
		"times_rooms": "Zeit/Veranstaltungsort",
		"capacity": "max. Teilnehmendenanzahl",
		"zero_assigned": "keine zugeordneten Veranstaltungen",
		"actions": "Aktionen",
		"delete": "Zuordnung löschen",
		"merge": "Zusammenfassen",
		"split": "Trennen",
		"execute": "Ausführen",
		"available_courses": "Keiner Zuteilungsgruppe zugeordnet",
		"zero_to_be_assigned": "keine zuzuordnenden Veranstaltungen"
	},
	"en_GB": {
		"group_name": "Name",
		"length_hint": "Remaining characters: ",
		"min_prios": "min. Number of Priorities to be submitted:",
		"assigned_courses": "Assigned courses",
		"course": "Course",
		"times_rooms": "Time/Course location",
		"capacity": "max. Number of Participants",
		"zero_assigned": "No assigned courses",
		"actions": "Actions",
		"delete": "Delete",
		"merge": "Merge",
		"split": "Split",
		"execute": "Execute",
		"available_courses": "Available courses",
		"zero_to_be_assigned": "No courses to be assigned"
	}
}
</i18n>
<template>
    <article class="">
        <header>
            <h1>
                <a href="#">
                    {{ group.groupName }}
                </a>
            </h1>
            <nav>
                <a v-if="!rule.locked" v-on:click.prevent="deleteGroup" data-dialog="size=auto;">
                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                         shape-rendering="geometricPrecision" fill="#24437c">
                        <path d="M9.759 2.438V1.03H6.24v1.408H2.717v1.917h10.565V2.438H9.759zM3.661 14.97h8.645V5.388H3.661v9.582zm6.163-8.075h1.016v6.567H9.824V6.895zm-2.35 0h1.017v6.567H7.474V6.895zm-2.349 0h1.016v6.567H5.125V6.895z"/>
                    </svg>
                </a>
            </nav>
        </header>
        <section>
            <label for="name">
                <span class="required">{{ $t('group_name' )}}</span>
                <div class="length-hint-wrapper" style="width: 667px;">
                    <div class="length-hint" style="display: none;">
                        {{ $t('length_hint' )}}<span class="length-hint-counter">255</span>
                    </div>
                    <input type="text" id="name" size="75" maxlength="255" required aria-required="true"
                           v-model="group.groupName" :readonly="rule.locked">
                </div>
            </label>
            <label for="min_amount_prios">
                <span class="required">{{ $t('min_prios' )}}</span>
                <input type="number" id="min_amount_prios" min="0" :max="Object.keys(group.bundleItems).length"
                       v-model="group.minAmountPrios" :readonly="rule.locked"/>
            </label>
            <label for="assigned">
                <span class="required">{{ $t('assigned_courses' )}}</span>
            </label>
            <table id="assigned" class="labeled default">
                <thead>
                <tr>
                    <th></th>
                    <th>{{ $t('course' )}}</th>
                    <th>{{ $t('times_rooms' )}}</th>
                    <th>{{ $t('capacity' )}}</th>
                </tr>
                </thead>
                <colgroup>
                    <col width="35"/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <tbody>
                <tr v-if="Object.keys(group.bundleItems).length === 0">
                    <td></td>
                    <td colspan="3"><em>{{ $t('zero_assigned' )}}</em></td>
                </tr>
                <template v-for="[item_id, bundle] in sortedBundleItems">
                    <tr v-for="(course, i) in bundleCourseDetails(bundle.courses)" :key="course.course_id">
                        <td :rowspan="Object.keys(bundle.courses).length" v-if="i === 0"
                            :style="mergedIndicator(bundle)">
                            <input type="checkbox" v-model="checkboxed[item_id]" :disabled="rule.locked"/>
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
                        <select class="select-action" v-model="action" :disabled="selectedBundleItems.length === 0 || rule.locked">
                            <option selected disabled value="">{{ $t('actions' )}}</option>
                            <option value="delete" v-if="selectedBundleItems.length > 0">{{ $t('delete' )}}</option>
                            <option value="merge" v-if="selectedBundleItems.length > 1">{{ $t('merge' )}}</option>
                            <option value="split" v-if="splittableBundleItem">{{ $t('split' )}}</option>
                        </select>
                        <button class="button" @click.prevent="delegateAction" :disabled="rule.locked">{{ $t('execute' )}}</button>
                    </td>
                </tr>
                </tfoot>
            </table>
            <label for="non_assigned">
                {{ $t('available_courses' )}}
            </label>
            <table id="non_assigned" class="labeled default">
                <thead>
                <tr>
                    <th></th>
                    <th>{{ $t('course') }}</th>
                    <th>{{ $t('times_rooms') }}</th>
                    <th>{{ $t('capacity') }}</th>
                </tr>
                </thead>
                <colgroup>
                    <col width="35"/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <tbody>
                <tr v-if="notAssignedCourseDetails.length === 0">
                    <td></td>
                    <td colspan="3"><em>{{ $t('zero_to_be_assigned' )}}</em></td>
                </tr>
                <tr v-for="course in notAssignedCourseDetails" :key="course.course_id">
                    <td>
                        <button class="add-button" @click.prevent="addCourseToGroup(course.course_id)" :disabled="rule.locked">
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
        <input type="hidden" :name="`groups[${id}]`" :value="JSON.stringify(group)"/>
    </article>
</template>

<script>

    import {mapGetters, mapState} from "vuex";

    export default {
        name: "RankingGroup",
        data: () => {
            return {
                checkboxed: {},
                action: '',
            }
        },
        props: ['id', 'group'],
        computed: {
            ...mapState(['course_infos', 'rule']),
            ...mapGetters(['notAssignedCourseDetails']),
            selectedBundleItems() {
                return Object.keys(this.checkboxed).filter(itemId => this.checkboxed[itemId] === true)
            },
            splittableBundleItem() {
                if (this.selectedBundleItems.length === 1) {
                    return Object.keys(this.group.bundleItems[this.selectedBundleItems[0]].courses).length > 1;
                }
                return false;
            },
            sortedBundleItems() {
                let bundleItems = {...this.group.bundleItems};
                let sortable = false;
                for (const item_id in bundleItems) {
                    if (this.course_infos[Object.keys(bundleItems[item_id].courses)[0]] !== undefined) {
                        bundleItems[item_id].sortingName = this.course_infos[Object.keys(bundleItems[item_id].courses)[0]].name;
                        sortable = true;
                    } else {
                        delete bundleItems[item_id];
                    }
                }
                if (sortable) {
                    return Object.entries(bundleItems).sort(([, itemA], [, itemB]) => {
                        return itemA.sortingName.localeCompare(itemB.sortingName);
                    })
                } else {
                    return [];
                }
            },
        },
        methods: {
            mergedIndicator(bundle) {
                if (Object.keys(bundle.courses).length > 1) {
                    return {borderRight: '3px solid #e7ebf1'};
                }
                return {};
            },
            bundleCourseDetails(courses) {
                let details = [];
                for (const course_id in courses) {
                    if (courses.hasOwnProperty(course_id) && this.course_infos.hasOwnProperty(course_id)) {
                        details.push({...this.course_infos[course_id]})
                    }
                }
                details.sort((a, b) => a.name.localeCompare(b.name));
                return details;
            },
            deleteGroup() {
                this.$store.dispatch('deleteGroup', {group_id: this.id})
            },
            addCourseToGroup(course_id) {
                this.$store.dispatch('addBundleItem', {group_id: this.id, course_ids: [course_id]})
            },
            delegateAction() {
                if (this.action === 'delete') {
                    this.$store.dispatch('deleteBundleItems', {group_id: this.id, item_ids: this.selectedBundleItems})
                } else if (this.action === 'merge') {
                    this.$store.dispatch('mergeBundleItems', {group_id: this.id, item_ids: this.selectedBundleItems})
                } else if (this.action === 'split') {
                    this.$store.dispatch('splitBundleItem', {group_id: this.id, item_id: this.selectedBundleItems[0]})
                }
                this.action = '';
                this.checkboxed = {};
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

    section > table.labeled {
        margin-top: -1ex;
        margin-bottom: 1.5ex;
    }
</style>