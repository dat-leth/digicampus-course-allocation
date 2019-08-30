// vue.config.js
module.exports = {
    publicPath: 'plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views',
    outputDir: '../views',
    assetsDir: '../assets/vue',

    pages: {
        configurerule: {
            entry: 'src/configurerule/main.js',
            template: 'public/configurerule/configure.php',
            filename: 'configurerule/configure.php'
        }
    },
}
  