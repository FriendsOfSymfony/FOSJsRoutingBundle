'use strict';

/**
 * @fileoverview This file defines the Router class.
 *
 * You can compile this file by running the following command from the Resources folder:
 *
 *    npm install && npm run build
 */

/**
 * Class Router
 */
class Router {

    /**
     * @constructor
     * @param {Router.Context=} context
     * @param {Object.<string, Router.Route>=} routes
     */
    constructor(context, routes) {
        this.context_ = context || {base_url: '', prefix: '', host: '', port: '', scheme: ''};
        this.setRoutes(routes || {});
    }

    /**
     * Returns the current instance.
     * @returns {Router}
     */
    static getInstance() {
        return Routing;
    }

    /**
     * Configures the current Router instance with the provided data.
     * @param {Object} data
     */
    static setData(data) {
        let router = Router.getInstance();

        router.setRoutingData(data);
    }

    /**
     * Sets data for the current instance
     * @param {Object} data
     */
    setRoutingData(data) {
        this.setBaseUrl(data['base_url']);
        this.setRoutes(data['routes']);

        if ('prefix' in data) {
            this.setPrefix(data['prefix']);
        }
        if ('port' in data) {
          this.setPort(data['port']);
        }

        this.setHost(data['host']);
        this.setScheme(data['scheme']);
    }

    /**
     * @param {Object.<string, Router.Route>} routes
     */
    setRoutes(routes) {
        this.routes_ = Object.freeze(routes);
    }

    /**
     * @return {Object.<string, Router.Route>} routes
     */
    getRoutes() {
        return this.routes_;
    }

    /**
     * @param {string} baseUrl
     */
    setBaseUrl(baseUrl) {
        this.context_.base_url = baseUrl;
    }

    /**
     * @return {string}
     */
    getBaseUrl() {
        return this.context_.base_url;
    }

    /**
     * @param {string} prefix
     */
    setPrefix(prefix) {
        this.context_.prefix = prefix;
    }

    /**
     * @param {string} scheme
     */
    setScheme(scheme) {
        this.context_.scheme = scheme;
    }

    /**
     * @return {string}
     */
    getScheme() {
        return this.context_.scheme;
    }

    /**
     * @param {string} host
     */
    setHost(host) {
        this.context_.host = host;
    }

    /**
     * @return {string}
     */
    getHost() {
        return this.context_.host;
    }

    /**
     * @param {string} port
    */
    setPort(port) {
      this.context_.port = port;
    }

    /**
     * @return {string}
     */
    getPort() {
      return this.context_.port;
    };

    /**
     * Builds query string params added to a URL.
     * Port of jQuery's $.param() function, so credit is due there.
     *
     * @param {string} prefix
     * @param {Array|Object|string} params
     * @param {Function} add
     */
    buildQueryParams(prefix, params, add) {
        let name;
        let rbracket = new RegExp(/\[\]$/);

        if (params instanceof Array) {
            params.forEach((val, i) => {
                if (rbracket.test(prefix)) {
                    add(prefix, val);
                } else {
                    this.buildQueryParams(prefix + '[' + (typeof val === 'object' ? i : '') + ']', val, add);
                }
            });
        } else if (typeof params === 'object') {
            for (name in params) {
                this.buildQueryParams(prefix + '[' + name + ']', params[name], add);
            }
        } else {
            add(prefix, params);
        }
    }

    /**
     * Returns a raw route object.
     *
     * @param {string} name
     * @return {Router.Route}
     */
    getRoute(name) {
        let prefixedName = this.context_.prefix + name;

        if (!(prefixedName in this.routes_)) {
            // Check first for default route before failing
            if (!(name in this.routes_)) {
                throw new Error('The route "' + name + '" does not exist.');
            }
        } else {
            name = prefixedName;
        }

        return this.routes_[name];
    }

    /**
     * Generates the URL for a route.
     *
     * @param {string} name
     * @param {Object.<string, string>} opt_params
     * @param {boolean} absolute
     * @return {string}
     */
    generate(name, opt_params, absolute = false) {
        let route = (this.getRoute(name)),
            params = opt_params || {},
            unusedParams = Object.assign({}, params),
            url = '',
            optional = true,
            host = '',
            port = (typeof this.getPort() == "undefined" || this.getPort() === null) ? '' : this.getPort();

        route.tokens.forEach((token) => {
            if ('text' === token[0]) {
                url = token[1] + url;
                optional = false;

                return;
            }

            if ('variable' === token[0]) {
                let hasDefault = route.defaults && (token[3] in route.defaults);
                if (false === optional || !hasDefault || ((token[3] in params) && params[token[3]] != route.defaults[token[3]])) {
                    let value;

                    if (token[3] in params) {
                        value = params[token[3]];
                        delete unusedParams[token[3]];
                    } else if (hasDefault) {
                        value = route.defaults[token[3]];
                    } else if (optional) {
                        return;
                    } else {
                        throw new Error('The route "' + name + '" requires the parameter "' + token[3] + '".');
                    }

                    let empty = true === value || false === value || '' === value;

                    if (!empty || !optional) {
                        let encodedValue = encodeURIComponent(value).replace(/%2F/g, '/');

                        if ('null' === encodedValue && null === value) {
                            encodedValue = '';
                        }

                        url = token[1] + encodedValue + url;
                    }

                    optional = false;
                } else if (hasDefault && (token[3] in unusedParams)) {
                    delete unusedParams[token[3]];
                }

                return;
            }

            throw new Error('The token type "' + token[0] + '" is not supported.');
        });

        if (url === '') {
            url = '/';
        }

        route.hosttokens.forEach((token) => {
            let value;

            if ('text' === token[0]) {
                host = token[1] + host;

                return;
            }

            if ('variable' === token[0]) {
                if (token[3] in params) {
                    value = params[token[3]];
                    delete unusedParams[token[3]];
                } else if (route.defaults && (token[3] in route.defaults)) {
                    value = route.defaults[token[3]];
                }

                host = token[1] + value + host;
            }
        });
        // Foo-bar!
        url = this.context_.base_url + url;
        if (route.requirements && ("_scheme" in route.requirements) && this.getScheme() != route.requirements["_scheme"]) {
            url = route.requirements["_scheme"] + "://" + (host || this.getHost()) + url;
        } else if ("undefined" !== typeof route.schemes && "undefined" !== typeof route.schemes[0] && this.getScheme() !== route.schemes[0]) {
            url = route.schemes[0] + "://" + (host || this.getHost()) + url;
        } else if (host && this.getHost() !== host + ('' === port ? '' : ':' + port)) {
          url = this.getScheme() + "://" + host + ('' === port ? '' : ':' + port) + url;
        } else if (absolute === true) {
            url = this.getScheme() + "://" + this.getHost() + url;
        }

        if (Object.keys(unusedParams).length > 0) {
            let prefix;
            let queryParams = [];
            let add = (key, value) => {
                // if value is a function then call it and assign it's return value as value
                value = (typeof value === 'function') ? value() : value;

                // change null to empty string
                value = (value === null) ? '' : value;

                queryParams.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
            };

            for (prefix in unusedParams) {
                this.buildQueryParams(prefix, unusedParams[prefix], add);
            }

            url = url + '?' + queryParams.join('&').replace(/%20/g, '+');
        }

        return url;
    }

}

/**
 * @typedef {{
 *     tokens: (Array.<Array.<string>>),
 *     defaults: (Object.<string, string>),
 *     requirements: Object,
 *     hosttokens: (Array.<string>)
 * }}
 */
Router.Route;

/**
 * @typedef {{
 *     base_url: (string)
 * }}
 */
Router.Context;

/**
 * Router singleton.
 * @const
 * @type {Router}
 */
const Routing = new Router();
