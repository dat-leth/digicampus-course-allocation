<head><link href=/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/../assets/vue/css/admission.c832c45a.css rel=preload as=style><link href=/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/../assets/vue/js/admission.3b86ccb5.js rel=preload as=script><link href=/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/../assets/vue/js/chunk-vendors.4644698b.js rel=preload as=script><link href=/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/../assets/vue/css/admission.c832c45a.css rel=stylesheet></head><script>const BUNDLEALLOCATION_APPLICATIONS = {
        coursesetId: <?= json_encode($setId) ?>,
        groups: <?= json_encode($rankings) ?>,
        prelim: <?= json_encode($prelim) ?> }</script><div id=app></div> <?= Studip\LinkButton::create("Anmeldeliste herunterladen", PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/applications/' . $setId . '/csv')) ?> <script src=/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/../assets/vue/js/chunk-vendors.4644698b.js></script><script src=/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/../assets/vue/js/admission.3b86ccb5.js></script>