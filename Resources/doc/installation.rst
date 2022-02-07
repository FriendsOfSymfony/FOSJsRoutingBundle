Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require friendsofsymfony/jsrouting-bundle

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

If you're using Symfony Flex, you can ignore the following steps as they will be executed automatically.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the ``config/bundles.php`` file of your project:

.. code-block:: php

    <?php
    // config/bundles.php

    // ...
    return [
        // ...

        FOS\JsRoutingBundle\FOSJsRoutingBundle::class => ['all' => true],
        // ...
    ];

Step 3: Register the Routes
---------------------------

Load the bundle's routing definition in the application:

.. code-block:: yaml

    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing-sf4.xml"

Step 4: Publish the Assets
--------------------------

Execute the following command to publish the assets required by the bundle:

.. code-block:: bash

    $ php bin/console assets:install --symlink public

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md

Step 5: If you are using webpack, install the npm package locally
-----------------------------------------------------------------
.. code-block:: bash

    $ yarn add -D ./vendor/friendsofsymfony/jsrouting-bundle/Resources/
