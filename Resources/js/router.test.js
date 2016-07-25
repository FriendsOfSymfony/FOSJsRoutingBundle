describe('Router', () => {
    let Router = require('./router');

    describe('generate', () => {

        it('generates a url', () => {
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

        it('generates a url with the given values for the respective parameters', () => {
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

        it('generates a url with the base url as prefix', () => {
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

        it('generates a url with the specified requirements', () => {
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

        it('generates a url for the specified host', () => {
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

        xit('', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "http"},
                    hosttokens: [['text', 'otherhost']]
                }
            });

            assertEquals('http://otherhost/foo/bar', router.generate('homepage'));
        });

        xit('', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "https"},
                    hosttokens: [['text', 'otherhost']]
                }
            });

            assertEquals('https://otherhost/foo/bar', router.generate('homepage'));
        });

        xit('', () => {
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

            assertEquals('http://api.localhost/foo/bar', router.generate('homepage', {subdomain: 'api'}));
        });

        xit('', () => {
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

            assertEquals('http://api.localhost/foo/bar', router.generate('homepage'));
        });

        xit('', () => {
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

            assertEquals('/foo/bar', router.generate('homepage', {subdomain: 'api'}));
        });

        xit('', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
        });

        xit('', () => {
            let router = new Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {"_scheme": "http"},
                    hosttokens: []
                }
            });

            assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
        });

        xit('', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [['variable', '.', '', '_format'], ['text', '/posts']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            assertEquals('/posts', router.generate('posts'));
            assertEquals('/posts.json', router.generate('posts', {'_format': 'json'}));
        });

        xit('', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [['variable', '/', '[1-9]+[0-9]*', 'page'], ['text', '/blog-posts']],
                    defaults: {'page' : 1},
                    requirements: {},
                    hosttokens: []
                }
            });

            assertEquals('/blog-posts?extra=1', router.generate('posts', {page: 1, extra: 1}));
        });

        xit('', () => {
            let router = new Router({base_url: ''}, {
                posts: {
                    tokens: [['variable', '/', '.+', 'id'], ['text', '/blog-post']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            assertEquals('/blog-post/foo/bar', router.generate('posts', {id: 'foo/bar'}));
        });

        xit('', () => {
            let router = new Router(undefined, {
                foo: {
                    tokens: [['variable', '/', '', 'bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            assertEquals('/baz?foo=bar', router.generate('foo', {
                bar: 'baz',
                foo: 'bar'
            }));
        });

        xit('', () => {
            let router = new Router(undefined, {
                foo: {
                    tokens: [['variable', '/', '', 'bar']],
                    defaults: {},
                    requirements: {},
                    hosttokens: []
                }
            });

            assertEquals('/baz?foo%5B%5D=1&foo%5B1%5D%5B%5D=1&foo%5B1%5D%5B%5D=2&foo%5B1%5D%5B%5D=3&foo%5B1%5D%5B%5D=foo&foo%5B%5D=3&foo%5B%5D=4&foo%5B%5D=bar&foo%5B5%5D%5B%5D=1&foo%5B5%5D%5B%5D=2&foo%5B5%5D%5B%5D=3&foo%5B5%5D%5B%5D=baz&baz%5Bfoo%5D=bar+foo&baz%5Bbar%5D=baz&bob=cat', router.generate('foo', {
                bar: 'baz', // valid param, not included in the query string
                foo: [1, [1, 2, 3, 'foo'], 3, 4, 'bar', [1, 2, 3, 'baz']],
                baz: {
                    foo : 'bar foo',
                    bar : 'baz'
                },
                bob: 'cat'
            }));
        });

        xit('', () => {
            let router = new Router({base_url: ''}, {
                foo: {
                    tokens: [['text', '/moo'], ['variable', '/', '', 'bar']],
                    defaults: {},
                    requirements: {}
                }
            });

            try {
                router.generate('foo');
                fail('generate() was expected to throw an error, but has not.');
            } catch (e) {
                assertEquals('The route "foo" requires the parameter "bar".', e.message);
            }
        });

        xit('', () => {
            let router = new Router({base_url: ''}, {});

            try {
                router.generate('foo');
                fail('generate() was expected to throw an error, but has not.');
            } catch (e) { }
        });

        xit('', () => {
            let router = new Router({base_url: '/foo'}, {
                homepage: {
                    tokens: [['text', '/bar']],
                    defaults: {},
                    requirements: {}
                }
            });

            assertEquals('/foo', router.getBaseUrl());
        });

        xit('', () => {
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

            assertEquals('/foo/bar', router.generate('homepage'));
            assertEquals('/foo/admin', router.generate('_admin'));

            router.setPrefix('es__RG__');
            assertEquals('/foo/es/bar', router.generate('homepage'));
        });

        xit('', () => {
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

            assertObjectEquals(expected, router.getRoute('blog_post'));
        });

        xit('', () => {
            let router = new Router({base_url: ''}, {
                blog_post: 'test',
                blog: 'test'
            });

            let expected = new goog.structs.Map({
                blog_post: 'test',
                blog: 'test'
            });

            assertObjectEquals(expected, router.getRoutes());
        });

        xit('', () => {
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

            assertEquals('/blog-post//10', router.generate('posts', { page: null, id: 10 }));
        });

    });

});

function testGenerateUsesHostWhenTheSameSchemeRequirementGiven() {

}

function testGenerateUsesHostWhenAnotherSchemeRequirementGiven() {

}

function testGenerateSupportsHostPlaceholders() {

}

function testGenerateSupportsHostPlaceholdersDefaults() {

}

function testGenerateGeneratesRelativePathWhenTheSameHostGiven() {

}

function testGenerateUsesAbsoluteUrl() {

}

function testGenerateUsesAbsoluteUrlWhenSchemeRequirementGiven() {

}

function testGenerateWithOptionalTrailingParam() {

}

function testGenerateQueryStringWithoutDefaults() {

}

function testAllowSlashes() {

}

function testGenerateWithExtraParams() {

}

function testGenerateWithExtraParamsDeep() {

}

function testGenerateThrowsErrorWhenRequiredParameterWasNotGiven() {

}

function testGenerateThrowsErrorForNonExistentRoute() {

}

function testGetBaseUrl() {

}

function testGeti18n() {

}

function testGetRoute() {

}

function testGetRoutes() {

}

function testGenerateWithNullValue() {

}
