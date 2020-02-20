<template>
    <div id="course_capacity">
        <h3>Schritt 2: Kapazitäten der Veranstaltungen festlegen</h3>
        <table class="default">
            <thead>
            <tr>
                <th></th>
                <th>Veranstaltung</th>
                <th>Zeit/Veranstaltungsort</th>
                <th><span class="required">max. Teilnehmendenanzahl</span></th>
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
                        <img title="Veranstaltungsdetails aufrufen"
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