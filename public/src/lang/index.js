import Vue from 'vue';
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

import es_ES from './es-ES.js'
import val_ES from './val-ES.js'

export default new VueI18n({
  locale: 'val',
  fallback: 'es',
  messages: {
	  "es": es_ES,
	  "val": val_ES
	}
})