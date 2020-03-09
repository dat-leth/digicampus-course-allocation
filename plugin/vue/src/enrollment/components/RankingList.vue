<i18n>
{
	"de_DE": {
		"selected_courses": "Ausgewählte Veranstaltungen",
		"zero_selected": "keine Veranstaltung gewählt"
	},
	"en_GB": {
		"selected_courses": "Ranked courses",
		"zero_selected": "No courses ranked yet"
	}
}
</i18n>

<template>
    <div id="ranking-list">
        <h4>{{ $t('selected_courses') }}</h4>
        <em v-if="ranking.length === 0">{{ $t('zero_selected') }}</em>
        <draggable v-model="ranking" handle=".handle">
            <RankingItem v-for="(itemId, index) in ranking" :key="itemId" :itemId="itemId" :index="index"></RankingItem>
        </draggable>

    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    import RankingItem from "./RankingItem";

    export default {
        name: "RankingList",
        components: {
            draggable,
            RankingItem
        },
        computed: {
            ranking: {
                get() {
                    return this.$store.state.ranking
                },
                set(val) {
                    this.$store.dispatch("setRanking", val)
                }
            }
        }
    }
</script>

<style scoped>
    #ranking-list {
        flex: 0 1 100%
    }

    @media (min-width: 768px) {
        #ranking-list {
            flex: 0 1 calc(50% - 1em);
            margin-left: 1em;
        }
    }
</style>