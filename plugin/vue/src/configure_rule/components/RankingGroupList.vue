<template>
    <div id="ranking_groups">
        <h3>Schritt 3: Zuteilungsgruppen konfigurieren</h3>
        <section class="contentbox">
            <div class="messagebox messagebox_info" v-if="Object.keys(rule.groups).length === 0">
                Keine Zuteilungsgruppe vorhanden.
            </div>
            <RankingGroup v-for="(group, id) in rule.groups" :key="id" :id="id" :group="group"/>
            <button class="button" v-on:click.prevent="addGroup">Neue Zuteilungsgruppe erstellen</button>
        </section>
    </div>
</template>

<script>
    import {mapState} from "vuex";
    import RankingGroup from "./RankingGroup";
    export default {
        name: "RankingGroupList",
        components: {RankingGroup},
        computed: {
            ...mapState(['rule'])
        },
        methods: {
            addGroup() {
                this.$store.dispatch('addGroup')
            }
        }
    }
</script>

<style scoped>
    section.contentbox {
        border: none;
        margin: 0;
        padding: 0;
    }
</style>