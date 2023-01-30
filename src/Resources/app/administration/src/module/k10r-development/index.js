import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';
import './extension/sw-admin-menu';

Shopware.Module.register('k10r-development', {
    type: 'plugin',
    name: 'K10rDevelopment',
    title: 'k10r-development.general.mainMenuItemGeneral',
    description: 'k10r-development.general.descriptionTextModule',
    color: '#ff68b4',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    }
});
