<i18n>
{
	"de_DE": {
		"avail_courses": "Verfügbare Veranstaltungen",
		"zero_avail_courses": "keine Veranstaltungen verfügbar"
	},
	"en_GB": {
		"avail_courses": "Available courses",
		"zero_avail_courses": "No courses to be ranked"
	}
}
</i18n>

<template>
    <div id="available-list">
        <h4>{{ $t('avail_courses') }}</h4>
        <em v-if="Object.keys(availableBundleItems).length === 0">{{ $t('zero_avail_courses') }}</em>
        <AvailableItem v-for="(courses, itemId) in availableBundleItems" :courses="courses" :itemId="itemId" :key="itemId"></AvailableItem>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import AvailableItem from "./AvailableItem";

    export default {
        name: "AvailableList",
        components: {
            AvailableItem
        },
        computed: {
            ...mapGetters(["bundleItems"]),
            availableBundleItems() {
                return Object.keys(this.bundleItems)
                    .filter(itemId => !this.$store.state.ranking.includes(itemId))
                    .reduce((result, itemId) => {
                        return (result[itemId] = this.bundleItems[itemId], result);
                    }, {})
            }
        }
    }
</script>

<style scoped>
    #available-list {
        flex: 0 1 100%
    }

    @media (min-width: 768px) {
        #available-list {
            flex: 0 1 calc(50% - 1em);
            margin-right: 1em;
        }
    }
</style>