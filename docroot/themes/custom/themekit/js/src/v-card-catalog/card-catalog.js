import Vue from 'vue'
import App from './App.vue'

window.cardCatalog = new Vue({
  el: '#card-catalog',
  template: '<App/>',
  components: { App }
});
