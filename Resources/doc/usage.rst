Usage
=====

In applications not using webpack add these two lines in your layout:

.. configuration-block::

    .. code-block:: html+twig

        <script src="{{ asset('bundles/fosjsrouting/js/router.min.js') }}"></script>
        <script src="{{ path('fos_js_routing_js', { callback: 'fos.Router.setData' }) }}"></script>

    .. code-block:: html+php

        <script src="<?php echo $view['assets']->getUrl('bundles/fosjsrouting/js/router.js') ?>"></script>
        <script src="<?php echo $view['router']->generate('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>

.. note::

    If you are not using Twig, then it is no problem. What you need is
    the two JavaScript files above loaded at some point in your web page.


If you are using webpack and Encore to package your assets you will need to use the dump command
and export your routes to json, this command will create a json file into the ``web/js`` folder:

.. code-block:: bash

    # Symfony 3
    bin/console fos:js-routing:dump --format=json

If you are using Flex, probably you want to dump your routes into the ``public`` folder
instead of ``web``, to achieve this you can set the ``target`` parameter:

.. code-block:: bash

    # Symfony Flex
    bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

Then within your JavaScript development you can use:

.. code-block:: javascript

    const routes = require('../../web/js/fos_js_routes.json');
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

.. configuration-block::

    .. code-block:: php-annotations

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
