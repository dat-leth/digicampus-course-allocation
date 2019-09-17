<template>
    <table class="default">
        <caption>
            {{ group.group_name }} ({{ group.users.length }} Anmeldungen)
        </caption>
        <colgroup>
            <col width="20px">
            <col width="25%">
            <col width="25%">
            <col width="7px">
            <col width="7px">
            <col width="75%">
        </colgroup>
        <thead>
        <tr>
            <th></th>
            <th>Nachname</th>
            <th>Vorname</th>
            <th>Priorität</th>
            <th>Warteliste</th>
            <th>Veranstaltung</th>
        </tr>
        </thead>
        <UserEntry v-for="user in sortedUsers" :key="user.user_id" :user="user" :availableCourses="availableCourses"
                   :groupId="group.group_id"></UserEntry>
    </table>
</template>

<script>
    import UserEntry from "./UserEntry";

    export default {
        name: "GroupTable",
        components: {UserEntry},
        props: ["group"],
        data: () => {
            return {
                currentSort: 'nachname',
                currentSortDir: 'asc',
            }
        },
        computed: {
            availableCourses() {
                return this.group.available_courses.slice().sort((a, b) => {
                    return a.course.name.localeCompare(b.course.name)
                })
            },
            sortedUsers() {
                return this.group.users.slice().sort((a, b) => {
                    let modifier = 1;
                    if (this.currentSortDir === 'desc') modifier = -1;
                    if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
                    if (a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
                    return 0;
                });
            }
        }
    }
</script>

<style scoped>

</style>