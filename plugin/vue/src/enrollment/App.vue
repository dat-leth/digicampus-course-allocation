<template>
    <div id="app">
        <div class="messagebox messagebox_warning" v-if="!notOverYet">
            <div class="messagebox_buttons">
                <a class="close" href="#" title="Nachrichtenbox schliessen">
                    <span>Nachrichtenbox schliessen</span>
                </a>
            </div>
            Die Abgabe von Prioritäten ist nicht mehr möglich. Die automatische Verteilung der Plätze geschah am {{ localeDate }}. Kontaktieren Sie die verantwortliche Lehrperson.
        </div>
        <div class="messagebox messagebox_info" v-if="notOverYet">
            <div class="messagebox_buttons">
                <a class="close" href="#" title="Nachrichtenbox schliessen">
                    <span>Nachrichtenbox schliessen</span>
                </a>
            </div>
            Die Plätze dieser Veranstaltung werden automatisch verteilt.
            <div class="messagebox_details">
                <ul>
                    <li>Sie können 1 von {{ courses.length }} Veranstaltungen belegen. Bei der Verteilung werden die von
                        Ihnen gewünschten Prioritäten sowie Überschneidungen zwischen Veranstaltungen von <em>{{ other_ranking_groups.join(', ') }}</em>
                        berücksichtigt.
                    </li>
                    <li>Überschneidungen zu Veranstaltungen außerhalb dieses Anmeldeverfahrens werden
                        <strong>nicht</strong> berücksichtigt.
                    </li>
                    <li>Es müssen mindestens {{ ranking_group.min_amount_prios }} Prioritäten abgegeben werden.</li>
                    <li>Zeitpunkt der Verteilung: {{ localeDate }}</li>
                </ul>
            </div>
        </div>
        <div class="messagebox messagebox_error" v-if="error.length !== 0">
            <div class="messagebox_buttons">
                <a class="close" href="#" title="Nachrichtenbox schliessen"
                   v-on:click.prevent="error = false">
                    <span>Nachrichtenbox schliessen</span>
                </a>
            </div>
            {{ error }}
        </div>
        <div class="messagebox messagebox_success" v-if="success === true">
            <div class="messagebox_buttons">
                <a class="close" href="#" title="Nachrichtenbox schliessen"
                   v-on:click.prevent="success = false">
                    <span>Nachrichtenbox schliessen</span>
                </a>
            </div>
            Prioritäten wurden gespeichert
        </div>
        <div class="ranking" v-if="notOverYet">
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
            ...mapState(["courses", "distribution_time", "ranking_group", "ranking", "other_ranking_groups"]),
            localeDate: function () {
                let d = new Date(this.distribution_time * 1000);
                return d.toLocaleString();
            },
            notOverYet: function() {
                return new Date() < new Date(this.distribution_time * 1000)
            },
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
                        this.error = 'Prioritäten konnten nicht gespeichert werden.';
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
                            this.error = `Es müssen mindestens ${this.ranking_group.min_amount_prios} Prioritäten abgegeben werden.`;
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
