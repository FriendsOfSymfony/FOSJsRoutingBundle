Commands
========

fos:js-routing:dump
-------------------

This command dumps the route information into a file so that instead of having
the controller generated JavaScript, you can use a normal file. This also allows
to combine the routes with the other JavaScript files in assetic.

.. code-block:: bash

    $ php bin/console fos:js-routing:dump

Instead of the line

.. code-block:: twig

    <script src="{{ path('fos_js_routing_js', {"callback": "fos.Router.setData"}) }}"></script>

you now include this as

.. code-block:: html

    <script src="/js/fos_js_routes.js"></script>

Or inside assetic, do

.. code-block:: twig

    {% javascripts filter='?yui_js'
        'bundles/fosjsrouting/js/router.js'
        'js/fos_js_routes.js'
    %}
        <script src="{{ asset_url }}"></script>
    {% endjavascripts %}

.. caution::

    You should follow the Symfony documentation about generating URLs
    in the console: `Forcing HTTPS on Generated URLs`_, as the console is unaware
    of the host/port combination you use during a request. You can also set the
    `HTTP_HOST` environment variable to hold your hostname including the port you
    use (i.e. `localhost:8443`). You can also use the `setHost` and `setPort`
    methods on the `Router` object to set it at runtime.

.. tip::

    If you are using JMSI18nRoutingBundle, you need to run the command with the
    ``--locale`` parameter and a custom ``--target`` once for each locale you use.
    Then adjust your include path accordingly. Note that you can only load the dump
    of one locale at once in your html as each following dump would overwrite the
    data of the previous one.

fos:js-routing:debug
--------------------

This command lists all exposed routes:

.. code-block:: bash

    # Symfony 3
    $ php bin/console fos:js-routing:debug [name]

.. _`Forcing HTTPS on Generated URLs`: https://symfony.com/doc/current/routing.html#forcing-https-on-generated-urls
