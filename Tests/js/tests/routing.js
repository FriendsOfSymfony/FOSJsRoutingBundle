test('api definition', function() {
  expect(6);

  ok(Routing, 'Routing is defined');
  ok($.isFunction(Routing.connect), 'Routing.connect is a function');
  ok($.isFunction(Routing.generate), 'Routing.generate is a function');
  ok($.isFunction(Routing.get), 'Routing.get is a function');
  ok($.isFunction(Routing.has), 'Routing.has is a function');
  ok($.isFunction(Routing.flush), 'Routing.flush is a function');
});

test('route registration', function() {
  expect(11);

  strictEqual(Routing.connect('route_1', '/route1'), Routing,
              'connect "route_1". Routing.connect returns Routing');
  ok(Routing.has('route_1'), 'route_1 is connected');
  ok(!Routing.has('route_2'), 'route_2 is not yet connected');
  equal(Routing.get('route_1'), '/route1', 'route_1 url is correct');

  strictEqual(Routing.connect('route_2', '/route2'), Routing,
              'connect "route_2". Routing.connect returns Routing');
  ok(Routing.has('route_1'), 'route_1 is still connected');
  ok(Routing.has('route_2'), 'route_2 is connected');
  equal(Routing.get('route_1'), '/route1', 'route_1 url is still correct');
  equal(Routing.get('route_2'), '/route2', 'route_2 url is correct');

  Routing.flush();
  strictEqual(Routing.connect('route_1', '/route1'), Routing,
              'connect "route_1". Routing.connect returns Routing');
  ok(Routing.has('route_1'), 'route_1 is connected');
});

test('route generation', function() {
  expect(14);

  Routing.flush();
  Routing.prefix = '';
  Routing.variablePrefix = '{';
  Routing.variableSuffix = '}';

  equal(Routing.connect('route_1', '/route1').generate('route_1'), '/route1',
      'generating url without parameters returns url');

  equal(Routing.generate('route_1', { foo: 'bar' }), '/route1?foo=bar',
      'passing extra parameters happens it as query string');

  equal(Routing.connect('route_1', '/route1?a=b')
                              .generate('route_1', { foo: 'bar' }),
      '/route1?a=b&foo=bar',
      'query string is extended if already started');

  equal(Routing.connect('route_1', '/route/{id}/edit')
                              .generate('route_1', { id: 'bar' }),
      '/route/bar/edit',
      'basic parameter replacement is ok');

  equal(Routing.connect('route_1', '/route/{id}')
                              .generate('route_1', { id: 'bar' }),
      '/route/bar',
      'end of string parameter replacement is ok');

  equal(Routing.generate('route_1', { id: 'foo', foo: 'bar' }),
      '/route/foo?foo=bar',
      'passing extra parameters happens it as query string');

  equal(Routing.connect('route_1',
      '/route/{identical}/id/{id}/id/{identical}/foo'
      ).generate('route_1', { id: 'bar' }),
      '/route/{identical}/id/bar/id/{identical}/foo',
      'only exact variable matches.');

  equal(Routing.connect('route_1', '{id}')
                              .generate('route_1', { id: 'bar' }),
      '/bar',
      'replacement without separator is ok');

  // check for prefix
  Routing.prefix = '/baz/';
  equal(Routing.connect('route_1', '{id}')
                              .generate('route_1', { id: 'bar' }),
      '/baz/bar',
      'prefix is added');
  Routing.prefix = 'baz';
  equal(Routing.connect('route_1', '{id}')
                              .generate('route_1', { id: 'bar' }),
      '/baz/bar',
      'prefix is surrounded by slashes');

  // check for default parameters
  Routing.prefix = '';
  equal(Routing.connect('route_1', '/foo/{id}', { id: 120 })
                              .generate('route_1'),
      '/foo/120',
      'default parameter is using if no parameter is specified');
  equal(Routing.connect('route_1', '/foo/{id}', { id: 120 })
                              .generate('route_1', { id: 210 }),
      '/foo/210',
      'default parameter is overrided if a valid parameter is specified');
  equal(Routing.connect('route_1', '/foo/{id}', { id: 120 })
                              .generate('route_1', { di: 210 }),
      '/foo/120?di=210',
      'default parameter is using if a wrong parameter is specified');
  equal(Routing.connect('route_1', '/foo/{id}/val/{val}', { val: 10 })
                              .generate('route_1', { id: 120, val: 210 }),
      '/foo/120/val/210',
      'Default parameters work with multiple variables');
});
