import '@fortawesome/fontawesome-free/css/all.css';
import { createVuetify } from 'vuetify';
import { aliases, fa } from 'vuetify/iconsets/fa';
import { VDataTable } from 'vuetify/labs/VDataTable';
import { VDialog } from 'vuetify/components/VDialog';
import { VDialogTransition } from 'vuetify/components';
import colors from 'vuetify/lib/util/colors';

export const Vuetify = createVuetify({
    components: {
        VDataTable,
        VDialog,
        VDialogTransition
    },
    icons: {
        defaultSet: 'fa',
        aliases,
        sets: {
            fa
        }
    },
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                    'primary-bg-color': colors.purple.darken3,
                    'secondary-bg-color': colors.grey.lighten2,

                    'add-bg-button-color': colors.blue.darken3,
                    'edit-bg-button-color': colors.orange.darken3,
                    'delete-bg-button-color': colors.red.darken3,
                    'cancel-bg-button-color': colors.grey.darken2,

                    'teal': colors.teal.darken1,
                    'blue': colors.blue.darken1,
                    'red': colors.red.darken1,
                    'orange': colors.orange.darken1,
                    'brown': colors.brown.darken1
                }
            }
        }
    } 
});