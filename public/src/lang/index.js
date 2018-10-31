import Vue from 'vue';
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

import es_ES from './es-ES.js'
import val_ES from './val-ES.js'

var locale = location.pathname.split('/')[1].toLowerCase();

export default new VueI18n({
  locale: locale == 'val' || locale =='es' ? locale : 'val',
  fallback: 'val',
  messages: {
	  "es": es_ES,
	  "val": val_ES
	}
})