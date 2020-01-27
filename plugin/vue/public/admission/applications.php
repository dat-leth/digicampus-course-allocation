<script>
    const BUNDLEALLOCATION_APPLICATIONS = {
        coursesetId: <?= json_encode($setId) ?>,
        groups: <?= json_encode($rankings) ?>,
        prelim: <?= json_encode($prelim) ?>
    }
</script>
<div id="app"></div>
<?= Studip\LinkButton::create("Anmeldeliste herunterladen", PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/applications/' . $setId . '/csv')) ?>
