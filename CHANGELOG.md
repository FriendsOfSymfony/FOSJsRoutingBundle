# Changelog

## v3.5.0 - 2024-01-23
- Fix TypeScript error when verbatimModuleSyntax is enabled ([#476](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/476))
- Define RoutesResponse as a Service ([#474](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/474))
- Ignore session in stateless requests ([#468](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/468))
- Add option to skip registering compile hooks ([#462](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/462))

## v3.4.1 - 2023-12-15
- fix: do not use BannerPlugin but newer webpack-inject-plugin instead to fix vulnerability

## v3.4.0 - 2023-12-12
- Allow Symfony 7.0 ([#471](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/471))
- fix: remove webpack-inject-plugin dependency ([#464](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/464))
- Docs only: remove $ so gitclip works ([#472](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/472))
- Docs only: Update console note ([#463](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/463))

## v3.3.0 - 2023-07-04
- add support for Windows when using the webpack plugin ([#444](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/444))
- Add PHP 8.2 tests ([#449](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/449))
- Phpunit config file migration ([#450](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/450)))
- Deprecation fixes (PHP 8 ([#451](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/451)) and Symfony 6.3 ([#460](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/460)))
- JSON Callback validator static call instead of new object ([#458](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/458))
- Optimize package size by excluding tests ([#457](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/457))

## v3.2.1 - 2022-07-01
- fix for webpack plugin: fosRoute.json dir created at root ([#443](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/443))

## v3.2.0 - 2022-06-30
- [BC break] Use Symfony Flex default path. Will break if you're still using the `web` directory and not defining the path ([#433](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/433))
- Add webpack plugin to automatically load the routes with no user interactions ([#429](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/429))
- Changed ExposedRoutesExtractor to handle mkdir warnings ([#434](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/434))
- Handle nullable route defaults ([#436](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/436))
- Fix Symfony 6.1 deprecations ([#439](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/439))

## v3.1.1 - 2022-03-02
- Allow willdurand/jsonp-callback-validator v2 ([#430](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/issues/430))
- Use latest PHP 8.0 features ([#432](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/issues/432))

## v3.1.0 - 2021-12-28
- Improve documentation for dump command when using locales ([#426](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/426))
- Add support for explicit default inclusion ([#423](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/issues/423))

## v3.0.0 - 2021-12-15
- Migrate router implementation to TS ([#406](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/406))
- Allow Symfony 6 ([#408](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/408))
- [BC break] Drop support for PHP <8.0 and Symfony <5.4, add typing to all classes
- Add documentation for attributes

## v2.8.0 - 2021-12-15
- Fix expose: false behavior ([#404](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/404))
- Fix dump using domains ([#410](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/410))
- Fix docs links ([#412](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/412))
- Replace Travis with Github actions ([#414](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/414))

## v2.7.0 - 2020-11-22
- Add support for PHP 8 ([#399](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/399))

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
