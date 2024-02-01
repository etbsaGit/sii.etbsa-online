const mix = require("laravel-mix");

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

mix
  .vue()
  .js("resources/js/admin.js", "public/js")
  .sass("resources/sass/admin.scss", "public/css")
  .sass("resources/sass/front.scss", "public/css")
  .copyDirectory("resources/img", "public/img")
  .webpackConfig(require("./webpack.config"))
  // .vue({ version: 2 })
  .extract([
    "vue",
    "vue-router",
    "moment",
    "axios",
    "lodash",
    "dropzone",
    "vuetify",
    "portal-vue",
  ]);

if (mix.inProduction()) {
  mix.version();
}
