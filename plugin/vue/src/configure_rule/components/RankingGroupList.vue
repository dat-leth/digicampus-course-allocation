<i18n>
{
	"de_DE": {
		"step3_header": "Schritt 3: Zuteilungsgruppen konfigurieren",
		"no_group": "Keine Zuteilungsgruppe vorhanden.",
		"add_group": "Neue Zuteilungsgruppe erstellen",
		"locked": "Es können keine Änderungen an Zuteilungsgruppen durchgeführt werden, nachdem Prioritäten bereits abgegeben wurden."
	},
	"en_GB": {
		"step3_header": "Step 3: Configure allocation groups",
		"no_group": "There are no allocation groups.",
		"add_group": "Create new allocation group",
		"locked": "Since applications to courses has already been submitted, you cannot change the current allocation group configuration."
	}
}
</i18n>

<template>
    <div id="ranking_groups">
        <h3>{{ $t('step3_header') }}</h3>
        <section class="contentbox">
            <div class="messagebox messagebox_info" v-if="Object.keys(rule.groups).length === 0">
                {{ $t('no_group') }}
            </div>
            <div class="messagebox messagebox_info" v-if="rule.locked">
                {{ $t('locked') }}
            </div>
            <RankingGroup v-for="(group, id) in rule.groups" :key="id" :id="id" :group="group"/>
            <button class="button" v-on:click.prevent="addGroup" :disabled="rule.locked">{{ $t('add_group') }}</button>
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