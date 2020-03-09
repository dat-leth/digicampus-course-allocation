<i18n>
{
	"de_DE": {
		"applications": "Anmeldungen",
		"lname": "Nachname",
		"fname": "Vorname",
		"prio": "Priorität",
		"waitlist": "Warteliste",
		"course": "Veranstaltung",
		"filter": "Zeige nur Anmeldungen ohne Zuteilung"
	},
	"en_GB": {
		"applications": "applications",
		"lname": "Last name",
		"fname": "First name",
		"prio": "Priority",
		"waitlist": "Waiting list",
		"course": "Course",
		"filter": "Only display applications without assignment"
	}
}
</i18n>

<template>
    <div id="group_table">
        <table class="default">
            <caption>{{ group.group_name }} ({{ sortedUsers.length }} {{ $t('applications') }})</caption>
            <colgroup>
                <col width="20px">
                <col width="25%">
                <col width="25%">
                <col width="7px">
                <col width="10px">
                <col width="50%">
            </colgroup>
            <thead>
            <tr>
                <th></th>
                <th>{{ $t('lname') }}</th>
                <th>{{ $t('fname') }}</th>
                <th>{{ $t('prio') }}</th>
                <th>{{ $t('waitlist') }}</th>
                <th>
                    {{ $t('course') }}
                    <label :title="$t('filter')">
                        <input type="checkbox" v-model="filtered">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" fill="#24437c"><path d="M4.655 9.135h6.69l1.675-2.901H2.98zM0 1.072l1.771 3.067h12.458L16 1.072zm8 13.856l2.135-3.698h-4.27z"/></svg>
                    </label>
                </th>
            </tr>
            </thead>
            <UserEntry v-for="user in sortedUsers" :key="user.user_id" :user="user" :availableCourses="availableCourses"
                       :groupId="group.group_id"></UserEntry>
        </table>
    </div>
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
                filtered: false
            }
        },
        computed: {
            availableCourses() {
                return this.group.available_courses.slice().sort((a, b) => {
                    return a.course.name.localeCompare(b.course.name)
                })
            },
            sortedUsers() {
                if (this.filtered === true) {
                    return this.group.users.slice()
                        .filter(user => this.$store.getters.allocatedCourse(user.user_id, this.group.group_id) == null)
                        .sort((a, b) => {
                            let modifier = 1;
                            if (this.currentSortDir === 'desc') modifier = -1;
                            if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
                            if (a[this.currentSort] > b[this.currentSort]) return modifier;
                            return 0;
                        });
                }
                return this.group.users.slice()
                    .sort((a, b) => {
                    let modifier = 1;
                    if (this.currentSortDir === 'desc') modifier = -1;
                    if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
                    if (a[this.currentSort] > b[this.currentSort]) return modifier;
                    return 0;
                });
            }
        }
    }
</script>

<style scoped>

</style>