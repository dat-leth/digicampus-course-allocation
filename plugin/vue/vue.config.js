// vue.config.js
module.exports = {
    publicPath: 'plugins_packages/uaux/BundleAllocationPlugin/views',
    outputDir: '../views',
    assetsDir: '../assets/vue',

    pages: {
        configurerule: {
            entry: 'src/configurerule/main.js',
            template: 'public/configurerule/configure.php',
            filename: 'configurerule/configure.php'
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