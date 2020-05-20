# Changelog

## v2.6.0 - 2020-05-20
- [BC break] Fix URL encoding to mimic Symfony URL Generator (this might change behavior for special characters, it should be in line with Symfony Router though) ([#387](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/387))
- Fixed issue with creating absolute instead of relative path on hosts with differing ports ([#391](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/391))

## v2.5.4 - 2020-04-15
- Fix duplicated port in absolute path ([#381](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/381))

## v2.5.3 - 2020-01-13
- Rervert fall back to current domain when baseurl is missing or empty in json ([#374](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/374))

## v2.5.2 - 2020-01-12
- Fall back to current domain when baseurl is missing or empty in json ([#371](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/371))
- Upgrade gulp to version 4 ([#372](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/372))

## v2.5.1 - 2019-12-02
- [BC break] Fix root dir deprecation and fix PHP 7.4 deprecation (drops Symfony < 3.3 support) ([#369](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/369))

## v2.5.0 - 2019-12-01
- [BC break] Add support for Symfony 5, drop support for PHP5, drop support for Symfony 2 ([#366](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/366))
- Fix absolute url generation including ports ([#361](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/361))
- Fix cache for exposed routes in debug mode ([#362](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/362))

## v2.4.0 - 2019-08-10
- Add Symfony 4.1 localized routes support ([#334](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/334))
- Add documentation remarks on JMSI18nRoutingBundle compatibility ([#352](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/352))

## v2.3.1 - 2019-06-17
- Fix regex pattern to match whole url pattern ([#350](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/350))
- Small documentation update

## v2.3.0 - 2019-02-03
- Add routing-sf4.xml to move towards Symfony >4.1 syntax
- Add functionality to granularly expose routes based on domains ([#346](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/issues/346))
- Small cleanup and textual fix

## v2.2.2 - 2018-11-28
- Fix Symfony 4.2 deprecation
- Add setRoutingData to typescript definition

## v2.2.1 - 2018-09-29
- Add support for a different port

## v2.2.0 - 2018-02-07
- Refactor JavaScript code to improve webpack compatibility

## v2.1.1 - 2017-12-13
- Fix SF <4 compatibility ([#306](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/issues/306))

## v2.1.0 - 2017-12-13
- Add Symfony 4 compatibility ([#300](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/300))
- Add JSON dump functionality ([#302](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/302))
- Fix bug denormalizing empty routing collections from cache
- Update documentation for Symfony 3 ([#273](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/273))

## v2.0.0 - 2017-11-08
- Add Symfony 3.* compatibility
- Added `--pretty-print` option to `fos:js-routing:dump`-command, making the resulting javascript pretty-printed
- Removed SF 2.1 backwards compatibility code
- Add automatic injection of `locale` parameter
- Added functionality to change the used router service
- Added normalizer classes
