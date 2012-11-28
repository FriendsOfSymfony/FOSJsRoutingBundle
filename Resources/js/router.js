goog.provide('fos.Router');

goog.require('goog.structs.Map');
goog.require('goog.array');
goog.require('goog.object');
goog.require('goog.uri.utils');

/**
 * @constructor
 * @param {fos.Router.Context=} opt_context
 * @param {Object.<string, fos.Router.Route>=} opt_routes
 */
fos.Router = function(opt_context, opt_routes) {
    this.context_ = opt_context || {base_url: '', prefix: '', host: '', scheme: ''};
    this.setRoutes(opt_routes || {});
};
goog.addSingletonGetter(fos.Router);

/**
 * @typedef {{
 *     tokens: (Array.<Array.<string>>),
 *     defaults: (Object.<string, string>),
 *     requirements: Object
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
 * @return {string}
 */
fos.Router.prototype.getBaseUrl = function() {
    return this.context_.base_url;
};

/**
 * @param {string} prefix
 */
fos.Router.prototype.setPrefix = function(prefix) {
  this.context_.prefix = prefix;
};

/**
 * @param {string} scheme
 */
fos.Router.prototype.setScheme = function(scheme) {
    this.context_.scheme = scheme;
};

/**
 * @return {string}
 */
fos.Router.prototype.getScheme = function() {
    return this.context_.scheme;
};

/**
 * @param {string} host
 */
fos.Router.prototype.setHost = function(host) {
    this.context_.host = host;
};

/**
 * @return {string}
 */
fos.Router.prototype.getHost = function() {
    return this.context_.host;
};


/**
 * Returns a raw route object
 * 
 * @param {string} name
 * @return {fos.Router.Route}
 */
fos.Router.prototype.getRoute = function(name) {
    var prefixedName = this.context_.prefix + name;
    if (!this.routes_.containsKey(prefixedName)) {
        // Check first for default route before failing
        if (!this.routes_.containsKey(name)) {
          throw new Error('The route "' + name + '" does not exist.');
        }
    } else {
      name = prefixedName;
    }

    return (this.routes_.get(name));
};


/**
 * Generates the URL for a route.
 *
 * @param {string} name
 * @param {Object.<string, string>} opt_params
 * @param {boolean} absolute
 * @return {string}
 */
fos.Router.prototype.generate = function(name, opt_params, absolute) {
    var route = (this.getRoute(name));
    var params = opt_params || {};
    var unusedParams = goog.object.clone(params);
    var url = '';
    var optional = true;
    goog.array.forEach(route.tokens, function(token) {
        if ('text' === token[0]) {
            url = token[1] + url;
            optional = false;

            return;
        }

        if ('variable' === token[0]) {
            if (false === optional || !goog.object.containsKey(route.defaults, token[3])
                    || (goog.object.containsKey(params, token[3]) && params[token[3]] != route.defaults[token[3]])) {
                var value;
                if (goog.object.containsKey(params, token[3])) {
                    value = params[token[3]];
                    goog.object.remove(unusedParams, token[3]);
                } else if (goog.object.containsKey(route.defaults, token[3])) {
                    value = route.defaults[token[3]];
                } else if (optional) {
                    return;
                } else {
                    throw new Error('The route "' + name + '" requires the parameter "' + token[3] + '".');
                }

                var empty = true === value || false === value || '' === value;

                if (!empty || !optional) {
                    url = token[1] + encodeURIComponent(value).replace(/%2F/g, '/') + url;
                }

                optional = false;
            }

            return;
        }

        throw new Error('The token type "' + token[0] + '" is not supported.');
    });

    if (url === '') {
        url = '/';
    }

    url = this.context_.base_url + url;
    if(goog.object.containsKey(route.requirements, "_scheme")){
    	if(this.getScheme() != route.requirements["_scheme"]){
    		url = route.requirements["_scheme"] + "://" + this.getHost() + url;
    	}
    }else if(absolute === true){
		url = this.getScheme() + "://" + this.getHost() + url;
	}
    
    if (goog.object.getCount(unusedParams) > 0) {
        url = goog.uri.utils.appendParamsFromMap(url, unusedParams);
    }
    
    return url;
};

