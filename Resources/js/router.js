goog.provide('fos.Router');

goog.require('goog.structs.Map');
goog.require('goog.array');
goog.require('goog.object');

/** 
 * @constructor
 * @param {fos.Router.Context=} opt_context
 * @param {Object.<string, fos.Router.Route>=} opt_routes
 */
fos.Router = function(opt_context, opt_routes) {
	this.context_ = opt_context || {base_url: ''};
	this.setRoutes(opt_routes || {});
};
goog.addSingletonGetter(fos.Router);

/**
 * @typedef {{
 *     tokens: (Array.<Array.<string>>),
 *     defaults: (Object.<string, string>)
 * }}
 */
fos.Router.Route;

/**
 * @typedef {{
 *     base_url: (string)
 * }}
 */
fos.Router.Context;

/**
 * @param {Object.<string, fos.Router.Route>} routes
 */
fos.Router.prototype.setRoutes = function(routes) {
	this.routes_ = new goog.structs.Map(routes);
};

/**
 * @param {string} baseUrl
 */
fos.Router.prototype.setBaseUrl = function(baseUrl) {
	this.context_.base_url = baseUrl;
};

/**
 * Generates the URL for a route.
 * 
 * @param {string} name
 * @param {Object.<string, string>=} opt_params
 * @return {string}
 */
fos.Router.prototype.generate = function(name, opt_params) {
	if (!this.routes_.containsKey(name)) {
		throw new Error('The route "' + name + '" does not exist.');
	}
	
	var route = /** @type {fos.Router.Route} */ (this.routes_.get(name));
	var params = opt_params || {};
	var url = this.context_.base_url;
	
	goog.array.forEachRight(route.tokens, function(token) {
		if ('text' === token[0]) {
			url += token[1];
			
			return;
		} 
		
		if ('variable' === token[0]) {
			url += token[1];
			
			if (goog.object.containsKey(params, token[3])) {
				url += params[token[3]];
			} else if (goog.object.containsKey(route.defaults, token[3])) {
				url += route.defaults[token[3]];
			} else {
				throw new Error('The route "' + name + '" requires the parameter "' + token[3] + '".');
			}
			
			return;
		}
		
		throw new Error('The token type "' + token[0] + '" is not supported.');
	});
	
	return url;
};

