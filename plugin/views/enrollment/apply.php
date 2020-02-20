<head><link href=/plugins_packages/uaux/BundleAllocationPlugin/views/../assets/vue/css/enrollment.aafc5af3.css rel=preload as=style><link href=/plugins_packages/uaux/BundleAllocationPlugin/views/../assets/vue/js/chunk-vendors.35261295.js rel=preload as=script><link href=/plugins_packages/uaux/BundleAllocationPlugin/views/../assets/vue/js/enrollment.98795edd.js rel=preload as=script><link href=/plugins_packages/uaux/BundleAllocationPlugin/views/../assets/vue/css/enrollment.aafc5af3.css rel=stylesheet></head><script>var BUNDLEALLOCATION = {
        distribution_time: <?= $distribution_time ?>,
        ranking_group: <?= json_encode($ranking_group) ?>,
        courses: <?= json_encode($courses) ?>,
        ranking: <?= json_encode($ranking) ?>,
        existing_entries: <?= json_encode($existing_entries) ?>,
        other_rankings: <?= json_encode($other_rankings) ?>,
        other_ranking_groups: <?= json_encode($other_ranking_groups) ?>,
    }</script><div id=app></div><div data-dialog-button> <? if (time() < $distribution_time): ?> <button type=submit class="accept button bps-button" data-dialog="size=big" name=Speichern>Speichern</button> <? endif; ?> <button type=submit class="cancel button bps-button" name=cancel>Schließen</button></div><script src=/plugins_packages/uaux/BundleAllocationPlugin/views/../assets/vue/js/chunk-vendors.35261295.js></script><script src=/plugins_packages/uaux/BundleAllocationPlugin/views/../assets/vue/js/enrollment.98795edd.js></script>