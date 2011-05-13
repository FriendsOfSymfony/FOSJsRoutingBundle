/**
 * define Routing class
 */

var Routing = Routing || {};

(function(Routing, $, undefined) {

  // now register our routing methods
  $.extend(Routing, (function() {

    var _routes = {},
        _defaults = {},
        rquery = /\?/,
        rabsurl = /^\//,
        rescregexp = /[-[\]()*+?.,\\^$|#\s]/g,
        rdblslash = /\/\//g;

    /**
     * @api private
     * prepare a regexp part with several caracters/parts
     * having to be escaped.
     *
     * @param {Array|string} separators   A list of separators.
     * @param {String} unescaped          A meta character to use in regexp.
     * return {String}                    The regexp part, ready to use.
     */
    function regexify(separators, unescaped) {
      var _i, _separators = [];
      // make sure separator is an array
      if (!$.isArray(separators)) {
        separators = [separators];
      }
      // escape every separator
      for (_i in separators) {
        _separators[_i] = separators[_i].replace(rescregexp, '\\$&');
      }
      // add unescaped caracters
      if (unescaped) { _separators.push(unescaped); }

      // return in a or
      if (_separators.length > 1) {return _separators.join('|')}
      return _separators[0];
    };

    return {
      /**
       * Route parameter prefix.
       *
       * @type {String}
       * @api public
       */
      variablePrefix: '{',
      /**
       * Route parameter suffix.
       *
       * @type {String}
       * @api public
       */
      variableSuffix: '}',
      /**
       * Route url separtor list.
       *
       * @type {String}
       * @api public
       */
      segmentSeparators: ['/', '.'],
      /**
       * Route url prefix to use.
       *
       * @type {String}
       * @api public
       */
      prefix: '',

      /**
       * Generate a route url from route id and params.
       *
       * @param {String}  route_id  the id of route to generate url for.
       * @param {Objects} params    the parameters to append to the route.
       * @return {String} generated url.
       * @api public
       */
      generate: function(route_id, params) {
        var _route = Routing.get(route_id),
            _i,
            _separators = Routing.segmentSeparators,
            _prefix = '(' + regexify(_separators, '^') +
                        ')' + regexify(Routing.variablePrefix),
            _suffix = regexify(Routing.variableSuffix) +
                        '(' + regexify(_separators, '$') + ')',
            _params = $.extend({}, _defaults[route_id] || {}, params || {}),
            _queryString,
            _url = _route;

        if (!_url) {
          throw 'No matching route for ' + route_id;
        }

        for (_i in _params) {
          var _r = new RegExp(_prefix + _i + _suffix, '');

          if (_r.test(_url)) {
            _url = _url.replace(_r, '$1' + _params[_i] + '$2');
            delete(_params[_i]);
          }
        }
        _queryString = $.param(_params);
        if (_queryString.length) {
          _url += (rquery.test(_url) ? '&' : '?') + _queryString;
        }

        _url = (rabsurl.test(_url) ? '' : '/') + _url;
        _url = Routing.prefix + _url;
        _url = (rabsurl.test(_url) ? '' : '/') + _url;

        return _url.replace(rdblslash, '/');
      },
      /**
       * connect a route.
       *
       * @param {String} id       the route id.
       * @param {String} pattern  the url pattern.
       * @param {Objects} params  the  default parameters.
       * @return {Object} Routing.
       * @api public
       */
      connect: function(id, pattern, defaults) {
        _routes[id] = pattern;
        _defaults[id] = $.extend({}, defaults || {});
        return Routing;
      },
      /**
       * retrieve a route by it's id.
       *
       * @param {String} route_id the route id to retrieve.
       * @return {String} requested route.
       * @api public
       */
      get: function(route_id) {
        return _routes[route_id] || undefined;
      },
      /**
       * determines wether a route is registered or not.
       *
       * @param {String} route_id the route id to retrieve.
       * @return {Boolean} wether the route is registered or not.
       * @api public
       */
      has: function(route_id) {
        return (_routes[route_id] ? true : false);
      },
      /**
       * clears all routes
       *
       * @return {Object} Routing.
       * @api public
       */
      flush: function() {
        _routes = {};
        _defaults = {};
        return Routing;
      }
    }; // end of return
  })());
})(Routing, jQuery);
