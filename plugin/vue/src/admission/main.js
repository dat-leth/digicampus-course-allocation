import Vue from 'vue'
import App from './App.vue'
import store from './store'
import VueI18n from "vue-i18n";

Vue.config.productionTip = false
Vue.use(VueI18n)

const i18n = new VueI18n({
  // eslint-disable-next-line
  locale: BUNDLEALLOCATION_APPLICATIONS.lang,
  messages: {
    en: {}
  }
})

new Vue({
  store,
  i18n,
  render: h => h(App)
}).$mount('#app')
