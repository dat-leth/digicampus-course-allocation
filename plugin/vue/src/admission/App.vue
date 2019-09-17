<template>
    <div id="app">
        <div class="messagebox messagebox_error" v-if="error.length !== 0">
            <div class="messagebox_buttons">
                <a class="close" href="#" title="Nachrichtenbox schliessen"
                   v-on:click.prevent="error = ''">
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
        <div class="messagebox messagebox_info" v-if="groups.length === 0">
            Keine Anmeldungen vorhanden.
        </div>
        <GroupTable v-for="group in groups" :key="group.group_id" :group="group"></GroupTable>
        <button class="button" @click="submit" v-if="groups.length > 0">Speichern</button>
        <button class="button" @click.prevent="submitAndEnroll" v-if="groups.length > 0">Speichern und eintragen</button>
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
                STUDIP.Dialog.confirm('Die Teilnehmer werden hiermit in die entsprechenden Veranstaltungen eingetragen. Soll die Eintragung durchgeführt werden?', () => {
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
                    .catch((err) => {
                        console.log(err)
                        this.error = 'Zugeteilte Veranstaltungen konnten nicht gespeichert werden.';
                        this.success = false;
                    })
            }
        }
    }
</script>

<style>
</style>
