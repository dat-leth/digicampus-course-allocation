<i18n>
{
	"de_DE": {
		"close": "Nachrichtenbox schließen",
		"not_started": "Die Anmeldung ist erst ab dem {date} möglich.",
		"over_msg": "Die Abgabe von Prioritäten ist nicht mehr möglich. Die automatische Verteilung der Plätze geschah am {date}. Kontaktieren Sie die verantwortliche Lehrperson.",
		"box_header": "Die Plätze dieser Veranstaltung werden automatisch verteilt.",
		"details_1ofX": "Sie können 1 von {max} Veranstaltungen belegen. Bei der Verteilung werden die von Ihnen gewünschten Prioritäten sowie Überschneidungen zwischen Veranstaltungen von {others} berücksichtigt.",
		"details_overlaps": "Überschneidungen zu Veranstaltungen außerhalb dieses Anmeldeverfahrens werden NICHT berücksichtigt.",
		"details_min_prios": "Es müssen mindestens {amount} Prioritäten abgegeben werden.",
		"details_dist_time": "Zeitpunkt der Verteilung: {date}",
		"saved": "Prioritäten wurden gespeichert.",
		"error_save": "Prioritäten konnten nicht gespeichert werden.",
		"error_min_prios": "Es müssen mindestens {amount} Prioritäten abgegeben werden."
	},
	"en_GB": {
		"close": "Dismiss",
		"not_started": "Registration is open on the {date}. You can only submit your preferences then.",
		"over_msg": "You can no longer submit your preferences. Seats will be assigned at {date}. Please contact the lecturers of this course.",
		"box_header": "Seats of this course will be assigned automatically.",
		"details_1ofX": "You may enroll in 1 of the following {max} courses. Your priorities will be accounted for.",
		"details_overlaps": "The assignment of applicants to courses of groups {others} is non-overlapping. Overlaps with your current courses or other registration procedures are NOT taken into consideration.",
		"details_min_prios": "You have to rank atleast {amount} courses.",
		"details_dist_time": "Date of assignment: {date}",
		"saved": "Your priorities have been saved.",
		"error_save": "Your priorities has not been saved.",
		"error_min_prios": "Please rank atleast {amount} courses."
	}
}
</i18n>

<template>
    <div id="app">
        <div class="messagebox messagebox_warning" v-if="!applicationPeriodStarted">
            <div class="messagebox_buttons">
                <a class="close" href="#" :title="$t('close')">
                    <span>{{ $t('close') }}</span>
                </a>
            </div>
            {{ $t('not_started', {date: applLocaleDate}) }}
        </div>
        <div class="messagebox messagebox_warning" v-if="!notOverYet">
            <div class="messagebox_buttons">
                <a class="close" href="#" :title="$t('close')">
                    <span>{{ $t('close') }}</span>
                </a>
            </div>
            {{ $t('over_msg', {date: distLocaleDate}) }}
        </div>
        <div class="messagebox messagebox_info" v-if="applicationPeriodStarted && notOverYet">
            <div class="messagebox_buttons">
                <a class="close" href="#" :title="$t('close')">
                    <span>{{ $t('close') }}</span>
                </a>
            </div>
            {{ $t('box_header') }}
            <div class="messagebox_details">
                <ul>
                    <li>{{ $t('details_1ofX', {max: courses.length, others: other_ranking_groups.join(', ')}) }}</li>
                    <li>{{ $t('details_overlaps', {others: other_ranking_groups.join(', ')}) }}</li>
                    <li>{{ $t('details_min_prios', {amount: ranking_group.min_amount_prios}) }}</li>
                    <li>{{ $t('details_dist_time', {date: distLocaleDate}) }}</li>
                </ul>
            </div>
        </div>
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
        <div class="ranking" v-if="applicationPeriodStarted && notOverYet">
            <Schedule></Schedule>
            <AvailableList></AvailableList>
            <RankingList></RankingList>
        </div>

    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import axios from 'axios';
    import AvailableList from "./components/AvailableList";
    import RankingList from "./components/RankingList";
    import Schedule from "./components/Schedule";

    export default {
        name: 'app',
        components: {
            Schedule,
            AvailableList,
            RankingList
        },
        data: () => {
            return {
                error: '',
                success: false
            }
        },
        computed: {
            ...mapState(["courses", "application_time", "distribution_time", "ranking_group", "ranking", "other_ranking_groups"]),
            distLocaleDate: function () {
                let d = new Date(this.distribution_time * 1000);
                return d.toLocaleString();
            },
            notOverYet: function() {
                return new Date() < new Date(this.distribution_time * 1000)
            },
            applLocaleDate: function () {
              return new Date(this.application_time * 1000).toLocaleString();
            },
            applicationPeriodStarted: function () {
                return new Date() > new Date(this.application_time * 1000)
            }
        },
        methods: {
            submit() {
                let data = {
                    group_id: this.ranking_group.group_id,
                    ranking: this.ranking
                };

                axios.post('/plugins.php/bundleallocationplugin/enrollment/submit_preferences', data)
                    .then(() => {
                        this.success = true;
                        this.error = '';
                        // eslint-disable-next-line
                        $('.ui-dialog-content').scrollTo(0);
                        // eslint-disable-next-line
                        $('body').scrollTo(0);
                    })
                    .catch(err => {
                        console.log(err);
                        this.error = this.$t('error_saved');
                        this.success = false;
                        // eslint-disable-next-line
                        $('.ui-dialog-content').scrollTo(0);
                        // eslint-disable-next-line
                        $('body').scrollTo(0);
                    });
            }
        },
        mounted() {
            (async () => {
                // eslint-disable-next-line
                $('button.accept.bps-button').on('click', (event) => {
                    event.preventDefault();
                    if (this.notOverYet) {
                        if (this.ranking.length < this.ranking_group.min_amount_prios && this.ranking.length > 0) {
                            this.error = this.$t('error_min_prios', {amount: this.ranking_group.min_amount_prios});
                            this.success = false;
                            // eslint-disable-next-line
                            $('.ui-dialog-content').scrollTo(0);
                            // eslint-disable-next-line
                            $('body').scrollTo(0);
                        } else {
                            this.submit();
                        }
                    }
                });
            })();

        },
    }
</script>

<style>
    .ranking {
        display: flex;
    }

    @media (max-width: 768px) {
        .ranking {
            flex-wrap: wrap-reverse;
        }

        .ranking > #schedule {
            order: 1;
        }
    }

    @media (min-width: 768px) {
        .ranking {
            flex-wrap: wrap;
        }
    }
</style>
