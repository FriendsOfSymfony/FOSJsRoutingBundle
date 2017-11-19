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
$ cd Resources
$ npm install google-closure-library phantomjs
```

Run the JS test suite with:

```bash
$ phantomjs js/run_jsunit.js js/router_test.html
```

Alternatively you can open `Resources/js/router_test.html` in your browser.

Compiling the JavaScript files
------------------------------

> **NOTE**
>
> We already provide a compiled version of the JavaScript; this section is only
> relevant if you want to make changes to this script.

This project is using [Gulp](https://gulpjs.com/) to compile JavaScript files.
In order to use Gulp you must install both [node](https://nodejs.org/en/) and 
[npm](https://www.npmjs.com/). 

If you are not familiar with using Gulp, it is recommended that you review this 
[An Introduction to Gulp.js](https://www.sitepoint.com/introduction-gulp-js/)
tutorial which will guide you through the process of getting node and npm installed.

Once you have node and npm installed:

```bash
$ cd Resources
$ npm install
```

Then to perform a build

```bash
$ npm run build
```
