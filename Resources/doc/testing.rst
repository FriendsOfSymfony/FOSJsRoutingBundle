Testing
=======

Before running the test suite, execute the following Composer command to install
the dependencies used by the bundle:

.. code-block:: bash

    $ composer install --dev

Then, execute the tests executing:

.. code-block:: bash

    $ phpunit

JavaScript Test Suite
---------------------

First, install `PhantomJS`_ and then, execute this command:

.. code-block:: bash

    $ phantomjs Resources/js/run_jsunit.js Resources/js/router_test.html

.. _`PhantomJS`: http://phantomjs.org/
