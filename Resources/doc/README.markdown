FOSJsRoutingBundle
==================

Port of the incredible plugin [chCmsExposeRoutingPlugin](https://github.com/themouette/chCmsExposeRoutingPlugin).

Installation
------------

For the management of the bundle you have 2 options: *Composer* or *submodules*.

### Through Composer (Symfony 2.1+):

Add the following lines in your `composer.json` file:

``` js
"require": {
    "friendsofsymfony/jsrouting-bundle": "~1.1"
}
```

Run Composer to download and install the bundle:

    $ php composer.phar update friendsofsymfony/jsrouting-bundle

### Through submodules (Symfony 2.0):

    $ git submodule add git://github.com/FriendsOfSymfony/FOSJsRoutingBundle.git vendor/bundles/FOS/JsRoutingBundle

Or add the following lines to your `deps` file:

``` ini
[FOSJsRoutingBundle]
    git=git://github.com/FriendsOfSymfony/FOSJsRoutingBundle.git
    target=/bundles/FOS/JsRoutingBundle
```

After the download of the files, register the namespace in `app/autoload.php` (only needed if
you are *not* using Composer):

``` php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'FOS' => __DIR__.'/../vendor/bundles',
));
```

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

Register the routing in `app/config/routing.yml`:

``` yml
# app/config/routing.yml

fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"
```

Publish assets:

    $ php app/console assets:install --symlink web


Usage
-----

Just add these two lines in your layout:

    <script src="{{ asset('bundles/fosjsrouting/js/router.js') }}"></script>
    <script src="{{ path('fos_js_routing_js', {"callback": "fos.Router.setData"}) }}"></script>


It's as simple as calling: `Routing.generate('route_id', /* your params */)`.

Or if you want to generate absolute Url: `Routing.generate('route_id', /* your params */, true)`.

Imagine some route definitions:

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

Or with annotations:

    # src/Acme/DemoBundle/Controller/DefaultController.php
    /**
     * @Route("/foo/{id}/bar", name="my_route_to_expose", options={"expose"=true})
     */
    public function exposedAction($foo)


You can do:

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


Moreover, you can configure a list of routes to expose in `app/config/config.yml`:

    # app/config/config.yml
    fos_js_routing:
        routes_to_expose: [ route_1, route_2, ... ]

These routes will be added to the exposed routes. You can use regular expression patterns
if you don't want to list all your routes name by name.

You can prevent to expose a route by configuring it as below:

    # app/config/routing.yml
    my_very_secret_route:
        pattern: /admin
        defaults: { _controller: HelloBundle:Admin:index }
        options:
            expose: false


Commands
--------

### fos:js-routing:dump

This command dumps the route information into a file so that instead of having
the controller generated javascript, you can use a normal file. This also allows
to combine the routes with the other javascript files in assetic.


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


*Hint*: If you are using JMSI18nRoutingBundle, you need to run the command with
the `--locale` parameter once for each locale you use and adjust your include paths
accordingly.


### fos:js-routing:debug

This command lists all exposed routes:

    $ php app/console fos:js-routing:debug [name]


Compiling the JavaScript files
------------------------------

Note: We already provide a compiled version of the Javascript; this section is only
relevant if you want to make changes to this script.

In order to re-compile the JavaScript source files that we ship with this bundle, you
need the Google Closure Tools. While you can install these dependencies manually, we
recommend that you instead install the JMSGoogleClosureBundle. If you install this bundle,
you can re-compile the JavaScript with the following command:

    $ php app/console plovr:build @FOSJsRoutingBundle/compile.js


Credits
-------

* William DURAND as main author.
* Julien MUETTON (Carpe Hora) for the inspiration.
