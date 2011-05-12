ExposeRoutingBundle
===================

Port of the incredible plugin [chCmsExposeRoutingPlugin](https://github.com/themouette/chCmsExposeRoutingPlugin).

Installation
------------

1. Add this bundle as a submodule:

> git submodule add git://github.com/Bazinga/ExposeRoutingBundle.git vendor/bundles/Bazinga/ExposeRoutingBundle

2. Register the namespace in `app/autoload.php`:

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Bazinga' => __DIR__.'/../vendor/bundles',
    ));

3. Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bazinga\ExposeRoutingBundle\BazingaExposeRoutingBundle(),
        );
    }

4. Register the routing in `app/config/routing.yml`:

    # app/config/routing.yml
    bazinga_exposerouting:
        resource: "@BazingaExposeRoutingBundle/Resources/config/routing/routing.xml"

5. Publish assets:

    $ php app/console assets:install --symlink web


Usage
-----

Just add these two lines in your layout:

    <script type="text/javascript" src="{{ asset('bundles/bazingaexposerouting/routing.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/routing.js') }}"></script>


It's as simple as calling `Routing.generate('route_id', /* your params */)`.

    Routing.generate('route_id', {id: 10});
    // will result in /foo/10/bar

    Routing.generate('route_id', {"id": 10, "foo":"bar"});
    // will result in /foo/10/bar?foo-bar

    $.get(Routing.generate('route_id', {"id": 10, "foo":"bar"}));
    // will call /foo/10/bar?foo-bar


Credits
-------

* William DURAND (Bazinga) as main author.
* Julien MUETTON (Carpe Hora) for the inspiration.
