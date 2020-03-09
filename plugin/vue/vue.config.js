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

    pluginOptions: {
      i18n: {
        locale: 'de_DE',
        fallbackLocale: 'de_DE',
        localeDir: 'locales',
        enableInSFC: true
      }
    },

    chainWebpack: config => {
        config.module
            .rule("i18n")
            .resourceQuery(/blockType=i18n/)
            .type('javascript/auto')
            .use("i18n")
            .loader("@kazupon/vue-i18n-loader")
            .end();
    }
};
