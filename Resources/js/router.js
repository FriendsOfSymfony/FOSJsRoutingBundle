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
        this.context_ = context || {base_url: '', prefix: '', host: '', port: '', scheme: '', locale: ''};
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
        if ('locale' in data) {
          this.setLocale(data['locale']);
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
     * @param {string} locale
     */
    setLocale(locale) {
      this.context_.locale = locale;
    }

    /**
     * @return {string}
     */
    getLocale() {
      return this.context_.locale;
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
        let sf41i18nName = name + '.' + this.context_.locale;
        let prefixedSf41i18nName = this.context_.prefix + name + '.' + this.context_.locale;
        let variants = [prefixedName, sf41i18nName, prefixedSf41i18nName, name];

        for (let i in variants) {
            if (variants[i] in this.routes_) {
                return this.routes_[variants[i]];
            }
        }

        throw new Error('The route "' + name + '" does not exist.');
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
                url = Router.encodePathComponent(token[1]) + url;
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
                        let encodedValue = Router.encodePathComponent(value);

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
            const currentHost = host || this.getHost();

            url = route.requirements["_scheme"] + "://" + currentHost + (currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
        } else if ("undefined" !== typeof route.schemes && "undefined" !== typeof route.schemes[0] && this.getScheme() !== route.schemes[0]) {
            const currentHost = host || this.getHost();

            url = route.schemes[0] + "://" + currentHost + (currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
        } else if (host && this.getHost() !== host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port)) {
            url = this.getScheme() + "://" + host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
        } else if (absolute === true) {
            url = this.getScheme() + "://" + this.getHost() + (this.getHost().indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
        }

        if (Object.keys(unusedParams).length > 0) {
            let prefix;
            let queryParams = [];
            let add = (key, value) => {
                // if value is a function then call it and assign it's return value as value
                value = (typeof value === 'function') ? value() : value;

                // change null to empty string
                value = (value === null) ? '' : value;

                queryParams.push(Router.encodeQueryComponent(key) + '=' + Router.encodeQueryComponent(value));
            };

            for (prefix in unusedParams) {
                this.buildQueryParams(prefix, unusedParams[prefix], add);
            }

            url = url + '?' + queryParams.join('&');
        }

        return url;
    }

    /**
     * Returns the given string encoded to mimic Symfony URL generator.
     *
     * @param {string} value
     * @return {string}
     */
    static customEncodeURIComponent(value) {
        return encodeURIComponent(value)
            .replace(/%2F/g, '/')
            .replace(/%40/g, '@')
            .replace(/%3A/g, ':')
            .replace(/%21/g, '!')
            .replace(/%3B/g, ';')
            .replace(/%2C/g, ',')
            .replace(/%2A/g, '*')
            .replace(/\(/g, '%28')
            .replace(/\)/g, '%29')
            .replace(/'/g, '%27')
        ;
    }

    /**
     * Returns the given path properly encoded to mimic Symfony URL generator.
     *
     * @param {string} value
     * @return {string}
     */
    static encodePathComponent(value) {
        return Router.customEncodeURIComponent(value)
            .replace(/%3D/g, '=')
            .replace(/%2B/g, '+')
            .replace(/%21/g, '!')
            .replace(/%7C/g, '|')
        ;
    }

    /**
     * Returns the given query parameter or value properly encoded to mimic Symfony URL generator.
     *
     * @param {string} value
     * @return {string}
     */
    static encodeQueryComponent(value) {
        return Router.customEncodeURIComponent(value)
            .replace(/%3F/g, '?')
        ;
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
