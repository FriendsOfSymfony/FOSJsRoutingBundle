ExposeRoutingBundle
===================

Port of the incredible plugin [chCmsExposeRoutingPlugin](https://github.com/themouette/chCmsExposeRoutingPlugin).

Installation
------------

Add this bundle as a submodule:

> git submodule add git://github.com/Bazinga/ExposeRoutingBundle.git vendor/bundles/Bazinga/ExposeRoutingBundle

Register the namespace in `app/autoload.php`:

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Bazinga' => __DIR__.'/../vendor/bundles',
    ));

Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bazinga\ExposeRoutingBundle\BazingaExposeRoutingBundle(),
        );
    }

Register the routing in `app/config/routing.yml`:

    # app/config/routing.yml
    bazinga_exposerouting:
        resource: "@BazingaExposeRoutingBundle/Resources/config/routing/routing.xml"

Publish assets:

    $ php app/console assets:install --symlink web



Usage
-----

Just add these two lines in your layout:

    <script type="text/javascript" src="{{ asset('bundles/bazingaexposerouting/js/routing.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/routing.js') }}"></script>


It's as simple as calling: `Routing.generate('route_id', /* your params */)`.

Imagine a route definition like this one:

    # app/config/routing.yml
    my_route_to_expose:
        pattern:  /foo/{id}/bar
        defaults:  { _controller: HelloBundle:Hello:index }
        options:
            expose: true

You can do:

    Routing.generate('my_route_to_expose', {id: 10});
    // will result in /foo/10/bar

    Routing.generate('my_route_to_expose', {"id": 10, "foo":"bar"});
    // will result in /foo/10/bar?foo-bar

    $.get(Routing.generate('my_route_to_expose', {"id": 10, "foo":"bar"}));
    // will call /foo/10/bar?foo-bar


Credits
-------

* William DURAND (Bazinga) as main author.
* Julien MUETTON (Carpe Hora) for the inspiration.
