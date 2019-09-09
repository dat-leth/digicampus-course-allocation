<template>
    <div id="schedule">
        <section class="contentbox">
            <article class="">
                <header><h1><a href="#">Stundenplan</a></h1></header>
                <section>
                    <table id="schedule_data" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <td></td>
                            <td>Montag</td>
                            <td>Dienstag</td>
                            <td>Mittwoch</td>
                            <td>Donnerstag</td>
                            <td>Freitag</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                            </td>
                            <td colspan="5" style="padding: 0px">
                                <!-- the lines separating the hours and half-hours -->
                                <div style="position: relative">
                                    <div style="position: absolute; width: 100%;">
                                        <div id="marker_8" class="schedule_marker"></div>
                                        <div id="marker_9" class="schedule_marker"></div>
                                        <div id="marker_10" class="schedule_marker"></div>
                                        <div id="marker_11" class="schedule_marker"></div>
                                        <div id="marker_12" class="schedule_marker"></div>
                                        <div id="marker_13" class="schedule_marker"></div>
                                        <div id="marker_14" class="schedule_marker"></div>
                                        <div id="marker_15" class="schedule_marker"></div>
                                        <div id="marker_16" class="schedule_marker"></div>
                                        <div id="marker_17" class="schedule_marker"></div>
                                        <div id="marker_18" class="schedule_marker"></div>
                                        <div id="marker_19" class="schedule_marker"></div>
                                        <div id="marker_20" class="schedule_marker"></div>
                                        <div id="marker_21" class="schedule_marker"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <!-- the hours for the time-table -->
                                <div class="schedule_hours">08:00</div>
                                <div class="schedule_hours">09:00</div>
                                <div class="schedule_hours">10:00</div>
                                <div class="schedule_hours">11:00</div>
                                <div class="schedule_hours">12:00</div>
                                <div class="schedule_hours">13:00</div>
                                <div class="schedule_hours">14:00</div>
                                <div class="schedule_hours">15:00</div>
                                <div class="schedule_hours">16:00</div>
                                <div class="schedule_hours">17:00</div>
                                <div class="schedule_hours">18:00</div>
                                <div class="schedule_hours">19:00</div>
                                <div class="schedule_hours">20:00</div>
                                <div class="schedule_hours">21:00</div>
                            </td>
                            <td style="vertical-align: top">
                                <!-- the days with the date for the timetable -->
                                <div id="calendar_view_2_column_0" class="schedule_day" style="overflow: hidden">
                                    <ScheduleEntry v-for="entry in schedule[0]['entries']" :entry="entry"
                                                   :key="entry.id"></ScheduleEntry>
                                </div>
                            </td>
                            <td style="vertical-align: top">
                                <!-- the days with the date for the timetable -->
                                <div id="calendar_view_2_column_1" class="schedule_day" style="overflow: hidden">
                                    <ScheduleEntry v-for="entry in schedule[1]['entries']" :entry="entry"
                                                   :key="entry.id"></ScheduleEntry>
                                </div>
                            </td>
                            <td style="vertical-align: top">
                                <!-- the days with the date for the timetable -->
                                <div id="calendar_view_2_column_2" class="schedule_day" style="overflow: hidden">
                                    <ScheduleEntry v-for="entry in schedule[2]['entries']" :entry="entry"
                                                   :key="entry.id"></ScheduleEntry>
                                </div>
                            </td>
                            <td style="vertical-align: top">
                                <!-- the days with the date for the timetable -->
                                <div id="calendar_view_2_column_3" class="schedule_day" style="overflow: hidden">
                                    <ScheduleEntry v-for="entry in schedule[3]['entries']" :entry="entry"
                                                   :key="entry.id"></ScheduleEntry>
                                </div>
                            </td>
                            <td style="vertical-align: top">
                                <!-- the days with the date for the timetable -->
                                <div id="calendar_view_2_column_4" class="schedule_day" style="overflow: hidden">
                                    <ScheduleEntry v-for="entry in schedule[4]['entries']" :entry="entry"
                                                   :key="entry.id"></ScheduleEntry>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </section>
            </article>

        </section>
    </div>

</template>

<script>
    import ScheduleEntry from "./ScheduleEntry";
    import {mapState} from 'vuex';

    export default {
        name: "Schedule",
        components: {ScheduleEntry},
        computed: {
            ...mapState(['courses', 'existing_entries', 'other_rankings']),
            schedule() {
                let schedule = this.existing_entries;
                this.courses.forEach(course => {
                    if (Array.isArray(course.turnus) && course.turnus.length !== 0) {
                        course.turnus.forEach(cycle => {
                            schedule[cycle.weekday]['entries'].push({
                                id: course.seminar_id,
                                start: `${cycle.start_hour}${cycle.start_minute}`,
                                end: `${cycle.end_hour}${cycle.end_minute}`,
                                day: cycle.weekday,
                                title: "",
                                content: course.name,
                                color: "#008512",
                                start_formatted: `${cycle.start_hour}:${cycle.start_minute}`,
                                end_formatted: `${cycle.end_hour}:${cycle.end_minute}`,
                                start_hour: cycle.start_hour,
                                start_minute: cycle.start_minute,
                                end_hour: cycle.end_hour,
                                end_minute: cycle.end_minute,
                                visible: true,
                                rankable: true,
                                bundle_item: course.item_id
                            });
                        })
                    }
                });

                let groupIds = this.other_rankings.reduce((groupIds, course) => {
                    if (!groupIds.includes(course.group_id)) {
                        groupIds.push(course.group_id);
                    }
                    return groupIds;
                }, []);

                let colors = ['#682c8b', '#b02e7c', '#129c94', '#f26e00', '#a85d45', '#6ead10', '#d60000', '#ffbd33',
                    '#66b570', '#a480b9', '#d082b0', '#70c3bf', '#f7a866' ,'#ca9eaf'];

                this.other_rankings.forEach(course => {
                    if (Array.isArray(course.turnus) && course.turnus.length !== 0) {
                        course.turnus.forEach(cycle => {
                            schedule[cycle.weekday]['entries'].push({
                                id: course.seminar_id,
                                start: `${cycle.start_hour}${cycle.start_minute}`,
                                end: `${cycle.end_hour}${cycle.end_minute}`,
                                day: cycle.weekday,
                                title: "",
                                content: course.name,
                                color: colors[groupIds.findIndex(id => id === course.group_id) % colors.length],
                                start_formatted: `${cycle.start_hour}:${cycle.start_minute}`,
                                end_formatted: `${cycle.end_hour}:${cycle.end_minute}`,
                                start_hour: cycle.start_hour,
                                start_minute: cycle.start_minute,
                                end_hour: cycle.end_hour,
                                end_minute: cycle.end_minute,
                                visible: true,
                                other_ranked: true,
                                priority: course.priority,
                            });
                        })
                    }
                });

                schedule.forEach(day => {
                    day.entries.forEach(entry => {
                        entry.concurrentEntries = day.entries.reduce((acc, other) => {
                            if (Math.max(entry.start, other.start) < Math.min(entry.end, other.end)) {
                                acc.push(other);
                            }
                            return acc;
                        }, []);
                    })
                });
                return schedule;
            }
        }
    }
</script>

<style scoped>
    @media (min-width: 768px) {
        #schedule {
            flex: 0 1 100%;
        }
    }

    div.schedule_day {
        height: 588px;
    }

    div.schedule_marker {
        height: 20px;
        line-height: 20px;
        margin-bottom: 20px;
    }

    div.schedule_hours {
        height: 40px;
    }

    section.contentbox {
        border: none;
        margin: 0;
        padding: 0;
    }

    section.contentbox > article {
        margin: 0;
    }
</style>