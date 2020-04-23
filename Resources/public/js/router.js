(function (root, factory) {
    var routing = factory();
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define([], routing.Routing);
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = routing.Routing;
    } else {
        // Browser globals (root is window)
        root.Routing = routing.Routing;
        root.fos = {
            Router: routing.Router
        };
    }
}(this, function () {
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

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Router = function () {

    /**
     * @constructor
     * @param {Router.Context=} context
     * @param {Object.<string, Router.Route>=} routes
     */
    function Router(context, routes) {
        _classCallCheck(this, Router);

        this.context_ = context || { base_url: '', prefix: '', host: '', port: '', scheme: '', locale: '' };
        this.setRoutes(routes || {});
    }

    /**
     * Returns the current instance.
     * @returns {Router}
     */


    _createClass(Router, [{
        key: 'setRoutingData',


        /**
         * Sets data for the current instance
         * @param {Object} data
         */
        value: function setRoutingData(data) {
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

    }, {
        key: 'setRoutes',
        value: function setRoutes(routes) {
            this.routes_ = Object.freeze(routes);
        }

        /**
         * @return {Object.<string, Router.Route>} routes
         */

    }, {
        key: 'getRoutes',
        value: function getRoutes() {
            return this.routes_;
        }

        /**
         * @param {string} baseUrl
         */

    }, {
        key: 'setBaseUrl',
        value: function setBaseUrl(baseUrl) {
            this.context_.base_url = baseUrl;
        }

        /**
         * @return {string}
         */

    }, {
        key: 'getBaseUrl',
        value: function getBaseUrl() {
            return this.context_.base_url;
        }

        /**
         * @param {string} prefix
         */

    }, {
        key: 'setPrefix',
        value: function setPrefix(prefix) {
            this.context_.prefix = prefix;
        }

        /**
         * @param {string} scheme
         */

    }, {
        key: 'setScheme',
        value: function setScheme(scheme) {
            this.context_.scheme = scheme;
        }

        /**
         * @return {string}
         */

    }, {
        key: 'getScheme',
        value: function getScheme() {
            return this.context_.scheme;
        }

        /**
         * @param {string} host
         */

    }, {
        key: 'setHost',
        value: function setHost(host) {
            this.context_.host = host;
        }

        /**
         * @return {string}
         */

    }, {
        key: 'getHost',
        value: function getHost() {
            return this.context_.host;
        }

        /**
         * @param {string} port
        */

    }, {
        key: 'setPort',
        value: function setPort(port) {
            this.context_.port = port;
        }

        /**
         * @return {string}
         */

    }, {
        key: 'getPort',
        value: function getPort() {
            return this.context_.port;
        }
    }, {
        key: 'setLocale',


        /**
         * @param {string} locale
         */
        value: function setLocale(locale) {
            this.context_.locale = locale;
        }

        /**
         * @return {string}
         */

    }, {
        key: 'getLocale',
        value: function getLocale() {
            return this.context_.locale;
        }
    }, {
        key: 'buildQueryParams',


        /**
         * Builds query string params added to a URL.
         * Port of jQuery's $.param() function, so credit is due there.
         *
         * @param {string} prefix
         * @param {Array|Object|string} params
         * @param {Function} add
         */
        value: function buildQueryParams(prefix, params, add) {
            var _this = this;

            var name = void 0;
            var rbracket = new RegExp(/\[\]$/);

            if (params instanceof Array) {
                params.forEach(function (val, i) {
                    if (rbracket.test(prefix)) {
                        add(prefix, val);
                    } else {
                        _this.buildQueryParams(prefix + '[' + ((typeof val === 'undefined' ? 'undefined' : _typeof(val)) === 'object' ? i : '') + ']', val, add);
                    }
                });
            } else if ((typeof params === 'undefined' ? 'undefined' : _typeof(params)) === 'object') {
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

    }, {
        key: 'getRoute',
        value: function getRoute(name) {
            var prefixedName = this.context_.prefix + name;
            var sf41i18nName = name + '.' + this.context_.locale;
            var prefixedSf41i18nName = this.context_.prefix + name + '.' + this.context_.locale;
            var variants = [prefixedName, sf41i18nName, prefixedSf41i18nName, name];

            for (var i in variants) {
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

    }, {
        key: 'generate',
        value: function generate(name, opt_params) {
            var absolute = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            var route = this.getRoute(name),
                params = opt_params || {},
                unusedParams = _extends({}, params),
                url = '',
                optional = true,
                host = '',
                port = typeof this.getPort() == "undefined" || this.getPort() === null ? '' : this.getPort();

            route.tokens.forEach(function (token) {
                if ('text' === token[0]) {
                    url = Router.encodePathComponent(token[1]) + url;
                    optional = false;

                    return;
                }

                if ('variable' === token[0]) {
                    var hasDefault = route.defaults && token[3] in route.defaults;
                    if (false === optional || !hasDefault || token[3] in params && params[token[3]] != route.defaults[token[3]]) {
                        var value = void 0;

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

                        var empty = true === value || false === value || '' === value;

                        if (!empty || !optional) {
                            var encodedValue = Router.encodePathComponent(value);

                            if ('null' === encodedValue && null === value) {
                                encodedValue = '';
                            }

                            url = token[1] + encodedValue + url;
                        }

                        optional = false;
                    } else if (hasDefault && token[3] in unusedParams) {
                        delete unusedParams[token[3]];
                    }

                    return;
                }

                throw new Error('The token type "' + token[0] + '" is not supported.');
            });

            if (url === '') {
                url = '/';
            }

            route.hosttokens.forEach(function (token) {
                var value = void 0;

                if ('text' === token[0]) {
                    host = token[1] + host;

                    return;
                }

                if ('variable' === token[0]) {
                    if (token[3] in params) {
                        value = params[token[3]];
                        delete unusedParams[token[3]];
                    } else if (route.defaults && token[3] in route.defaults) {
                        value = route.defaults[token[3]];
                    }

                    host = token[1] + value + host;
                }
            });
            // Foo-bar!
            url = this.context_.base_url + url;

            if (route.requirements && "_scheme" in route.requirements && this.getScheme() != route.requirements["_scheme"]) {
                var currentHost = host || this.getHost();

                url = route.requirements["_scheme"] + "://" + currentHost + (currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
            } else if ("undefined" !== typeof route.schemes && "undefined" !== typeof route.schemes[0] && this.getScheme() !== route.schemes[0]) {
                var _currentHost = host || this.getHost();

                url = route.schemes[0] + "://" + _currentHost + (_currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
            } else if (host && this.getHost() !== host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port)) {
                url = this.getScheme() + "://" + host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
            } else if (absolute === true) {
                url = this.getScheme() + "://" + this.getHost() + (this.getHost().indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
            }

            if (Object.keys(unusedParams).length > 0) {
                var prefix = void 0;
                var queryParams = [];
                var add = function add(key, value) {
                    // if value is a function then call it and assign it's return value as value
                    value = typeof value === 'function' ? value() : value;

                    // change null to empty string
                    value = value === null ? '' : value;

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

    }], [{
        key: 'getInstance',
        value: function getInstance() {
            return Routing;
        }

        /**
         * Configures the current Router instance with the provided data.
         * @param {Object} data
         */

    }, {
        key: 'setData',
        value: function setData(data) {
            var router = Router.getInstance();

            router.setRoutingData(data);
        }
    }, {
        key: 'customEncodeURIComponent',
        value: function customEncodeURIComponent(value) {
            return encodeURIComponent(value).replace(/%2F/g, '/').replace(/%40/g, '@').replace(/%3A/g, ':').replace(/%21/g, '!').replace(/%3B/g, ';').replace(/%2C/g, ',').replace(/%2A/g, '*').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/'/g, '%27');
        }

        /**
         * Returns the given path properly encoded to mimic Symfony URL generator.
         *
         * @param {string} value
         * @return {string}
         */

    }, {
        key: 'encodePathComponent',
        value: function encodePathComponent(value) {
            return Router.customEncodeURIComponent(value).replace(/%3D/g, '=').replace(/%2B/g, '+').replace(/%21/g, '!').replace(/%7C/g, '|');
        }

        /**
         * Returns the given query parameter or value properly encoded to mimic Symfony URL generator.
         *
         * @param {string} value
         * @return {string}
         */

    }, {
        key: 'encodeQueryComponent',
        value: function encodeQueryComponent(value) {
            return Router.customEncodeURIComponent(value).replace(/%3F/g, '?');
        }
    }]);

    return Router;
}();

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
var Routing = new Router();

    return { Router: Router, Routing: Routing };
}));