// import './main.js';
import '../css/app.css'

import {createApp, h} from 'vue'
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Link, createInertiaApp, Head } from "@inertiajs/vue3";
import vClickOutside from 'v-click-outside'


createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: true})
        return pages[`./Pages/${name}.vue`]
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(vClickOutside)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el)
    },
    progress: {
        delay: 250,
        color: '#16a34a',
        includeCSS: true,
        showSpinner: true,
    }
})

