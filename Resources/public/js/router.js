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
    var exports = {};
    "use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = exports.Routing = exports.Router = void 0;

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

var Router = /*#__PURE__*/function () {
  function Router(context, routes) {
    _classCallCheck(this, Router);

    this.context_ = context || {
      base_url: '',
      prefix: '',
      host: '',
      port: '',
      scheme: '',
      locale: ''
    };
    this.setRoutes(routes || {});
  }

  _createClass(Router, [{
    key: "setRoutingData",
    value: function setRoutingData(data) {
      this.setBaseUrl(data['base_url']);
      this.setRoutes(data['routes']);

      if (typeof data.prefix !== 'undefined') {
        this.setPrefix(data['prefix']);
      }

      if (typeof data.port !== 'undefined') {
        this.setPort(data['port']);
      }

      if (typeof data.locale !== 'undefined') {
        this.setLocale(data['locale']);
      }

      this.setHost(data['host']);

      if (typeof data.scheme !== 'undefined') {
        this.setScheme(data['scheme']);
      }
    }
  }, {
    key: "setRoutes",
    value: function setRoutes(routes) {
      this.routes_ = Object.freeze(routes);
    }
  }, {
    key: "getRoutes",
    value: function getRoutes() {
      return this.routes_;
    }
  }, {
    key: "setBaseUrl",
    value: function setBaseUrl(baseUrl) {
      this.context_.base_url = baseUrl;
    }
  }, {
    key: "getBaseUrl",
    value: function getBaseUrl() {
      return this.context_.base_url;
    }
  }, {
    key: "setPrefix",
    value: function setPrefix(prefix) {
      this.context_.prefix = prefix;
    }
  }, {
    key: "setScheme",
    value: function setScheme(scheme) {
      this.context_.scheme = scheme;
    }
  }, {
    key: "getScheme",
    value: function getScheme() {
      return this.context_.scheme;
    }
  }, {
    key: "setHost",
    value: function setHost(host) {
      this.context_.host = host;
    }
  }, {
    key: "getHost",
    value: function getHost() {
      return this.context_.host;
    }
  }, {
    key: "setPort",
    value: function setPort(port) {
      this.context_.port = port;
    }
  }, {
    key: "getPort",
    value: function getPort() {
      return this.context_.port;
    }
  }, {
    key: "setLocale",
    value: function setLocale(locale) {
      this.context_.locale = locale;
    }
  }, {
    key: "getLocale",
    value: function getLocale() {
      return this.context_.locale;
    }
  }, {
    key: "buildQueryParams",
    value:
    /**
     * Builds query string params added to a URL.
     * Port of jQuery's $.param() function, so credit is due there.
     */
    function buildQueryParams(prefix, params, add) {
      var _this = this;

      var name;
      var rbracket = new RegExp(/\[\]$/);

      if (params instanceof Array) {
        params.forEach(function (val, i) {
          if (rbracket.test(prefix)) {
            add(prefix, val);
          } else {
            _this.buildQueryParams(prefix + '[' + (_typeof(val) === 'object' ? i : '') + ']', val, add);
          }
        });
      } else if (_typeof(params) === 'object') {
        for (name in params) {
          this.buildQueryParams(prefix + '[' + name + ']', params[name], add);
        }
      } else {
        add(prefix, params);
      }
    }
    /**
     * Returns a raw route object.
     */

  }, {
    key: "getRoute",
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
     */

  }, {
    key: "generate",
    value: function generate(name, opt_params, absolute) {
      var route = this.getRoute(name);
      var params = opt_params || {};

      var unusedParams = _extends({}, params);

      var url = '';
      var optional = true;
      var host = '';
      var port = typeof this.getPort() == 'undefined' || this.getPort() === null ? '' : this.getPort();
      route.tokens.forEach(function (token) {
        if ('text' === token[0] && typeof token[1] === 'string') {
          url = Router.encodePathComponent(token[1]) + url;
          optional = false;
          return;
        }

        if ('variable' === token[0]) {
          if (token.length === 6 && token[5] === true) {
            // Sixth part of the token array indicates if it should be included in case of defaults
            optional = false;
          }

          var hasDefault = route.defaults && !Array.isArray(route.defaults) && typeof token[3] === 'string' && token[3] in route.defaults;

          if (false === optional || !hasDefault || typeof token[3] === 'string' && token[3] in params && !Array.isArray(route.defaults) && params[token[3]] != route.defaults[token[3]]) {
            var value;

            if (typeof token[3] === 'string' && token[3] in params) {
              value = params[token[3]];
              delete unusedParams[token[3]];
            } else if (typeof token[3] === 'string' && hasDefault && !Array.isArray(route.defaults)) {
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
          } else if (hasDefault && typeof token[3] === 'string' && token[3] in unusedParams) {
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
        var value;

        if ('text' === token[0]) {
          host = token[1] + host;
          return;
        }

        if ('variable' === token[0]) {
          if (token[3] in params) {
            value = params[token[3]];
            delete unusedParams[token[3]];
          } else if (route.defaults && !Array.isArray(route.defaults) && token[3] in route.defaults) {
            value = route.defaults[token[3]];
          }

          host = token[1] + value + host;
        }
      });
      url = this.context_.base_url + url;

      if (route.requirements && '_scheme' in route.requirements && this.getScheme() != route.requirements['_scheme']) {
        var currentHost = host || this.getHost();
        url = route.requirements['_scheme'] + '://' + currentHost + (currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
      } else if ('undefined' !== typeof route.schemes && 'undefined' !== typeof route.schemes[0] && this.getScheme() !== route.schemes[0]) {
        var _currentHost = host || this.getHost();

        url = route.schemes[0] + '://' + _currentHost + (_currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
      } else if (host && this.getHost() !== host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port)) {
        url = this.getScheme() + '://' + host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
      } else if (absolute === true) {
        url = this.getScheme() + '://' + this.getHost() + (this.getHost().indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
      }

      if (Object.keys(unusedParams).length > 0) {
        var queryParams = [];

        var add = function add(key, value) {
          // if value is a function then call it and assign it's return value as value
          value = typeof value === 'function' ? value() : value; // change null to empty string

          value = value === null ? '' : value;
          queryParams.push(Router.encodeQueryComponent(key) + '=' + Router.encodeQueryComponent(value));
        };

        for (var _prefix in unusedParams) {
          if (unusedParams.hasOwnProperty(_prefix)) {
            this.buildQueryParams(_prefix, unusedParams[_prefix], add);
          }
        }

        url = url + '?' + queryParams.join('&');
      }

      return url;
    }
    /**
     * Returns the given string encoded to mimic Symfony URL generator.
     */

  }], [{
    key: "getInstance",
    value: function getInstance() {
      return Routing;
    }
  }, {
    key: "setData",
    value: function setData(data) {
      var router = Router.getInstance();
      router.setRoutingData(data);
    }
  }, {
    key: "customEncodeURIComponent",
    value: function customEncodeURIComponent(value) {
      return encodeURIComponent(value).replace(/%2F/g, '/').replace(/%40/g, '@').replace(/%3A/g, ':').replace(/%21/g, '!').replace(/%3B/g, ';').replace(/%2C/g, ',').replace(/%2A/g, '*').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/'/g, '%27');
    }
    /**
     * Returns the given path properly encoded to mimic Symfony URL generator.
     */

  }, {
    key: "encodePathComponent",
    value: function encodePathComponent(value) {
      return Router.customEncodeURIComponent(value).replace(/%3D/g, '=').replace(/%2B/g, '+').replace(/%21/g, '!').replace(/%7C/g, '|');
    }
    /**
     * Returns the given query parameter or value properly encoded to mimic Symfony URL generator.
     */

  }, {
    key: "encodeQueryComponent",
    value: function encodeQueryComponent(value) {
      return Router.customEncodeURIComponent(value).replace(/%3F/g, '?');
    }
  }]);

  return Router;
}();

exports.Router = Router;
var Routing = new Router();
exports.Routing = Routing;
var _default = Routing;
exports["default"] = _default;

    return { Router: Router, Routing: Routing };
}));