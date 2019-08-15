// vue.config.js
module.exports = {
    publicPath: 'plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views',
    outputDir: '../views',
    assetsDir: '../assets/vue',

    pages: {
        apply: {
            entry: 'src/apply/main.ts',
            template: 'public/enrollment/apply.php',
            filename: 'enrollment/apply.php'
        }
    },
}
  