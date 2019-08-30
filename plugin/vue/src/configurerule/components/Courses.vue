<template>
    <div id="courses">
        <h4>Schritt {{ step + 1 }}: Teilnehmendenanzahl konfigurieren</h4>
        <p>
            <strong>Hinweis:</strong> Bei Änderungen an der Veranstaltungszuordnung des Anmeldesets muss die
            Anmelderegel stets neukonfiguriert werden. Ansonsten kann es zur fehlerhaften Prioritätenerhebung und
            Zuteilung kommen.
        </p>
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
                <col width="18">
                <col>
                <col>
                <col>
            </colgroup>
            <tbody>
            <tr v-for="(course, index) in courses" :key="index">
                <td>
                    <a :href="'/dispatch.php/course/details/index/' + course.seminar_id"
                       data-dialog="">
                        <img title="Veranstaltungsdetails aufrufen"
                             src="/assets/images/icons/grey/info-circle.svg"
                             alt="Veranstaltungsdetails aufrufen" class="icon-role-inactive icon-shape-info-circle"
                             width="16" height="16">
                    </a>
                </td>
                <td>{{ course.name }}</td>
                <td>
                    <ul>
                        <li v-for="(cycle, j) in course.cycles" :key="j">
                            {{ cycle.weekday | weekday }}, {{ cycle.start_time | time }} - {{ cycle.end_time | time }}
                        </li>
                    </ul>
                </td>
                <td><input type="number" min="0" placeholder="0" :value="course.capacity"
                           @input="updateCapacity($event, index)"/></td>
            </tr>
            </tbody>
        </table>
        <button class="button" v-on:click.prevent="prevStep" v-if="this.$store.state.step > 0">Zurück</button>
        <button class="button" v-on:click.prevent="nextStep"
                v-if="this.$store.state.components.length - 1 > this.$store.state.step">
            Weiter
        </button>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    import moment from 'moment'
    import axios from 'axios'

    export default {
        name: "Courses",
        computed: {
            ...mapState(["courses", "step", "courseSetId"])
        },
        methods: {
            nextStep: function () {
                if (document.querySelector('#ruleform').reportValidity()) {
                    let data = this.courses.map(course => ({seminar_id: course.seminar_id, capacity: course.capacity}));
                    axios.post('/plugins.php/bundleallocationplugin/config/courses_capacity/', data)
                        .then(this.$store.dispatch("nextStep"))
                        .catch(err => console.log(err))
                }
            },
            prevStep: function () {
                this.$store.dispatch("prevStep")
            },
            updateCapacity: function (event, index) {
                this.$store.dispatch('setCourseCapacity', {index: index, capacity: parseFloat(event.target.value)})
            }
        },
        filters: {
            weekday: function (number) {
                return moment().isoWeekday(number).format('dddd');
            },
            time: function (time) {
                return moment(time, 'HH:mm:ss').format('HH:mm')
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
</style>