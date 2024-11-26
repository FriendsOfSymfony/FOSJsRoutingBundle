Usage
=====

In applications not using webpack add these two lines in your layout:

**With Twig:**

.. code-block:: twig

    <script src="{{ asset('bundles/fosjsrouting/js/router.min.js') }}"></script>
    <script src="{{ path('fos_js_routing_js', { callback: 'fos.Router.setData' }) }}"></script>

**With PHP:**

.. code-block:: html+php

    <script src="<?php echo $view['assets']->getUrl('bundles/fosjsrouting/js/router.js') ?>"></script>
    <script src="<?php echo $view['router']->generate('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>

.. note::

    If you are not using Twig, then it is no problem. What you need is
    the two JavaScript files above loaded at some point in your web page.


If you are using webpack and Encore to package your assets you can use the webpack plugin included in this package

.. code-block:: js

    const FosRouting = require('fos-router/webpack/FosRouting');
    //...
    Encore
      .addPlugin(new FosRouting())

Then use it simply by importing ``import Routing from 'fos-router';`` in your js or ts code

The plugin hooks into the webpack `build` and `watch` process and triggers the `fos:js-routing:dump` command automatically, 
once routes have been changed.

To avoid that, e.g. when building the frontend on a machine or docker image/layer, where no PHP is present, you can configure the 
plugin to use a static dumped `routes.json` and suppress automatic recompilation of the file, by passing some options to the plugin:

.. code-block:: js
    
    const FosRouting = require('fos-router/webpack/FosRouting');
    //...
    Encore
        .addPlugin(new FosRouting(
            { target: './assets/js/routes.json' }, // <- path to dumped routes.json 
            false // <- set false to suppress automatic recompilation of the file
            )
        )

Alternatively you can use the dump command
and export your routes to json, this command will create a json file into the ``public/js`` folder:

.. code-block:: bash

    bin/console fos:js-routing:dump --format=json --target=assets/js/routes.json

If you are not using Flex, probably you want to dump your routes into the ``web`` folder
instead of ``public``, to achieve this you can set the ``target`` parameter:

.. code-block:: bash

    # Symfony Flex
    bin/console fos:js-routing:dump --format=json --target=web/js/fos_js_routes.json

When you dump the routes from a command, the Request Context (e.g. host and base_url) are not known. To generate correct routes for each environment with console commands, add the following (see `How to Generate URLs from the Console`):

.. code-block:: yaml

    # app/config/config_dev.yml (Symfony 3)
    parameters:
        router.request_context.host: "yourhost" 
        router.request_context.scheme: "http"
        router.request_context.base_url: "/app_dev.php" 

Then within your JavaScript development you can use:

.. code-block:: javascript

    const routes = require('../../public/js/fos_js_routes.json');
    import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

    Routing.setRoutingData(routes);
    Routing.generate('rep_log_list');


Generating URIs
---------------

It's as simple as calling:

.. code-block:: javascript

    Routing.generate('route_name', /* your params */)

Or if you want to generate **absolute URLs**:

.. code-block:: javascript

    Routing.generate('route_name', /* your params */, true)

Assuming some route definitions:

**With attributes:**

.. code-block:: php

    // src/AppBundle/Controller/DefaultController.php

    #[Route(path: '/foo/{id}/bar', name: 'my_route_to_expose', options: ['expose' => true])]
    public function indexAction($foo) {
        // ...
    }

    #[Route(path: '/blog/{page}', name: 'my_route_to_expose_with_defaults', options: ['expose' => true], defaults: ['page' => 1])]
    public function blogAction($page) {
        // ...
    }

**With YAML:**

.. code-block:: yaml

    # app/config/routing.yml
    my_route_to_expose:
        pattern: /foo/{id}/bar
        defaults: { _controller: AppBundle:Default:index }
        options:
            expose: true

    my_route_to_expose_with_defaults:
        pattern: /blog/{page}
        defaults: { _controller: AppBundle:Default:blog, page: 1 }
        options:
            expose: true

**With annotations (deprecated):**

.. code-block:: php

    // src/AppBundle/Controller/DefaultController.php

    /**
     * @Route("/foo/{id}/bar", options={"expose"=true}, name="my_route_to_expose")
     */
    public function indexAction($foo) {
        // ...
    }

    /**
     * @Route("/blog/{page}",
     *     defaults = { "page" = 1 },
     *     options = { "expose" = true },
     *     name = "my_route_to_expose_with_defaults",
     * )
     */
    public function blogAction($page) {
        // ...
    }




You can use the ``generate()`` method that way:

.. code-block:: javascript

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

Moreover, you can configure a list of routes to expose in ``app/config/config.yml``:

.. code-block:: yaml

    # app/config/config.yml
    fos_js_routing:
        routes_to_expose: [ route_1, route_2, ... ]

These routes will be added to the exposed routes. You can use regular expression
patterns if you don't want to list all your routes name by name.

.. note::

    If you're using `JMSI18nRoutingBundle`_ for your internationalized routes, your exposed routes must now match the bundle locale-prefixed routes, so you could either specify each locale by hand in the routes names, or use a regular expression to match all of your locales at once:

.. code-block:: yaml

    # app/config/config.yml
    fos_js_routing:
        routes_to_expose: [ en__RG__route_1, en__RG__route_2, ... ]

.. code-block:: yaml

    # app/config/config.yml
    fos_js_routing:
        routes_to_expose: [ '[a-z]{2}__RG__route_1', '[a-z]{2}__RG__route_2', ... ]

Note that `Symfony 4.1 added support for internationalized routes`_ out-of-the-box.

You can prevent to expose a route by configuring it as below:

.. code-block:: yaml

    # app/config/routing.yml
    my_very_secret_route:
        pattern: /admin
        defaults: { _controller: HelloBundle:Admin:index }
        options:
            expose: false

Router service
--------------

By default, this bundle exports routes from the default service `router`. You
can configure a different router service if needed:

.. code-block:: yaml

    # app/config/config.yml
    fos_js_routing:
        router: my_router_service

HTTP Caching
------------

You can enable HTTP caching as below:

.. code-block:: yaml

    # app/config/config.yml
    fos_js_routing:
        cache_control:
            # All are optional, defaults shown
            public: false   # can be true (public) or false (private)
            maxage: null    # integer value, e.g. 300
            smaxage: null   # integer value, e.g. 300
            expires: null   # anything that can be fed to "new \DateTime($expires)", e.g. "5 minutes"
            vary: []        # string or array, e.g. "Cookie" or [ Cookie, Accept ]

.. _`JMSI18nRoutingBundle`: https://github.com/schmittjoh/JMSI18nRoutingBundle
.. _`Symfony 4.1 added support for internationalized routes`: https://symfony.com/blog/new-in-symfony-4-1-internationalized-routing
