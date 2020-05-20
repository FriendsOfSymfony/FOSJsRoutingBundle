goog.require('goog.testing.jsunit');
goog.require('goog.structs.Map');

function testGenerate() {
    var router = new fos.Router({base_url: ''}, {
        literal: {
            tokens: [['text', '/homepage']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/homepage', router.generate('literal'));
}

function testGenerateWithParams() {
    var router = new fos.Router({base_url: ''}, {
        blog_post: {
            tokens: [['variable', '/', '[^/]+?', 'slug'], ['text', '/blog-post']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/blog-post/foo', router.generate('blog_post', {slug: 'foo'}));
}

function testGenerateUsesBaseUrl() {
    var router = new fos.Router({base_url: '/foo'}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/foo/bar', router.generate('homepage'));
}

function testGenerateUsesSchemeRequirements() {
    var router = new fos.Router({base_url: '/foo', host: "localhost"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "https"},
            hosttokens: []
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
            hosttokens: [['text', 'otherhost']]
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
            hosttokens: [['text', 'otherhost']]
        }
    });

    assertEquals('http://otherhost/foo/bar', router.generate('homepage'));
}

function testGenerateUsesHostWhenTheSameSchemeGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: [['text', 'otherhost']],
            schemes: ['http'],
            methods: []
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
            hosttokens: [['text', 'otherhost']]
        }
    });

    assertEquals('https://otherhost/foo/bar', router.generate('homepage'));
}

function testGenerateUsesHostWhenAnotherSchemeGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: [['text', 'otherhost']],
            schemes: ['https'],
            methods: []
        }
    });

    assertEquals('https://otherhost/foo/bar', router.generate('homepage'));
}

function testGenerateSupportsHostPlaceholders() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
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
}

function testGenerateSupportsHostPlaceholdersDefaults() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
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
}

function testGenerateGeneratesRelativePathWhenTheSameHostGiven() {
    var router = new fos.Router({base_url: '/foo', host: "api.localhost", scheme: "http"}, {
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
}

function testGenerateUsesAbsoluteUrl() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWithGivenPort() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http", port: "8000"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('http://localhost:8000/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWithGivenPortAndHostWithPort() {
    var router = new fos.Router({base_url: '/foo', host: "localhost:8080", scheme: "http", port: "8080"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('http://localhost:8080/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWhenSchemeRequirementGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "http"},
            hosttokens: []
        }
    });

    assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWithGivenPortWhenSchemeRequirementGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http", port: "8080"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "http"},
            hosttokens: []
        }
    });

    assertEquals('http://localhost:8080/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWithGivenPortWhenSchemeRequirementAndHostWithPortGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost:8080", scheme: "http", port: "8080"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {"_scheme": "http"},
            hosttokens: []
        }
    });

    assertEquals('http://localhost:8080/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWhenSchemeGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: [],
            schemes: ['http'],
            methods: []
        }
    });

    assertEquals('http://localhost/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWithGivenPortWhenSchemeGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http", port:"1234"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: [],
            schemes: ['http'],
            methods: []
        }
    });

    assertEquals('http://localhost:1234/foo/bar', router.generate('homepage', [], true));
}

function testGenerateUsesAbsoluteUrlWithGivenPortWhenSchemeAndHostWithPortGiven() {
    var router = new fos.Router({base_url: '/foo', host: "localhost:8080", scheme: "http", port:"8080"}, {
        homepage: {
            tokens: [['text', '/bar']],
            defaults: {},
            requirements: {},
            hosttokens: [],
            schemes: ['http'],
            methods: []
        }
    });

    assertEquals('http://localhost:8080/foo/bar', router.generate('homepage', [], true));
}

function testGenerateWithOptionalTrailingParam() {
    var router = new fos.Router({base_url: ''}, {
        posts: {
            tokens: [['variable', '.', '', '_format'], ['text', '/posts']],
            defaults: {},
            requirements: {},
            hosttokens: []
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
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/blog-posts?extra=1', router.generate('posts', {page: 1, extra: 1}));
}

function testAllowSlashes() {
    var router = new fos.Router({base_url: ''}, {
        posts: {
            tokens: [['variable', '/', '.+', 'id'], ['text', '/blog-post']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/blog-post/foo/bar', router.generate('posts', {id: 'foo/bar'}));
}

function testGenerateWithExtraParams() {
    var router = new fos.Router(undefined, {
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
}

function testGenerateWithExtraParamsDeep() {
    var router = new fos.Router(undefined, {
        foo: {
            tokens: [['variable', '/', '', 'bar']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/baz?foo%5B%5D=1&foo%5B1%5D%5B%5D=1&foo%5B1%5D%5B%5D=2&foo%5B1%5D%5B%5D=3&foo%5B1%5D%5B%5D=foo&foo%5B%5D=3&foo%5B%5D=4&foo%5B%5D=bar&foo%5B5%5D%5B%5D=1&foo%5B5%5D%5B%5D=2&foo%5B5%5D%5B%5D=3&foo%5B5%5D%5B%5D=baz&baz%5Bfoo%5D=bar%20foo&baz%5Bbar%5D=baz&bob=cat', router.generate('foo', {
        bar: 'baz', // valid param, not included in the query string
        foo: [1, [1, 2, 3, 'foo'], 3, 4, 'bar', [1, 2, 3, 'baz']],
        baz: {
            foo : 'bar foo',
            bar : 'baz'
        },
        bob: 'cat'
    }));
}

function testUrlEncoding() {
    // This test was copied from Symfony URL Generator

    // This tests the encoding of reserved characters that are used for delimiting of URI components (defined in RFC 3986)
    // and other special ASCII chars. These chars are tested as static text path, variable path and query param.
    var chars = '@:[]/()*\'" +,;-._~&$<>|{}%\\^`!?foo=bar#id';

    var router = new fos.Router({base_url: '/app.php'}, {
        posts: {
            tokens: [['variable', '/', '.+', 'varpath'], ['text', '/'+chars]],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals(
        '/app.php/@:%5B%5D/%28%29*%27%22%20+,;-._~%26%24%3C%3E|%7B%7D%25%5C%5E%60!%3Ffoo=bar%23id/@:%5B%5D/%28%29*%27%22%20+,;-._~%26%24%3C%3E|%7B%7D%25%5C%5E%60!%3Ffoo=bar%23id?query=@:%5B%5D/%28%29*%27%22%20%2B,;-._~%26%24%3C%3E%7C%7B%7D%25%5C%5E%60!?foo%3Dbar%23id',
        router.generate('posts', {varpath: chars, query: chars})
    );
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
    var router = new fos.Router({base_url: '/foo', prefix: 'en__RG__', locale: 'en'}, {
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
        },
        "login.en": {
            tokens: [['text', '/en/login']],
            defaults: {},
            requirements: {},
            hosttokens: []
        },
        "login.es": {
            tokens: [['text', '/es/login']],
            defaults: {},
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/foo/bar', router.generate('homepage'));
    assertEquals('/foo/admin', router.generate('_admin'));
    assertEquals('/foo/en/login', router.generate('login'));

    router.setPrefix('es__RG__');
    router.setLocale('es');
    assertEquals('/foo/es/bar', router.generate('homepage'));
    assertEquals('/foo/es/login', router.generate('login'));
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

function testGetRoutes() {
    var router = new fos.Router({base_url: ''}, {
        blog_post: 'test',
        blog: 'test'
    });

    var expected = new goog.structs.Map({
        blog_post: 'test',
        blog: 'test'
    });

    assertObjectEquals(expected.toObject(), router.getRoutes());
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
            requirements: {},
            hosttokens: []
        }
    });

    assertEquals('/blog-post//10', router.generate('posts', { page: null, id: 10 }));
}

function testGenerateWithPort() {
  var router = new fos.Router({base_url: '/foo', host: "localhost", scheme: "http", port: 443}, {
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

  assertEquals('http://api.localhost:443/foo/bar', router.generate('homepage'));
}

// Regression test for issue #384 (https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/issues/384)
function testGenerateWithPortInHost() {
  var router = new fos.Router({base_url: '', host: "my-host.loc:81", scheme: "http", port: 81}, {
    homepage: {
      tokens: [['text', "\/foo\/"]],
      defaults: [],
      requirements: {},
      hosttokens: [["text", "my-host.loc"]],
      methods: ["GET", "POST"],
    }
  });

  assertEquals('/foo/', router.generate('homepage'));
}