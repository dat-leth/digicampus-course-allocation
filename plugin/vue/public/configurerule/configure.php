<h3><?= $rule->getName() ?></h3>
<h4>Verteilzeitpunkt</h4>
<label class="col-1">
    <?= _('Datum') ?>
    <input type="text" name="distributiondate" id="distributiondate"
           class="size-s no-hint" placeholder="tt.mm.jjjj"
           value="<?= $rule->getDistributionTime() ? date('d.m.Y', $rule->getDistributionTime()) : '' ?>" required/>
</label>

<label class="col-1">
    <?= _('Uhrzeit') ?>
    <input type="text" name="distributiontime" id="distributiontime"
           class="size-s no-hint" placeholder="ss:mm"
           value="<?= $rule->getDistributionTime() ? date('H:i', $rule->getDistributionTime()) : '23:59' ?>"/>
</label>
<noscript>
    <strong>We're sorry but this doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
</noscript>
<script>
    $('#distributiondate').datepicker();
    $('#distributiontime').timepicker();
</script>
<script>
    const BUNDLEALLOCATION = {
        rule_id: <?= json_encode($rule->getId()) ?>,
        courseset_id: <?= json_encode($courseset_id) ?>
    }
</script>
<h3>Veranstaltungen konfigurieren</h3>
<div id="app"></div>
