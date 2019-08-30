<template>
    <div id="ranking_groups">
        <h4>Schritt {{ step + 1 }}: Zuteilungsgruppen konfigurieren</h4>
        <p>
            Für jede Zuteilungsgruppe kann jeweils unabhängig von den anderen Gruppen Prioritäten zu Veranstaltungen
            abgegeben werden. Es wird jedem Studierenden ein (1) Kurs pro Gruppe zugeteilt. Parallel stattfindende
            Veranstaltungen können für die Prioritätenerhebung zusammengefasst werden.
        </p>
        <section class="contentbox">
            <div class="messagebox messagebox_info" v-if="rankingGroups.length === 0">
                Keine Zuteilungsgruppe vorhanden.
            </div>
            <ranking-group v-for="(group, index) in rankingGroups" :key="group.group_id" :index="index"
                           :group="group"></ranking-group>
        </section>
        <button class="button" v-on:click.prevent="addRankingGroup">Neue Zuteilungsgruppe erstellen</button>
        <br/>
        <button class="button" v-on:click.prevent="prevStep" v-if="this.$store.state.step > 0">Zurück</button>
        <button class="button" v-on:click.prevent="nextStep"
                v-if="this.$store.state.components.length - 1 > this.$store.state.step">
            Weiter
        </button>
    </div>

</template>

<script>
    import {mapState} from 'vuex'
    import RankingGroup from "./RankingGroup"
    import axios from 'axios'

    export default {
        name: "RankingGroupList",
        components: {
            RankingGroup
        },
        computed: {
            ...mapState(["step", "rankingGroups", "ruleId"])
        },
        methods: {
            nextStep: function () {
                axios.post('/plugins.php/bundleallocationplugin/config/ranking_groups/' + this.ruleId, this.rankingGroups)
                    .then(() => this.$store.dispatch("nextStep"))
                    .catch(err => console.log(err))
            },
            prevStep: function () {
                this.$store.dispatch("prevStep")
            },
            addRankingGroup: function () {
                this.$store.dispatch("addRankingGroup")
            }
        },
    }
</script>

<style scoped>
    section.contentbox {
        border: none;
        margin: 0;
        padding: 0;
    }
</style>