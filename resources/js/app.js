import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { Vuetify } from './Plugins/vuetify';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const options = {
    confirmButtonColor: '#6b21a8'
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(VueSweetalert2, options)
            .use(Vuetify);
            
        window.Swal = app.config.globalProperties.$swal;

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
