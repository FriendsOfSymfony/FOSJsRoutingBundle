goog.require('goog.testing.jsunit');

function testGenerate() {
    var router = new fos.Router({base_url: ''}, {
        literal: {
            tokens: [['text', '/homepage']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/homepage', router.generate('literal'));
}

function testGenerateWithParams() {
    var router = new fos.Router({base_url: ''}, {
        blog_post: {
            tokens: [['variable', '/', '[^/]+?', 'slug'], ['text', '/blog-post']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/blog-post/foo', router.generate('blog_post', {slug: 'foo'}));
}

function testGenerateUsesBaseUrl() {
    var router = new fos.Router({base_url: '/foo'}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/foo/bar', router.generate('homepage'));
}

function testGenerateUsesSchemeRequirements() {
    var router = new fos.Router({base_url: '/foo', host: "localhost"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "https"}
        }
    });

    assertEquals('https://localhost/foo/bar', router.generate('homepage'));
}

function testGenerateUsesHost() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            host: 'otherhost'
        }
    });

    assertEquals('http://otherhost/foo/bar', router.generate('homepage'));
}

function testGenerateUsesHostWhenTheSameSchemeRequirementGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "http"},
            host: 'otherhost'
        }
    });

    assertEquals('http://otherhost/foo/bar', router.generate('homepage'));
}

function testGenerateUsesHostWhenAnotherSchemeRequirementGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "https"},
            host: 'otherhost'
        }
    });

    assertEquals('https://otherhost/foo/bar', router.generate('homepage'));
}

function testGenerateUsesAbsoluteUrl() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWhenSchemeRequirementGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "http"}
        }
    });

    assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
}

function testGenerateWithOptionalTrailingParam() {
    var router = new fos.Router({base_url: ''}, {
        posts: {
            tokens: [['variable', '.', '', '_format'], ['text', '/posts']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/posts', router.generate('posts'));
    assertEquals('/posts.json', router.generate('posts', {'_format': 'json'}));
}

function testGenerateQueryStringWithoutDefaults() {
    var router = new fos.Router({base_url: ''}, {
        posts: {
            tokens: [['variable', '/', '[1-9]+[0-9]*', 'page'], ['text', '/blog-posts']],
            defaults: {'page' : 1},
            requirements: {}
        }
    });

    assertEquals('/blog-posts?extra=1', router.generate('posts', {page: 1, extra: 1}));
}

function testAllowSlashes() {
    var router = new fos.Router({base_url: ''}, {
        posts: {
            tokens: [['variable', '/', '.+', 'id'], ['text', '/blog-post']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/blog-post/foo/bar', router.generate('posts', {id: 'foo/bar'}));
}

function testGenerateWithExtraParams() {
    var router = new fos.Router(undefined, {
        foo: {
            tokens: [['variable', '/', '', 'bar']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/baz?foo=bar', router.generate('foo', {
        bar: 'baz',
        foo: 'bar'
    }));
}

function testGenerateWithExtraParamsDeep() {
    var router = new fos.Router(undefined, {
        foo: {
            tokens: [['variable', '/', '', 'bar']],
            defaults: {},
            requirements: {}
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
}

function testGenerateThrowsErrorWhenRequiredParameterWasNotGiven() {
    var router = new fos.Router({base_url: ''}, {
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
}

function testGenerateThrowsErrorForNonExistentRoute() {
    var router = new fos.Router({base_url: ''}, {});

    try {
        router.generate('foo');
        fail('generate() was expected to throw an error, but has not.');
    } catch (e) { }
}

function testGetBaseUrl() {
    var router = new fos.Router({base_url: '/foo'}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/foo', router.getBaseUrl());
}

function testGeti18n() {
    var router = new fos.Router({base_url: '/foo', prefix: 'en__RG__'}, {
        en__RG__homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {}
        },
        es__RG__homepage: {
            tokens: [['text', '/es/bar']],
            defaults: {},
            requirements: {}
        },
        _admin: {
            tokens: [['text', '/admin']],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/foo/bar', router.generate('homepage'));
    assertEquals('/foo/admin', router.generate('_admin'));

    router.setPrefix('es__RG__');
    assertEquals('/foo/es/bar', router.generate('homepage'));
}

function testGetRoute() {
    var router = new fos.Router({base_url: ''}, {
        blog_post: {
            tokens: [['variable', '/', '[^/]+?', 'slug'], ['text', '/blog-post']],
            defaults: {},
            requirements: {"_scheme": "http"}
        }
    });

    var expected = {
        'defaults': {},
        'tokens' : [
            ['variable', '/', '[^/]+?', 'slug'],
            ['text', '/blog-post']
        ],
        'requirements': {"_scheme": "http"}
    };

    assertObjectEquals(expected, router.getRoute('blog_post'));
}

function testGenerateWithNullValue() {
    var router = new fos.Router({base_url: ''}, {
        posts: {
            tokens: [
                ['variable', '/', '.+', 'id'],
                ['variable', '/', '.+', 'page'],
                ['text', '/blog-post']
            ],
            defaults: {},
            requirements: {}
        }
    });

    assertEquals('/blog-post//10', router.generate('posts', { page: null, id: 10 }));
}
