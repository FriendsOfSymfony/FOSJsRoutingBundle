Commands
========

fos:js-routing:dump
-------------------

This command dumps the route information into a file so that instead of having
the controller generated JavaScript, you can use a normal file. This also allows
to combine the routes with the other JavaScript files in assetic.

.. code-block:: bash

    # Symfony 2
    $ php app/console fos:js-routing:dump
    
    # Symfony 3
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
    in the console: `Configuring The Request Context Globally`_.

.. tip::

    If you are using JMSI18nRoutingBundle, you need to run the command with the
    ``--locale`` parameter once for each locale you use and adjust your include
    paths accordingly.

fos:js-routing:debug
--------------------

This command lists all exposed routes:

.. code-block:: bash

    # Symfony 2
    $ php app/console fos:js-routing:debug [name]
    
    # Symfony 3
    $ php bin/console fos:js-routing:debug [name]

.. _`Configuring The Request Context Globally`: http://symfony.com/doc/current/cookbook/console/sending_emails.html#configuring-the-request-context-globally
