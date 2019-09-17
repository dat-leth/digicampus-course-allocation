<template>
    <tbody>
    <tr>
        <th @click="open = !open">
            <svg v-if="!open" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                 shape-rendering="geometricPrecision" fill="#24437c">
                <path d="M7.912 8l-4.484 4.488 2.328 2.326 6.816-6.818-6.814-6.81-2.33 2.328z"/>
            </svg>
            <svg v-else width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                 shape-rendering="geometricPrecision" fill="#24437c">
                <path d="M8 7.912L3.512 3.428 1.186 5.756l6.817 6.816 6.811-6.814-2.328-2.33z"/>
            </svg>
        </th>
        <th @click="open = !open">{{ user.nachname }}</th>
        <th @click="open = !open">{{ user.vorname }}</th>
        <th @click="open = !open">{{ prioritySelected }} / {{ user.items.length }}</th>
        <th style="text-align: center"><label>
            <input type="checkbox" v-model="waitlist" :disabled="allocatedCourse === 'noallocation'"/>
        </label></th>
        <th>
            <label>
                <select v-model="allocatedCourse">
                    <option hidden value="noallocation">Noch keine zugeteilte Veranstaltung</option>
                    <option v-for="item in availableCourses" :key="item.course.seminar_id" :value="{itemId: item.item_id, seminarId: item.course.seminar_id}">
                        {{ item.course.name }} ({{ amountParticipants(groupId, item.course.seminar_id) }} / {{ item.course.capacity }})
                    </option>
                </select>
            </label>
        </th>
    </tr>

    <template v-for="(item, index) in user.items">
        <tr :key="item.item_id" v-if="open">
            <td></td>
            <td></td>
            <td></td>
            <td>{{ index + 1 }}</td>
            <td></td>
            <td>
                <ul v-for="course in item.courses" :key="course.seminar_id">
                    <li>
                        <strong>{{ course.name }}</strong><br>
                        <span v-html="course.formatted_date"></span>
                    </li>
                </ul>
            </td>
        </tr>
    </template>
    </tbody>
</template>

<script>
    import {mapGetters} from 'vuex';

    export default {
        name: "UserEntry",
        props: ["user", "groupId", "availableCourses"],
        data: () => {
            return {
                open: false
            }
        },
        computed: {
            ...mapGetters(["amountParticipants"]),
            allocatedCourse: {
                get() {
                    if (this.$store.getters.allocatedCourse(this.user.user_id, this.groupId) == null) {
                        return 'noallocation'
                    } else {
                        let course = this.$store.getters.allocatedCourse(this.user.user_id, this.groupId);
                        return {
                            itemId: course.item_id,
                            seminarId: course.seminar_id
                        };
                    }
                },
                set(value) {
                    this.$store.dispatch("setPrelimCourse", {
                        groupId: this.groupId,
                        userId: this.user.user_id,
                        itemId: value.itemId,
                        seminarId: value.seminarId,
                        priority: this.user.items.findIndex(item => item.item_id === value.itemId)
                    })
                }
            },
            waitlist: {
                get() {
                    if (this.$store.getters.allocatedCourse(this.user.user_id, this.groupId) == null) {
                        return false
                    } else {
                        return this.$store.getters.allocatedCourse(this.user.user_id, this.groupId).waitlist
                    }
                },
                set(value) {
                    if (this.allocatedCourse !== 'noallocation') {
                        this.$store.dispatch("setPrelimWaitlist", {
                            groupId: this.groupId,
                            userId: this.user.user_id,
                            waitlist: value
                        })
                    }
                }
            },
            prioritySelected() {
                if (this.allocatedCourse !== 'noallocation') {
                    let index = this.user.items.findIndex(item => item.item_id === this.allocatedCourse.itemId);
                    if (index === -1) {
                        return '?';
                    }
                    return index + 1;
                } else {
                    return '?'
                }
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