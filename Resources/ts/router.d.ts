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
    tokens: (string | boolean)[][];
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
export declare class Router {
    private context_;
    private routes_;
    static getInstance(): Router;
    static setData(data: RoutingData): void;
    constructor(context?: Context, routes?: RoutesMap);
    setRoutingData(data: RoutingData): void;
    setRoutes(routes: RoutesMap): void;
    getRoutes(): RoutesMap;
    setBaseUrl(baseUrl: string): void;
    getBaseUrl(): string;
    setPrefix(prefix: string): void;
    setScheme(scheme: string): void;
    getScheme(): string;
    setHost(host: string): void;
    getHost(): string;
    setPort(port: string | null): void;
    getPort(): string | null;
    setLocale(locale: string | null): void;
    getLocale(): string | null;
    /**
     * Builds query string params added to a URL.
     * Port of jQuery's $.param() function, so credit is due there.
     */
    buildQueryParams(prefix: string, params: any, add: QueryParamAddFunction): void;
    /**
     * Returns a raw route object.
     */
    getRoute(name: string): Route;
    /**
     * Generates the URL for a route.
     */
    generate(name: string, opt_params?: RouteParams, absolute?: boolean): string;
    /**
     * Returns the given string encoded to mimic Symfony URL generator.
     */
    static customEncodeURIComponent(value: string): string;
    /**
     * Returns the given path properly encoded to mimic Symfony URL generator.
     */
    static encodePathComponent(value: string): string;
    /**
     * Returns the given query parameter or value properly encoded to mimic Symfony URL generator.
     */
    static encodeQueryComponent(value: string): string;
}
export declare const Routing: Router;
export default Routing;
