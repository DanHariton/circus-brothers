let mix = require('laravel-mix');

mix
    .disableNotifications()
    .options({processCssUrls: false})
    .setPublicPath('public')
    .js('assets/admin/js/app.js', 'public/admin/admin.js')
    .js('assets/site/js/app.js', 'public/site/site.js')
    .js('assets/site/js/components/showConcerts.js', 'public/site/components/showConcerts.js')
    .sass('assets/admin/css/app.scss', 'public/admin/admin.css')
    .sass('assets/site/css/app.scss', 'public/site/site.css')
    .version()
;