window.vue = require('vue');

import Vue from 'vue';

import Loading from 'vue-loading-overlay';
import VueSweetalert2 from 'vue-sweetalert2';
import pagination from 'laravel-vue-pagination';
import panicsettings from './pages/panic_settings.vue';

Vue.use(VueSweetalert2);

Vue.component('loading', Loading);
Vue.component('pagination', pagination);

//Main vue instance
new Vue({
    el: '#VueJs',

    data: {
    },

    components: {
        panicsettings: panicsettings
    },

    created: function () {
    }
})