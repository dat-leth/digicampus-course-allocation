<h3><?= $rule->getName() ?></h3>
<noscript>
    <strong>We're sorry but this doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
</noscript>
<script>
    const BUNDLEALLOCATION = {
        rule_id: <?= json_encode($rule->getId()) ?>,
        courseset_id: <?= json_encode($courseset_id) ?>,
        courses: <?= json_encode($courses) ?>,
    }
</script>
<script>
    alert($('.hidden-alert').is(':visible'))
</script>
<div id="bundle_allocation_configurator"></div>
<!-- TODO: built files will be auto injected -->