FOSJsRoutingBundle
==================

Port of the incredible plugin
[chCmsExposeRoutingPlugin](https://github.com/themouette/chCmsExposeRoutingPlugin).

* [Installation](#installation)
* [Usage](#usage)
  - [Generating URIs](#generating-uris)
  - [Commands](#commands)
    - [fos:js-routing:dump](#fosjs-routingdump)
    - [fos:js-routing:debug](#fosjs-routingdebug)
  - [HTTP Caching](#http-caching)
* [Compiling the JavaScript files](#compiling-the-javascript-files)


Installation
------------

Require [`friendsofsymfony/jsrouting-bundle`](https://packagist.org/packages/friendsofsymfony/jsrouting-bundle)
into your `composer.json` file:


``` json
{
    "require": {
        "friendsofsymfony/jsrouting-bundle": "@stable"
    }
}
```

**Protip:** you should browse the
[`friendsofsymfony/jsrouting-bundle`](https://packagist.org/packages/friendsofsymfony/jsrouting-bundle)
page to choose a stable version to use, avoid the `@stable` meta constraint.

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
    );
}
```

Register the routing definition in `app/config/routing.yml`:

``` yml
# app/config/routing.yml
fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"
```

Publish assets:

    $ php app/console assets:install --symlink web


Usage
-----

Add these two lines in your layout:

```
<script src="{{ asset('bundles/fosjsrouting/js/router.js') }}"></script>
<script src="{{ path('fos_js_routing_js', {"callback": "fos.Router.setData"}) }}"></script>
```

**Note:** if you are not using Twig, then it is no problem. What you need is to
add the two JavaScript files above loaded at some point in your web page.

### Generating URIs

It's as simple as calling:

```JavaScript
Routing.generate('route_id', /* your params */)
```

Or if you want to generate **absolute URLs**:

```JavaScript
Routing.generate('route_id', /* your params */, true)
```

Assuming some route definitions:

```yaml
# app/config/routing.yml
my_route_to_expose:
    pattern: /foo/{id}/bar
    defaults: { _controller: HelloBundle:Hello:index }
    options:
        expose: true

my_route_to_expose_with_defaults:
    pattern: /blog/{page}
    defaults: { _controller: AcmeBlogBundle:Blog:index, page: 1 }
    options:
        expose: true
```

Or using annotations:

    # src/Acme/DemoBundle/Controller/DefaultController.php
    /**
     * @Route("/foo/{id}/bar", name="my_route_to_expose", options={"expose"=true})
     */
    public function exposedAction($foo)

You can use the `generate()` method that way:

```JavaScript
Routing.generate('my_route_to_expose', { id: 10 });
// will result in /foo/10/bar

Routing.generate('my_route_to_expose', { id: 10, foo: "bar" });
// will result in /foo/10/bar?foo=bar

$.get(Routing.generate('my_route_to_expose', { id: 10, foo: "bar" }));
// will call /foo/10/bar?foo=bar

Routing.generate('my_route_to_expose_with_defaults');
// will result in /blog/1

Routing.generate('my_route_to_expose_with_defaults', { id: 2 });
// will result in /blog/2

Routing.generate('my_route_to_expose_with_defaults', { foo: "bar" });
// will result in /blog/1?foo=bar

Routing.generate('my_route_to_expose_with_defaults', { id: 2, foo: "bar" });
// will result in /blog/2?foo=bar
```

Moreover, you can configure a list of routes to expose in `app/config/config.yml`:

``` yaml
# app/config/config.yml
fos_js_routing:
    routes_to_expose: [ route_1, route_2, ... ]
```

These routes will be added to the exposed routes. You can use regular expression patterns
if you don't want to list all your routes name by name.

You can prevent to expose a route by configuring it as below:

```yml
# app/config/routing.yml
my_very_secret_route:
    pattern: /admin
    defaults: { _controller: HelloBundle:Admin:index }
    options:
        expose: false
```

### HTTP Caching

You can enable HTTP caching as below:

```
# app/config/config.yml
fos_js_routing:
    cache_control:
        # All are optional, defaults shown
        public: false   # can be true (public) or false (private)
        maxage: null    # integer value, e.g. 300
        smaxage: null   # integer value, e.g. 300
        expires: null   # anything that can be fed to "new \DateTime($expires)", e.g. "5 minutes"
        vary: []        # string or array, e.g. "Cookie" or [ Cookie, Accept ]
```

### Commands

#### fos:js-routing:dump

This command dumps the route information into a file so that instead of having
the controller generated JavaScript, you can use a normal file. This also allows
to combine the routes with the other JavaScript files in assetic.

    $ php app/console fos:js-routing:dump

Instead of the line

    <script src="{{ path('fos_js_routing_js', {"callback": "fos.Router.setData"}) }}"></script>

you now include this as

    <script src="/js/fos_js_routes.js"></script>

Or inside assetic, do

    {% javascripts filter='?yui_js'
        'bundles/fosjsrouting/js/router.js'
        'js/fos_js_routes.js'
    %}
        <script src="{{ asset_url }}"></script>
    {% endjavascripts %}

**Important:** you should follow the Symfony documentation about generating URLs
in the console: [Configuring The Request Context
Globally](http://symfony.com/doc/current/cookbook/console/sending_emails.html#configuring-the-request-context-globally).

*Hint*: If you are using JMSI18nRoutingBundle, you need to run the command with
the `--locale` parameter once for each locale you use and adjust your include paths
accordingly.


#### fos:js-routing:debug

This command lists all exposed routes:

    $ php app/console fos:js-routing:debug [name]


Compiling the JavaScript files
------------------------------

Note: We already provide a compiled version of the JavaScript; this section is only
relevant if you want to make changes to this script.

In order to re-compile the JavaScript source files that we ship with this bundle, you
need the Google Closure Tools. You need the
[**plovr**](http://plovr.com/download.html) tool, it is a Java ARchive, so you
also need a working Java environment. You can re-compile the JavaScript with the
following command:

    $ java -jar plovr.jar build Resources/config/plovr/compile.js

Alternatively, you can use the JMSGoogleClosureBundle. If you install this bundle,
you can re-compile the JavaScript with the following command:

    $ php app/console plovr:build @FOSJsRoutingBundle/compile.js


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

    $ composer install --dev

Run it using PHPUnit:

    $ phpunit

### JavaScript Test Suite

You need [PhantomJS](http://phantomjs.org/):

    $ phantomjs Resources/js/run_jsunit.js Resources/js/router_test.html
