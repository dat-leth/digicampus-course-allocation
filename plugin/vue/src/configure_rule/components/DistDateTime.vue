<i18n>
{
	"de_DE": {
		"step1_header": "Schritt 1: Anmeldezeitraum festlegen",
		"date": "Datum",
		"time": "Uhrzeit",
		"begin": "Anfang des Anmeldezeitraums",
		"end": "Ende des Anmeldezeitraums"
	},
	"en_GB": {
		"step1_header": "Step 1: Define registration period",
		"date": "Date",
		"time": "Time",
		"begin": "Start of registration period",
		"end": "End of registration period"
	}
}
</i18n>

<template>
    <div id="datetime">
        <h3>{{ $t('step1_header') }}</h3>
        <p>{{ $t('begin') }}</p>
        <label class="col-1">
            <span class="required">{{ $t('date') }}</span>
            <input type="text" name="applicationdate" id="applicationdate"
                   class="size-s no-hint" placeholder="tt.mm.jjjj"
                   :value="rule.appl_date" required/>
        </label>
        <label class="col-1">
            <span class="required">{{ $t('time') }}</span>
            <input type="text" name="applicationtime" id="applicationtime"
                   class="size-s no-hint" placeholder="ss:mm"
                   :value="rule.appl_time" required/>
        </label>
        <p>{{ $t('end') }}</p>
        <label class="col-1">
            <span class="required">{{ $t('date') }}</span>
            <input type="text" name="distributiondate" id="distributiondate"
                   class="size-s no-hint" placeholder="tt.mm.jjjj"
                   :value="rule.dist_date" required/>
        </label>
        <label class="col-1">
            <span class="required">{{ $t('time') }}</span>
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
            jQuery('#applicationdate').datepicker();
            // eslint-disable-next-line
            jQuery('#applicationtime').timepicker();
            // eslint-disable-next-line
            jQuery('#distributiondate').datepicker();
            // eslint-disable-next-line
            jQuery('#distributiontime').timepicker();
        }
    }
</script>

<style scoped>

</style>