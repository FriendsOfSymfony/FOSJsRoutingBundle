import { expectType } from 'tsd';
import { RoutesMap } from '../js/router';
import { Route, Router, Routing } from './router';
import routes from './routes.json';

expectType<Router>(Router.getInstance());
expectType<Router>(Routing);

expectType<RoutesMap>(Routing.getRoutes());
expectType<Route>(Routing.getRoute('homepage'));

expectType<string>(Routing.getBaseUrl());
Routing.setBaseUrl('');

expectType<string>(Routing.getScheme());
Routing.setScheme('https');

expectType<string>(Routing.getHost());
Routing.setHost('localhost');

expectType<string | null>(Routing.getPort());
Routing.setPort('1234');

expectType<string | null>(Routing.getLocale());
Routing.setLocale('en');

Routing.setRoutingData(routes);
Routing.setRoutingData({
  base_url: '',
  routes: {
    homepage: { tokens: [['text', '/']], defaults: [], requirements: [], hosttokens: [], methods: [], schemes: [], },
    admin_index: { tokens: [['text', '/admin']], defaults: [], requirements: [], hosttokens: [], methods: [], schemes: [], },
    admin_pages: { tokens: [['text', '/admin/path']], defaults: [], requirements: [], hosttokens: [], methods: [], schemes: [], },
    blog_index: { tokens: [['text', '/blog']], defaults: [], requirements: [], hosttokens: [['text', 'localhost']], methods: [], schemes: [], },
    blog_post: {
      tokens: [
        ['variable', '/', '[^/]++', 'slug'],
        ['text', '/blog'],
      ],
      defaults: [],
      requirements: [],
      hosttokens: [['text', 'localhost']],
      methods: [],
      schemes: [],
    },
    users_delete: {
      tokens: [
        ['text', '/delete'],
        ['variable', '/', '[^/]++', 'id', true],
        ['text', '/users']
      ],
      defaults: [],
      requirements: [],
      hosttokens: [],
      methods: [
        'DELETE'
      ],
      schemes: []
    },
    feed_post: {
      tokens: [
        ['variable', '.', 'js|json', '_format', true],
        ['text', '/feed/post']
      ],
      defaults: {
        _format: 'xml',
      },
      requirements: {
        _format: 'xml|json',
      },
      hosttokens: [],
      methods: ['GET'],
      schemes: [],
    },
  },
  prefix: '',
  host: '',
  port: null,
  scheme: '',
  locale: 'en',
});

expectType<string>(Routing.generate('homepage'));
expectType<string>(Routing.generate('blog_post', {
  slug: 'my-blog-post',
}));
expectType<string>(Routing.generate('users_delete', {
  id: 123,
}));
