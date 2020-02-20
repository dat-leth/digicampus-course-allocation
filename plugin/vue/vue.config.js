// vue.config.js
module.exports = {
    publicPath: 'plugins_packages/uaux/BundleAllocationPlugin/views',
    outputDir: '../views',
    assetsDir: '../assets/vue',

    pages: {
        configure_rule: {
            entry: 'src/configure_rule/main.js',
            template: 'public/configure_rule/configure.php',
            filename: 'configure_rule/configure.php',
        },
        enrollment: {
            entry: 'src/enrollment/main.js',
            template: 'public/enrollment/apply.php',
            filename: 'enrollment/apply.php'
        },
        admission: {
            entry: 'src/admission/main.js',
            template: 'public/admission/applications.php',
            filename: 'admission/applications.php'
        }
    },
};