<template>
    <div id="datetime">
        <h3>Schritt 1: Verteilzeitpunkt festlegen</h3>
        <label class="col-1">
            <span class="required">Datum</span>
            <input type="text" name="distributiondate" id="distributiondate"
                   class="size-s no-hint" placeholder="tt.mm.jjjj"
                   :value="rule.dist_date" required/>
        </label>
        <label class="col-1">
            <span class="required">Uhrzeit</span>
            <input type="text" name="distributiontime" id="distributiontime"
                   class="size-s no-hint" placeholder="ss:mm"
                   :value="rule.dist_time" required/>
        </label>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "DistDateTime",
        computed: {
            ...mapState(["rule"])
        },
        mounted() {
            // eslint-disable-next-line
            jQuery('#distributiondate').datepicker();
            // eslint-disable-next-line
            jQuery('#distributiontime').timepicker();
        }
    }
</script>

<style scoped>

</style>