<script>
    var BUNDLEALLOCATION = {
        distribution_time: <?= $distribution_time ?>,
        ranking_group: <?= json_encode($ranking_group) ?>,
        courses: <?= json_encode($courses) ?>,
        ranking: <?= json_encode($ranking) ?>,
        existing_entries: <?= json_encode($existing_entries) ?>,
        other_rankings: <?= json_encode($other_rankings) ?>,
        other_ranking_groups: <?= json_encode($other_ranking_groups) ?>,
    }
</script>

<div id="app"></div>
<div data-dialog-button>
    <? if (time() < $distribution_time): ?>
    <button type="submit" class="accept button bps-button" data-dialog="size=big" name="Speichern">Speichern</button>
    <? endif; ?>
    <button type="submit" class="cancel button bps-button" name="cancel">Schließen</button>
</div>
