FOSJsRoutingBundle
==================

Port of the incredible plugin [chCmsExposeRoutingPlugin](https://github.com/themouette/chCmsExposeRoutingPlugin).

Installation
------------

Add this bundle as a submodule:

    git submodule add git://github.com/FriendsOfSymfony/FOSJsRoutingBundle.git vendor/bundles/FOS/JsRoutingBundle
    
Or add the following lines in your `deps` file:

``` ini
[FOSJsRoutingBundle]
	git=git://github.com/FriendsOfSymfony/FOSJsRoutingBundle.git
	target=/bundles/FOS/JsRoutingBundle
```

Register the namespace in `app/autoload.php`:

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'FOS' => __DIR__.'/../vendor/bundles',
    ));

Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        );
    }

Register the routing in `app/config/routing.yml`:

    # app/config/routing.yml
    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

Publish assets:

    $ php app/console assets:install --symlink web


Usage
-----

Just add these two lines in your layout:

    <script type="text/javascript" src="{{ asset('bundles/fosjsrouting/js/router.js') }}"></script>
    <script type="text/javascript" src="{{ path('fos_js_routing_js', {"callback": "fos.Router.setData"}) }}"></script>


It's as simple as calling: `Routing.generate('route_id', /* your params */)`.

Imagine some route definitions:

    # app/config/routing.yml
    my_route_to_expose:
        pattern:  /foo/{id}/bar
        defaults:  { _controller: HelloBundle:Hello:index }
        options:
            expose: true

    my_route_to_expose_with_defaults:
        pattern:  /blog/{page}
        defaults: { _controller: AcmeBlogBundle:Blog:index, page: 1 }
        options:
            expose: true

Or with annotations:
   
    # src/Acme/DemoBundle/Controller/DefaultController.php
    /**
     * @Route ("/foo/{id}/bar", name="my_route_to_expose", options={"expose"=true}))
     */
    public function exposedAction($foo)


You can do:

    Routing.generate('my_route_to_expose', { id: 10 });
    // will result in /foo/10/bar

    Routing.generate('my_route_to_expose', { "id": 10, "foo": "bar" });
    // will result in /foo/10/bar?foo=bar

    $.get(Routing.generate('my_route_to_expose', { "id": 10, "foo": "bar" }));
    // will call /foo/10/bar?foo=bar

    Routing.generate('my_route_to_expose_with_defaults');
    // will result in /blog/1

    Routing.generate('my_route_to_expose_with_defaults', { id: 2 });
    // will result in /blog/2

    Routing.generate('my_route_to_expose_with_defaults', { "foo": "bar" });
    // will result in /blog/1?foo=bar

    Routing.generate('my_route_to_expose_with_defaults', { id: 2, "foo": "bar" });
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


Command
-------

A command is provided to list all exposed routes: `fos:js-routing:debug`:

    $ php app/console fos:js-routing:debug [name]


Compiling the Javascript files
------------------------------

Note: We already provide a compiled version of the Javascript; this section is only
relevant if you want to make changes to this script.

In order to re-compile the Javascript source files that we ship with this bundle, you
need the Google Closure Tools. While you can install these dependencies manually, we
recommend that you instead install the JMSGoogleClosureBundle. If you install this bundle,
you can re-compile the Javascript with the following command:

    $ php app/console plovr:build @FOSJsRoutingBundle/compile.js


Credits
-------

* William DURAND as main author.
* Julien MUETTON (Carpe Hora) for the inspiration.
