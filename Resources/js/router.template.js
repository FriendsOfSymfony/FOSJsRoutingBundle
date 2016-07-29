(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['fos'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory();
    } else {
        var result = factory();

        // Browser globals (root is window)
        root.Routing = result.Routing;
        root.fos = {
            Router: result.Router,
        };
  }
}(this, function () {
    <%= contents %>

    return { Router: Router, Routing: Routing };
}));
