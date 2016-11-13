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

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the ``app/AppKernel.php`` file of your project:

.. code-block:: php

    <?php
    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            );

            // ...
        }

        // ...
    }

Step 3: Register the Routes
---------------------------

Load the bundle's routing definition in the application (usually in the
``app/config/routing.yml`` file):

.. code-block:: yaml

    # app/config/routing.yml
    fos_js_routing:
        resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

Step 4: Publish the Assets
--------------------------

Execute the following command to publish the assets required by the bundle:

.. code-block:: bash

    # Symfony 2
    $ php app/console assets:install --symlink web

    # Symfony 3
    $ php bin/console assets:install --symlink web

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
