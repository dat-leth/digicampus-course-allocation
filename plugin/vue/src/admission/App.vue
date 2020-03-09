<i18n>
{
	"de_DE": {
		"close": "Nachrichtenbox schließen",
		"saved": "Änderungen wurden gespeichert.",
		"no_applications": "Keine Anmeldungen vorhanden.",
		"save": "Speichern",
		"save_enroll": "Speichern und eintragen",
		"confirm_enroll": "Die Teilnehmer werden hiermit in die entsprechenden Veranstaltungen eingetragen. Soll die Eintragung durchgeführt werden?",
		"error": "Änderungen konnten nicht gespeichert werden.",
		"recalculate": "Neuberechnung anfordern",
		"confirm_recalculate": "Das jetzige Ergebnis wird unwiderruflich gelöscht. Eine neue Verteilung wird anhand von ggf. aktualisierten Kapazitäten berechnet. Möchten Sie wirklich eine Neuberechnung anfordern?",
		"note": "Hinweis:",
		"note_content": "Wenn Sie eine Neuberechnung anhand von aktualisierten Kapazitäten durchführen wollen, vergewissern Sie sich, dass die erwarteten Teilnehmendenanzahlen der Veranstaltungen angepasst wurden. Änderungen über die Anmelderegel erfordern das Speichern der Regel sowie des Anmeldesets.",
		"recalculation": "Neuverteilung"
	},
	"en_GB": {
		"close": "Dismiss",
		"saved": "Changes have been saved.",
		"no_applications": "There are no applications.",
		"save": "Save",
		"save_enroll": "Save and Enroll",
		"confirm_enroll": "Applicants will be enrolled in their respective courses. Are you sure to finalize the assignment?",
		"error": "Changes have not been saved.",
		"recalculate": "Request recalculation",
		"confirm_recalculate": "This current assignment will be permanently deleted. A new assignment of applicants to courses will be calculated with respect to updated capacities. Are you sure that you want to a recalculation?",
		"note": "Please note:",
		"note_content": "If a new assignment of applicants to courses shall be calculated with respect to updated capacities, do ensure that the expected number of participants of each course is set accordingly. Changes via the admission rule require an explicit saving of the rule as well as the admission set.",
		"recalculation": "Requesting a recalculation"
	}
}
</i18n>
<template>
    <div id="app">
        <div class="messagebox messagebox_error" v-if="error.length !== 0">
            <div class="messagebox_buttons">
                <a class="close" href="#" :title="$t('close')"
                   v-on:click.prevent="error = ''">
                    <span>{{ $t('close') }}</span>
                </a>
            </div>
            {{ error }}
        </div>
        <div class="messagebox messagebox_success" v-if="success === true">
            <div class="messagebox_buttons">
                <a class="close" href="#" :title="$t('close')"
                   v-on:click.prevent="success = false">
                    <span>{{ $t('close') }}</span>
                </a>
            </div>
            {{ $t('saved') }}
        </div>
        <div class="messagebox messagebox_info" v-if="groups.length === 0">
            {{ $t('no_applications') }}
        </div>
        <GroupTable v-for="group in groups" :key="group.group_id" :group="group"></GroupTable>
        <button class="button" @click="submit" v-if="groups.length > 0">{{ $t('save') }}</button>
        <button class="button" @click.prevent="submitAndEnroll" v-if="groups.length > 0 && prelim.length > 0">{{ $t('save_enroll') }}</button>
        <hr>
        <div>
            <h3>{{ $t('recalculation') }}</h3>
            <p><strong>{{ $t('note') }} </strong>{{ $t('note_content') }}</p>
            <button class="button" @click.prevent="recalculate" v-if="groups.length > 0">{{ $t('recalculate') }}</button>
        </div>
    </div>
</template>

<script>
    import GroupTable from "./components/GroupTable";
    import {mapState} from 'vuex';
    import axios from 'axios';

    export default {

        name: 'app',
        components: {
            GroupTable
        },
        data: () => {
            return {
                error: '',
                success: false,
            }
        },
        computed: {
            ...mapState(['groups', 'prelim', 'coursesetId'])
        },
        methods: {
            submitAndEnroll() {
                // eslint-disable-next-line
                STUDIP.Dialog.confirm(this.$t('confirm_enroll'), () => {
                    this.submit(true);
                });
            },
            submit(finalize) {
                let params = {finalize: false};
                if (finalize === true) {
                    params.finalize = true;
                }
                axios.post('/plugins.php/bundleallocationplugin/admission/submit_alloc/' + this.coursesetId, this.prelim, {params: params})
                    .then(() => {
                        this.success = true;
                        this.error = '';
                    })
                    .catch(() => {
                        this.error = this.$t('error');
                        this.success = false;
                    })
            },
            recalculate() {
                // eslint-disable-next-line
                STUDIP.Dialog.confirm(this.$t('confirm_recalculate'), () => {
                    axios.post('/plugins.php/bundleallocationplugin/admission/recalculate/' + this.coursesetId)
                        .then(() => {
                            window.location.reload();
                        })
                        .catch(() => {
                            this.error = this.$t('error');
                            this.success = false;
                        })
                })
            }
        }
    }
</script>

<style>
</style>
