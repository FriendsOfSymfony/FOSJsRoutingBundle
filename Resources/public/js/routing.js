/**
 * this file defines the routing api
 */
var Routing = Routing || {};
(function(Routing, $, undefined) {

  // now register our routing methods
  $.extend(Routing, (function() {

    var _routes = {},
        rquery = /\?/,
        rabsurl = /^\//;

    /**
     * replaces variables in sUrl.
     */
    function _replace(sUrl, hReplacement) {
      };

    return {
      variablePrefix: ':',
      segmentSeparators: '[/\.$^]',
      prefix: '',
      /**
       * generate a route
       */
      generate: function(route_id, params) {
        var _route = _routes[route_id],
            _i,
            _prefix = '(' + Routing.segmentSeparators + ')' + Routing.variablePrefix,
            _suffix = '(' + Routing.segmentSeparators + ')',
            _params = $.extend({}, params || {}),
            _queryString,
            _url;

        if (!_route) {
          throw 'No matching route for ' + route_id;
        }

        _url = _route;

        for (_i in _params) {
          var _r = new RegExp( _prefix + _i + _suffix, 'g');

          if (_r.test(_url)) {
            _url = _url.replace(_r, '$1' + _params[_i] + '$2');
            delete(_params[_i])
          }
        }
        _queryString = $.param(_params);
        if (_queryString.length) {
          _url += ( rquery.test(_url) ? '&' : '?' ) + _queryString;
        }

        return Routing.prefix + (rabsurl.test(_url) ? '' : '/') + _url;
      },
      register: function(id, pattern) {
        _routes[id] = pattern;
      }
    }; // end of return 
  })());
})(Routing, jQuery);
