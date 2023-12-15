window.vue = require('vue');
require('lodash');

import Vue from 'vue';
import axios from 'axios';

Vue.prototype.$axios = axios;
Vue.prototype.trans = (key) => {
    return _.get(window.lang, key, key);
};

import PanicNotification from './pages/PanicNotification.vue';

new Vue({
    el: '.notification_panic_lib',
    components: {
        buttonPanicNotification: PanicNotification
    },
});
