var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

 /*var paths = {
 	'jquery': './vendor/bower_components/jquery/',
    'bootstrap': './vendor/bower_components/bootstrap-sass-official/assets/',
    'fontawesome': './vendor/bower_components/fontawesome/css/',
    'bourbon': './vendor/bower_components/bourbon/app/assets/stylesheets/'
}

elixir(function(mix) {
    mix.sass('app.scss')
		.copy(paths.bootstrap + 'stylesheets/', 'resources/assets/sass') //bootstrap sass
		.copy(paths.bourbon, 'resources/assets/sass') //bourbon sass
		.copy(paths.bootstrap + 'fonts/bootstrap/', 'public/fonts') //bootstrap fonts
		.copy(paths.fontawesome + 'font-awesome.min.css', 'public/css/vendor/font-awesome.css') //fontawesome css
		.copy(paths.bootstrap + 'javascripts/bootstrap.js', 'public/js/vendor/bootstrap.js') //bootstrap js
		.copy(paths.jquery + 'dist/jquery.min.js', 'public/js/vendor/jquery.js'); //jquery
});*/

var paths = {
'bower_base_path': './vendor/bower_components/',
'bootstrap': './vendor/bower_components/bootstrap-sass-official/assets/'
};

elixir(function (mix) {
mix.sass('app.scss')
.copy(paths.bootstrap + 'stylesheets/', 'resources/assets/sass')
.copy(paths.bootstrap + 'fonts/bootstrap', 'public/fonts/bootstrap')
.copy(paths.bootstrap + 'javascripts/bootstrap.js', 'public/js/vendor/bootstrap.js')
.copy(paths.bower_base_path + 'jquery/dist/jquery.min.js', 'public/js/vendor/jquery.js')
.copy(paths.bower_base_path + 'fontawesome/css/font-awesome.min.css', 'public/css/vendor/font-awesome.css')
.copy(paths.bower_base_path + 'fontawesome/fonts/', 'public/css/fonts') //<-- this is not ideal, I would prefer that FontAwesome be in public/fonts...
.copy(paths.bower_base_path + 'moment/min/moment.min.js', 'public/js/vendor/moment.min.js')
.copy(paths.bower_base_path + 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', 'public/js/vendor/bootstrap-datetimepicker.min.js')
.copy(paths.bower_base_path + 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'public/css/vendor/bootstrap-datetimepicker.min.css');
});
