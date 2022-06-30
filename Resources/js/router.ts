export interface RouteDefaults {
  [index: string]: string | null;
}

export interface RouteRequirements {
  [index: string]: string;
}

export interface RouteParams {
  [index: string]: any;
}

export interface QueryParamAddFunction {
  (prefix: string, params: any): void;
}

export interface Route {
  tokens: (string|boolean)[][];
  defaults: undefined[] | RouteDefaults;
  requirements: undefined[] | RouteRequirements;
  hosttokens: string[][];
  schemes: string[];
  methods: string[];
}

export interface RoutesMap {
  [index: string]: Route;
}

export interface Context {
  base_url: string;
  prefix: string;
  host: string;
  port: string | null;
  scheme: string;
  locale: string | null;
}

export interface RoutingData {
  base_url: string;
  routes: RoutesMap;
  prefix?: string;
  host: string;
  port?: string | null;
  scheme?: string;
  locale?: string | null;
}

export class Router {
  private context_: Context;
  private routes_!: RoutesMap;

  static getInstance(): Router {
    return Routing;
  }

  static setData(data: RoutingData): void {
    const router = Router.getInstance();

    router.setRoutingData(data);
  }

  constructor(context?: Context, routes?: RoutesMap) {
    this.context_ = context || { base_url: '', prefix: '', host: '', port: '', scheme: '', locale: '' };
    this.setRoutes(routes || {});
  }

  setRoutingData(data: RoutingData): void {
    this.setBaseUrl(data['base_url']);
    this.setRoutes(data['routes']);

    if (typeof data.prefix !== 'undefined') {
      this.setPrefix(data['prefix']);
    }
    if (typeof data.port !== 'undefined') {
      this.setPort(data['port']);
    }
    if (typeof data.locale !== 'undefined') {
      this.setLocale(data['locale']);
    }

    this.setHost(data['host']);

    if (typeof data.scheme !== 'undefined') {
      this.setScheme(data['scheme']);
    }
  }

  setRoutes(routes: RoutesMap): void {
    this.routes_ = Object.freeze(routes);
  }

  getRoutes(): RoutesMap {
    return this.routes_;
  }

  setBaseUrl(baseUrl: string): void {
    this.context_.base_url = baseUrl;
  }

  getBaseUrl(): string {
    return this.context_.base_url;
  }

  setPrefix(prefix: string): void {
    this.context_.prefix = prefix;
  }

  setScheme(scheme: string): void {
    this.context_.scheme = scheme;
  }

  getScheme(): string {
    return this.context_.scheme;
  }

  setHost(host: string): void {
    this.context_.host = host;
  }

  getHost(): string {
    return this.context_.host;
  }

  setPort(port: string | null) {
    this.context_.port = port;
  }

  getPort(): string | null {
    return this.context_.port;
  };

  setLocale(locale: string | null) {
    this.context_.locale = locale;
  }

  getLocale(): string | null {
    return this.context_.locale;
  };

  /**
   * Builds query string params added to a URL.
   * Port of jQuery's $.param() function, so credit is due there.
   */
  buildQueryParams(prefix: string, params: any, add: QueryParamAddFunction): void {
    let name;
    let rbracket = new RegExp(/\[\]$/);

    if (params instanceof Array) {
      params.forEach((val, i) => {
        if (rbracket.test(prefix)) {
          add(prefix, val);
        } else {
          this.buildQueryParams(prefix + '[' + (typeof val === 'object' ? i : '') + ']', val, add);
        }
      });
    } else if (typeof params === 'object') {
      for (name in params) {
        this.buildQueryParams(prefix + '[' + name + ']', params[name], add);
      }
    } else {
      add(prefix, params);
    }
  }

  /**
   * Returns a raw route object.
   */
  getRoute(name: string): Route {
    let prefixedName = this.context_.prefix + name;
    let sf41i18nName = name + '.' + this.context_.locale;
    let prefixedSf41i18nName = this.context_.prefix + name + '.' + this.context_.locale;
    let variants = [prefixedName, sf41i18nName, prefixedSf41i18nName, name];

    for (let i in variants) {
      if (variants[i] in this.routes_) {
        return this.routes_[variants[i]];
      }
    }

    throw new Error('The route "' + name + '" does not exist.');
  }

  /**
   * Generates the URL for a route.
   */
  generate(name: string, opt_params?: RouteParams, absolute?: boolean): string {
    let route = (this.getRoute(name));
    let params = opt_params || {};
    let unusedParams = Object.assign({}, params);
    let url = '';
    let optional = true;
    let host = '';
    let port = (typeof this.getPort() == 'undefined' || this.getPort() === null) ? '' : this.getPort();

    route.tokens.forEach((token) => {
      if ('text' === token[0] && typeof token[1] === 'string') {
        url = Router.encodePathComponent(token[1]) + url;
        optional = false;

        return;
      }

      if ('variable' === token[0]) {
        if (token.length === 6 && token[5] === true) { // Sixth part of the token array indicates if it should be included in case of defaults
          optional = false;
        }
        let hasDefault = route.defaults && !Array.isArray(route.defaults) && typeof token[3] === 'string' && (token[3] in route.defaults);
        if (false === optional || !hasDefault || ((typeof token[3] === 'string' && token[3] in params) && !Array.isArray(route.defaults) && params[token[3]] != route.defaults[token[3]])) {
          let value;

          if (typeof token[3] === 'string' && token[3] in params) {
            value = params[token[3]];
            delete unusedParams[token[3]];
          } else if (typeof token[3] === 'string' && hasDefault && !Array.isArray(route.defaults)) {
            value = route.defaults[token[3]];
          } else if (optional) {
            return;
          } else {
            throw new Error('The route "' + name + '" requires the parameter "' + token[3] + '".');
          }

          let empty = true === value || false === value || '' === value;

          if (!empty || !optional) {
            let encodedValue = Router.encodePathComponent(value);

            if ('null' === encodedValue && null === value) {
              encodedValue = '';
            }

            url = token[1] + encodedValue + url;
          }

          optional = false;
        } else if (hasDefault && (typeof token[3] === 'string' && token[3] in unusedParams)) {
          delete unusedParams[token[3]];
        }

        return;
      }

      throw new Error('The token type "' + token[0] + '" is not supported.');
    });

    if (url === '') {
      url = '/';
    }

    route.hosttokens.forEach((token) => {
      let value;

      if ('text' === token[0]) {
        host = token[1] + host;

        return;
      }

      if ('variable' === token[0]) {
        if (token[3] in params) {
          value = params[token[3]];
          delete unusedParams[token[3]];
        } else if (route.defaults && !Array.isArray(route.defaults) && (token[3] in route.defaults)) {
          value = route.defaults[token[3]];
        }

        host = token[1] + value + host;
      }
    });

    url = this.context_.base_url + url;

    if (route.requirements && ('_scheme' in route.requirements) && this.getScheme() != route.requirements['_scheme']) {
      const currentHost = host || this.getHost();

      url = route.requirements['_scheme'] + '://' + currentHost + (currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
    } else if ('undefined' !== typeof route.schemes && 'undefined' !== typeof route.schemes[0] && this.getScheme() !== route.schemes[0]) {
      const currentHost = host || this.getHost();

      url = route.schemes[0] + '://' + currentHost + (currentHost.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
    } else if (host && this.getHost() !== host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port)) {
      url = this.getScheme() + '://' + host + (host.indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
    } else if (absolute === true) {
      url = this.getScheme() + '://' + this.getHost() + (this.getHost().indexOf(':' + port) > -1 || '' === port ? '' : ':' + port) + url;
    }

    if (Object.keys(unusedParams).length > 0) {
      let queryParams: string[] = [];
      let add = (key: string, value: string|(() => string)) => {
        // if value is a function then call it and assign it's return value as value
        value = (typeof value === 'function') ? value() : value;

        // change null to empty string
        value = (value === null) ? '' : value;

        queryParams.push(Router.encodeQueryComponent(key) + '=' + Router.encodeQueryComponent(value));
      };

      for (const prefix in unusedParams) {
        if(unusedParams.hasOwnProperty(prefix)) {
          this.buildQueryParams(prefix, unusedParams[prefix], add);
        }
      }

      url = url + '?' + queryParams.join('&');
    }

    return url;
  }

  /**
   * Returns the given string encoded to mimic Symfony URL generator.
   */
  static customEncodeURIComponent(value: string): string {
    return encodeURIComponent(value)
      .replace(/%2F/g, '/')
      .replace(/%40/g, '@')
      .replace(/%3A/g, ':')
      .replace(/%21/g, '!')
      .replace(/%3B/g, ';')
      .replace(/%2C/g, ',')
      .replace(/%2A/g, '*')
      .replace(/\(/g, '%28')
      .replace(/\)/g, '%29')
      .replace(/'/g, '%27')
      ;
  }

  /**
   * Returns the given path properly encoded to mimic Symfony URL generator.
   */
  static encodePathComponent(value: string): string {
    return Router.customEncodeURIComponent(value)
      .replace(/%3D/g, '=')
      .replace(/%2B/g, '+')
      .replace(/%21/g, '!')
      .replace(/%7C/g, '|')
      ;
  }

  /**
   * Returns the given query parameter or value properly encoded to mimic Symfony URL generator.
   */
  static encodeQueryComponent(value: string): string {
    return Router.customEncodeURIComponent(value)
      .replace(/%3F/g, '?')
      ;
  }
}

export const Routing = new Router();

export default Routing;
