window.vue = require('vue');

import Vue from 'vue';

import Loading from 'vue-loading-overlay';
import VueSweetalert2 from 'vue-sweetalert2';
import pagination from 'laravel-vue-pagination';
import PanicSettings from '../pages/panic_settings.vue';

Vue.use(VueSweetalert2);

Vue.component('loading', Loading);
Vue.component('pagination', pagination);

//Allows localization using trans()
Vue.prototype.trans = (key) => {
    return _.get(window.lang, key, key);
};

//Main vue instance
new Vue({
    el: '#VueJs',

    data: {
    },

    components: {
        panicsettings: PanicSettings
    },

    created: function () {
    }
})