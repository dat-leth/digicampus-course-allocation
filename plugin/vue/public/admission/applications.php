<script>
    var BUNDLEALLOCATION_APPLICATIONS = {
        lang: <?= json_encode(getUserLanguage($GLOBALS['user']->id)) ?>,
        coursesetId: <?= json_encode($setId) ?>,
        groups: <?= json_encode($rankings) ?>,
        prelim: <?= json_encode($prelim) ?>
    }
</script>
<div id="app"></div>
<hr>
<?= Studip\LinkButton::create(_("Anmeldeliste herunterladen (CSV)"), PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/applications/' . $setId . '/csv')) ?>
<?= Studip\LinkButton::create(_("Anmeldeliste herunterladen (JSON)"), URLHelper::getLink('api.php/bundleallocation/courseset/' . $setId . '/preferences')) ?>