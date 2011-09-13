{
    "id": "router",
    "paths": ["@FOSJsRoutingBundle/Resources/js"],
    "mode": "ADVANCED",
    "level": "VERBOSE",
    "inputs": "@FOSJsRoutingBundle/Resources/js/export.js",
    "externs": "@FOSJsRoutingBundle/Resources/js/externs.js",

    "define": {
        "goog.DEBUG": false
    },

    "type-prefixes-to-strip": ["goog.debug", "goog.asserts", "goog.assert", "console"],
    "name-suffixes-to-strip": ["logger", "logger_"],

    "output-file": "@FOSJsRoutingBundle/Resources/public/js/router.js",
    "output-wrapper": "/**\n * Portions of this code are from the Google Closure Library,\n * received from the Closure Authors under the Apache 2.0 license.\n *\n * All other code is (C) 2011 FriendsOfSymfony and subject to the MIT license.\n */\n(function() {%output%})();",

    "pretty-print": false,
    "debug": false
}