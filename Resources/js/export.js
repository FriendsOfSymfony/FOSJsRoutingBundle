/**
 * @fileoverview This file is the entry point for the compiler.
 *
 * You can compile this script by running (assuming you have JMSGoogleClosureBundle installed):
 *
 *    php app/console plovr:build @FOSJsRoutingBundle/compile.js
 */

goog.require('fos.Router');

goog.exportSymbol('fos.Router', fos.Router);
goog.exportSymbol('fos.Router.setData', function(data) {
    var router = fos.Router.getInstance();
    router.setBaseUrl(/** @type {string} */ (data['base_url']));
    router.setRoutes(/** @type {Object.<string, fos.Router.Route>} */ (data['routes']));
    if ('prefix' in data) {
        router.setPrefix(/** @type {string} */ (data['prefix']));
    }
    router.setHost(/** @type {string} */ (data['host']));
    router.setScheme(/** @type {string} */ (data['scheme']));
});
goog.exportProperty(fos.Router, 'getInstance', fos.Router.getInstance);
goog.exportProperty(fos.Router.prototype, 'setRoutes', fos.Router.prototype.setRoutes);
goog.exportProperty(fos.Router.prototype, 'getRoutes', fos.Router.prototype.getRoutes);
goog.exportProperty(fos.Router.prototype, 'setBaseUrl', fos.Router.prototype.setBaseUrl);
goog.exportProperty(fos.Router.prototype, 'getBaseUrl', fos.Router.prototype.getBaseUrl);
goog.exportProperty(fos.Router.prototype, 'generate', fos.Router.prototype.generate);
goog.exportProperty(fos.Router.prototype, 'setPrefix', fos.Router.prototype.setPrefix);
goog.exportProperty(fos.Router.prototype, 'getRoute', fos.Router.prototype.getRoute);

window['Routing'] = fos.Router.getInstance();
