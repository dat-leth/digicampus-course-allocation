<template>
    <div id="app">
        <DistDateTime v-show="step === 0"/>
        <CourseCapacity v-show="step === 1"/>
        <RankingGroupList v-show="step === 2"/>

        <button class="button" v-on:click.prevent="prevStep" :disabled="!step > 0">{{ $t('prevStep') }}</button>
        <button class="button" v-on:click.prevent="nextStep" :disabled="step >= 2">{{ $t('nextStep') }}</button>
    </div>
</template>
<i18n>
    {
    "de_DE": {
    "nextStep": "Weiter",
    "prevStep": "Zurück"
    },
    "en_GB": {
    "nextStep": "Next",
    "prevStep": "Back"
    }
    }
</i18n>
<script>
    import DistDateTime from "./components/DistDateTime";
    import CourseCapacity from "./components/CourseCapacity";
    import RankingGroupList from "./components/RankingGroupList";

    export default {
        name: 'configure_rule',
        components: {
            RankingGroupList,
            CourseCapacity,
            DistDateTime,
        },
        data: () => {
            return {
                step: 0
            }
        },
        created() {
            // eslint-disable-next-line
            const instance = STUDIP.Dialog.getInstance('configurerule');
            if (instance !== undefined) {
                // eslint-disable-next-line
                jQuery(instance.element).dialog('option', 'width', jQuery(window).width() * 0.9);
                // eslint-disable-next-line
                jQuery(instance.element).dialog('option', 'height', jQuery(window).height() * 0.8);
            }
            this.$store.dispatch("initialState");
            this.$root.$i18n.locale = this.$store.state.lang;
        },
        methods: {
            nextStep() {
                if (this.step < 2) {
                    this.step++
                }
            },
            prevStep() {
                if (this.step > 0) {
                    this.step--
                }
            }
        }
    }
</script>

<style>
</style>
