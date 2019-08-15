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
<div id="bundle_allocation_configurator" style="width: 900px"></div>
<img src="http://via.placeholder.com/900x1020" alt="asdf">
<!-- TODO: built files will be auto injected -->