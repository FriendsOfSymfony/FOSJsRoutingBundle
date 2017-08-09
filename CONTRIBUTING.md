Contributing
============

First of all, **thank you** for contributing, **you are awesome**!

Here are a few rules to follow in order to ease code reviews, and discussions
before maintainers accept and merge your work:

 * You MUST follow the [PSR-1](http://www.php-fig.org/psr/1/) and
   [PSR-2](http://www.php-fig.org/psr/2/) recommendations. Use the [PHP-CS-Fixer
   tool](http://cs.sensiolabs.org/) to fix the syntax of your code automatically.
 * You MUST run the test suite.
 * You MUST write (or update) unit tests.
 * You SHOULD write documentation.

Please, write [commit messages that make
sense](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html),
and [rebase your branch](http://git-scm.com/book/en/Git-Branching-Rebasing)
before submitting your Pull Request.

One may ask you to [squash your
commits](http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html)
too. This is used to "clean" your Pull Request before merging it (we don't want
commits such as `fix tests`, `fix 2`, `fix 3`, etc.).

Also, while creating your Pull Request on GitHub, you MUST write a description
which gives the context and/or explains why you are creating it.

Thank you!

Running tests
-------------

Before running the test suite, execute the following Composer command to install
the dependencies used by the bundle:

```bash
$ composer update
```

Then, execute the tests executing:

```bash
$ ./phpunit
```

### JavaScript Test Suite

First, install [PhantomJS](http://phantomjs.org/) and [Google Closure
Library](https://github.com/google/closure-library):

```bash
$ npm install google-closure-library
```

Run the JS test suite with:

```bash
$ phantomjs Resources/js/run_jsunit.js Resources/js/router_test.html
```

Compiling the JavaScript files
------------------------------

> **NOTE**
>
> We already provide a compiled version of the JavaScript; this section is only
> relevant if you want to make changes to this script.

In order to re-compile the JavaScript source files that we ship with this
bundle, you need the Google Closure Tools. You need the
[plovr](http://plovr.com/download.html) tool, which is a Java ARchive, so you
also need a working Java environment. You can re-compile the JavaScript with the
following command:

```bash
$ java -jar plovr.jar build Resources/config/plovr/compile.js
```

Alternatively, you can use the JMSGoogleClosureBundle. If you install this
bundle, you can re-compile the JavaScript with the following command:

```bash
$ php app/console plovr:build @FOSJsRoutingBundle/compile.js
```
