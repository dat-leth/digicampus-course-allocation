<template>
    <div id="ranking-list">
        <h4>Ausgewählte Veranstaltungen</h4>
        <em v-if="ranking.length === 0">keine Veranstaltung gewählt</em>
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