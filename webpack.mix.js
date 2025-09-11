const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.combine([
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/bootstrap-icons/font/bootstrap-icons.css',
    'node_modules/sweetalert2/dist/sweetalert2.css',
    'node_modules/select2/dist/css/select2.css',
    'node_modules/toastr/build/toastr.min.css',
    'node_modules/@chenfengyuan/datepicker/dist/datepicker.css',
    'node_modules/daterangepicker/daterangepicker.css',
    'node_modules/summernote/dist/summernote.min.css',
    'resources/assets/plugins/formValidation/css/formValidation.css',
    'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
], 'public/css/app.css').version();

mix.combine([
    'node_modules/jquery/dist/jquery.js',
    'node_modules/@popperjs/core/dist/umd/popper.js',
    'node_modules/moment/min/moment.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.js',
    'node_modules/@chenfengyuan/datepicker/dist/datepicker.js',
    'node_modules/select2/dist/js/select2.js',
    'node_modules/toastr/build/toastr.min.js',
    'node_modules/sweetalert2/dist/sweetalert2.js',
    'node_modules/daterangepicker/daterangepicker.js',
    'node_modules/summernote/dist/summernote.min.js',
    'node_modules/datatables.net/js/jquery.dataTables.min.js',
    'node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
    'resources/assets/plugins/formValidation/js/es6-shim.min.js',
    'resources/assets/plugins/formValidation/js/FormValidation.js',
    'resources/assets/plugins/formValidation/js/plugins/Bootstrap5.js',
    'resources/assets/plugins/formValidation/js/plugins/StartEndDate.js',
], 'public/js/app.js').version();

mix.copyDirectory('node_modules/bootstrap-icons/font/fonts', 'public/css/fonts');
mix.copyDirectory('node_modules/summernote/dist/font', 'public/css/font');
