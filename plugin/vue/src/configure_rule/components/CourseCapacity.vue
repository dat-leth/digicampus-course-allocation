<i18n>
{
	"de_DE": {
		"step2_header": "Schritt 2: Kapazitäten der Veranstaltungen festlegen",
		"course": "Veranstaltung",
		"times_rooms": "Zeit/Veranstaltungsort",
		"capacity": "max. Teilnehmendenanzahl",
		"course_details": "Veranstaltungsdetails aufrufen"
	},
	"en_GB": {
		"step2_header": "Step 2: Set course capacities",
		"course": "Course",
		"times_rooms": "Time/Course location",
		"capacity": "max. Number of Participants",
		"course_details": "Display course details"
	}
}
</i18n>

<template>
    <div id="course_capacity">
        <h3>{{ $t('step2_header') }}</h3>
        <table class="default">
            <thead>
            <tr>
                <th></th>
                <th>{{ $t('course') }}</th>
                <th>{{ $t('times_rooms') }}</th>
                <th><span class="required">{{ $t('capacity') }}</span></th>
            </tr>
            </thead>
            <colgroup>
                <col width="18">
                <col>
                <col>
                <col>
            </colgroup>
            <tbody>
            <tr v-for="(course, index) in course_infos" :key="index">
                <td>
                    <a :href="`/dispatch.php/course/details/index/${course.course_id}`"
                       data-dialog="">
                        <img :title="$t('course_details')"
                             src="/assets/images/icons/grey/info-circle.svg"
                             alt="Veranstaltungsdetails aufrufen" class="icon-role-inactive icon-shape-info-circle"
                             width="16" height="16">
                    </a>
                </td>
                <td>{{ course.name }}</td>
                <td><span v-html="course.times_rooms"></span></td>
                <td><input type="number" min="0" placeholder="0" :name="`course_capacity[${course.course_id}]`"
                           :value="course.capacity" @input="updateCapacity($event, course.course_id)"/></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "CourseCapacity",
        computed: {
            ...mapState(['course_infos', 'rule'])
        },
        methods: {
            updateCapacity(e, course_id) {
                this.$store.dispatch('updateCapacity', {course_id: course_id, capacity: e.target.value})
            }
        }
    }
</script>

<style scoped>

</style>