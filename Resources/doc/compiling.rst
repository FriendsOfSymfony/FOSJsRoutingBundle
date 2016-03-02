Compiling the JavaScript files
==============================

.. note::

    We already provide a compiled version of the JavaScript; this section is only
    relevant if you want to make changes to this script.

In order to re-compile the JavaScript source files that we ship with this
bundle, you need the Google Closure Tools. You need the `plovr`_ tool, which is
a Java ARchive, so you also need a working Java environment. You can re-compile
the JavaScript with the following command:

.. code-block:: bash

    $ java -jar plovr.jar build Resources/config/plovr/compile.js

Alternatively, you can use the JMSGoogleClosureBundle. If you install this
bundle, you can re-compile the JavaScript with the following command:

.. code-block:: bash

    $ php app/console plovr:build @FOSJsRoutingBundle/compile.js

.. _`plovr`: http://plovr.com/download.html
