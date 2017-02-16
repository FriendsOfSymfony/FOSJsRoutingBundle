'use strict';

/**
 * @fileoverview This file is the entry point for the Router tests.
 *
 * You can run these tests by running the following command from the Resources folder:
 *
 *    npm test
 */

import polyfill from 'babel-polyfill';
import { Router } from '..';

describe(Router.prototype.constructor.name, () => {

    describe('baseUrl()', () => {

        it('gets the base url', () => {
            let router = new Router({base_url: '/foo'}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {}
                }
            });

            expect(router.getBaseUrl()).toBe('/foo');
        });

    });

    describe('generate()', () => {

        it('generates a path', () => {
            let router = new Router({base_url: ''}, {
                literal: {
                    tokens: [['text', '/homepage']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('literal')).toBe('/homepage');
        });

        it('generates a path with the given values for the respective parameters', () => {
            let router = new Router({base_url: ''}, {
                blog_post: {
                    tokens: [['variable', '/', '[^/]+?', 'slug'], ['text', '/blog-post']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('blog_post', {slug: 'foo'})).toBe('/blog-post/foo');
        });

        it('generates a path with the base url as prefix', () => {
            let router = new Router({base_url: '/foo'}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('homepage')).toBe('/foo/bar');
        });

        it('generates a path with the specified requirements', () => {
            let router = new Router({base_url: '/foo', host: "localhost"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "https"},
                    hosttokens: []
                }
            });

            expect(router.generate('homepage')).toBe('https://localhost/foo/bar');
        });

        it('generates a path for the specified host', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: [['text', 'otherhost']]
                }
            });

            expect(router.generate('homepage')).toBe('http://otherhost/foo/bar');
        });

        it('uses host when the same scheme requirement is given', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "http"},
                    hosttokens: [['text', 'otherhost']]
                }
            });

            expect(router.generate('homepage')).toBe('http://otherhost/foo/bar');
        });

        it('uses host when another scheme requirement is given', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "https"},
                    hosttokens: [['text', 'otherhost']]
                }
            });

            expect(router.generate('homepage')).toBe('https://otherhost/foo/bar');
        });

        it('supports host placeholders', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: [
                        ['text', '.localhost'],
                        ['variable', '', '', 'subdomain']
                    ]
                }
            });

            expect(router.generate('homepage', {subdomain: 'api'})).toBe('http://api.localhost/foo/bar');
        });

        it('supports host placeholders defaults', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {subdomain: 'api'},
                    requirements: {},
                    hosttokens: [
                        ['text', '.localhost'],
                        ['variable', '', '', 'subdomain']
                    ]
                }
            });

            expect(router.generate('homepage')).toBe('http://api.localhost/foo/bar');
        });

        it('generates a relative path when the same host is given', () => {
            let router = new Router({base_url: '/foo', host: "api.localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: [
                        ['text', '.localhost'],
                        ['variable', '', '', 'subdomain']
                    ]
                }
            });

            expect(router.generate('homepage', {subdomain: 'api'})).toBe('/foo/bar');
        });

        it('generates absolute path when scheme is given', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('homepage', [], true)).toBe('http://localhost/foo/bar');
        });

        it('generates absolute path when scheme requirement given', () => {
            let router = new Router({base_url: '/foo', host: "localhost"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "http"},
                    hosttokens: []
                }
            });

            expect(router.generate('homepage', [], true)).toBe('http://localhost/foo/bar');
        });

        it('supports optional trailing parameters', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [['variable', '.', '', '_format'], ['text', '/posts']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('posts')).toBe('/posts');
            expect(router.generate('posts', {'_format': 'json'})).toBe('/posts.json');
        });

        it('ignores parameters when the given value is the default value', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [['variable', '/', '[1-9]+[0-9]*', 'page'], ['text', '/blog-posts']],
                    defaults: {'page' : 1},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('posts', {page: 1, extra: 1})).toBe('/blog-posts?extra=1');
        });

        it('allows slashes in the parameters', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [['variable', '/', '.+', 'id'], ['text', '/blog-post']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('posts', {id: 'foo/bar'})).toBe('/blog-post/foo/bar');
        });

        it('adds unrecognised parameters to the search query', () => {
            let router = new Router(undefined, {
                foo: {
                    tokens: [['variable', '/', '', 'bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('foo', {
                bar: 'baz',
                foo: 'bar'
            })).toBe('/baz?foo=bar');
        });

        it('supports nested search parameters', () => {
            let router = new Router(undefined, {
                foo: {
                    tokens: [['variable', '/', '', 'bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('foo', {
                bar: 'baz', // valid param, not included in the query string
                foo: [1, [1, 2, 3, 'foo'], 3, 4, 'bar', [1, 2, 3, 'baz']],
                baz: {
                    foo : 'bar foo',
                    bar : 'baz'
                },
                bob: 'cat'
            })).toBe('/baz?foo%5B%5D=1&foo%5B1%5D%5B%5D=1&foo%5B1%5D%5B%5D=2&foo%5B1%5D%5B%5D=3&foo%5B1%5D%5B%5D=foo&foo%5B%5D=3&foo%5B%5D=4&foo%5B%5D=bar&foo%5B5%5D%5B%5D=1&foo%5B5%5D%5B%5D=2&foo%5B5%5D%5B%5D=3&foo%5B5%5D%5B%5D=baz&baz%5Bfoo%5D=bar+foo&baz%5Bbar%5D=baz&bob=cat');
        });

        it('supports i18n', () => {
            let router = new Router({base_url: '/foo', prefix: 'en__RG__'}, {
                en__RG__homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                },
                es__RG__homepage: {
                    tokens: [['text', '/es/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                },
                _admin: {
                    tokens: [['text', '/admin']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('homepage')).toBe('/foo/bar');
            expect(router.generate('_admin')).toBe('/foo/admin');

            router.setPrefix('es__RG__');
            expect(router.generate('homepage')).toBe('/foo/es/bar');
        });

        it('treats null values as empty', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [
                        ['variable', '/', '.+', 'id'],
                        ['variable', '/', '.+', 'page'],
                        ['text', '/blog-post']
                    ],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            expect(router.generate('posts', { page: null, id: 10 })).toBe('/blog-post//10');
        });

        it('throws error when require parameter is not given', () => {
            let router = new Router({base_url: ''}, {
                foo: {
                    tokens: [['text', '/moo'], ['variable', '/', '', 'bar']],
                    defaults: {},
                    requirements: {}
                }
            });

            expect(() => router.generate('foo')).toThrowError('The route "foo" requires the parameter "bar".');
        });

        it('throws error for non existing route', () => {
            let router = new Router({base_url: ''}, {});

            expect(() => router.generate('foo')).toThrowError();
        });

    });

    describe('getRoute()', () => {

        it('gets the specified route', () => {
            let router = new Router({base_url: ''}, {
                blog_post: {
                    tokens: [['variable', '/', '[^/]+?', 'slug'], ['text', '/blog-post']],
                    defaults: {},
                    requirements: {"_scheme": "http"}
                }
            });

            let expected = {
                'defaults': {},
                'tokens' : [
                    ['variable', '/', '[^/]+?', 'slug'],
                    ['text', '/blog-post']
                ],
                'requirements': {"_scheme": "http"}
            };

            expect(router.getRoute('blog_post')).toEqual(expected);
        });

    });

    describe('getRoutes()', () => {

        it('gets the list of routes', () => {
            let router = new Router({base_url: ''}, {
                blog_post: 'test',
                blog: 'test'
            });

            let expected = {
                blog_post: 'test',
                blog: 'test'
            };

            expect(router.getRoutes()).toEqual(expected);
        });

    });

});
