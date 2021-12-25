window.vue = require('vue');

require('lodash');

import Vue from 'vue';

import Loading from 'vue-loading-overlay';
import VueSweetalert2 from 'vue-sweetalert2';

import panicsettings from './pages/panic_settings.vue';
import panicreport from './pages/panic_report.vue';

import pagination from 'laravel-vue-pagination';
Vue.component('pagination', pagination);

Vue.use(VueSweetalert2);

Vue.component('loading', Loading);
Vue.component('pagination', pagination);

//Allows localization using trans()
Vue.prototype.trans = (key) => {
    return _.get(window.lang, key, key);
};

//Tells if an JSON parsed object is empty
Vue.prototype.isEmpty = (obj) => {
    return _.isEmpty(obj);
};

import Toasted from 'vue-toasted';
Vue.use(Toasted);

//Main vue instance
new Vue({
    el: '#VueJs',

    data: {
    },
    components: {
        panicsettings: panicsettings,
		panicreport: panicreport
    },
    created: function () {
    }
})