/**
 * Portions of this code are from the Google Closure Library,
 * received from the Closure Authors under the Apache 2.0 license.
 *
 * All other code is (C) FriendsOfSymfony and subject to the MIT license.
 */
(function() {var f,l=this;function m(a,c){var b=a.split("."),d=l;b[0]in d||!d.execScript||d.execScript("var "+b[0]);for(var e;b.length&&(e=b.shift());)b.length||void 0===c?d=d[e]?d[e]:d[e]={}:d[e]=c};var n=Array.prototype,p=n.forEach?function(a,c,b){n.forEach.call(a,c,b)}:function(a,c,b){for(var d=a.length,e="string"==typeof a?a.split(""):a,g=0;g<d;g++)g in e&&c.call(b,e[g],g,a)};function q(a){var c=0,b;for(b in a)c++;return c}function r(a){var c={},b;for(b in a)c[b]=a[b];return c};function s(a,c){this.c={};this.b=[];var b=arguments.length;if(1<b){if(b%2)throw Error("Uneven number of arguments");for(var d=0;d<b;d+=2)this.set(arguments[d],arguments[d+1])}else if(a){var e;if(a instanceof s)for(u(a),d=a.b.concat(),u(a),e=[],b=0;b<a.b.length;b++)e.push(a.c[a.b[b]]);else{var b=[],g=0;for(d in a)b[g++]=d;d=b;b=[];g=0;for(e in a)b[g++]=a[e];e=b}for(b=0;b<d.length;b++)this.set(d[b],e[b])}}s.prototype.f=0;s.prototype.p=0;
function u(a){if(a.f!=a.b.length){for(var c=0,b=0;c<a.b.length;){var d=a.b[c];v(a.c,d)&&(a.b[b++]=d);c++}a.b.length=b}if(a.f!=a.b.length){for(var e={},b=c=0;c<a.b.length;)d=a.b[c],v(e,d)||(a.b[b++]=d,e[d]=1),c++;a.b.length=b}}s.prototype.get=function(a,c){return v(this.c,a)?this.c[a]:c};s.prototype.set=function(a,c){v(this.c,a)||(this.f++,this.b.push(a),this.p++);this.c[a]=c};function v(a,c){return Object.prototype.hasOwnProperty.call(a,c)};var w,x,y,A;function B(){return l.navigator?l.navigator.userAgent:null}A=y=x=w=!1;var C;if(C=B()){var D=l.navigator;w=0==C.lastIndexOf("Opera",0);x=!w&&(-1!=C.indexOf("MSIE")||-1!=C.indexOf("Trident"));y=!w&&-1!=C.indexOf("WebKit");A=!w&&!y&&!x&&"Gecko"==D.product}var E=x,G=A,H=y;var I;if(w&&l.opera){var J=l.opera.version;"function"==typeof J&&J()}else G?I=/rv\:([^\);]+)(\)|;)/:E?I=/\b(?:MSIE|rv)\s+([^\);]+)(\)|;)/:H&&(I=/WebKit\/(\S+)/),I&&I.exec(B());function K(a,c){this.a=a||{e:"",prefix:"",host:"",scheme:""};this.h(c||{})}K.g=function(){return K.j?K.j:K.j=new K};f=K.prototype;f.h=function(a){this.d=new s(a)};f.o=function(){return this.d};f.k=function(a){this.a.e=a};f.n=function(){return this.a.e};f.l=function(a){this.a.prefix=a};
function L(a,c,b,d){var e,g=RegExp(/\[\]$/);if(b instanceof Array)p(b,function(b,e){g.test(c)?d(c,b):L(a,c+"["+("object"===typeof b?e:"")+"]",b,d)});else if("object"===typeof b)for(e in b)L(a,c+"["+e+"]",b[e],d);else d(c,b)}f.i=function(a){var c=this.a.prefix+a;if(v(this.d.c,c))a=c;else if(!v(this.d.c,a))throw Error('The route "'+a+'" does not exist.');return this.d.get(a)};
f.m=function(a,c,b){var d=this.i(a),e=c||{},g=r(e),h="",t=!0,k="";p(d.tokens,function(b){if("text"===b[0])h=b[1]+h,t=!1;else if("variable"===b[0]){var c=b[3]in d.defaults;if(!1===t||!c||b[3]in e&&e[b[3]]!=d.defaults[b[3]]){if(b[3]in e){var c=e[b[3]],k=b[3];k in g&&delete g[k]}else if(c)c=d.defaults[b[3]];else{if(t)return;throw Error('The route "'+a+'" requires the parameter "'+b[3]+'".');}if(!0!==c&&!1!==c&&""!==c||!t)k=encodeURIComponent(c).replace(/%2F/g,"/"),"null"===k&&null===c&&(k=""),h=b[1]+
k+h;t=!1}else c&&(b=b[3],b in g&&delete g[b])}else throw Error('The token type "'+b[0]+'" is not supported.');});""===h&&(h="/");p(d.hosttokens,function(a){var b;if("text"===a[0])k=a[1]+k;else if("variable"===a[0]){if(a[3]in e){b=e[a[3]];var c=a[3];c in g&&delete g[c]}else a[3]in d.defaults&&(b=d.defaults[a[3]]);k=a[1]+b+k}});h=this.a.e+h;"_scheme"in d.requirements&&this.a.scheme!=d.requirements._scheme?h=d.requirements._scheme+"://"+(k||this.a.host)+h:"schemes"in d&&0<d.schemes.length&&"undefined"!==
typeof d.schemes[0]&&this.a.scheme!=d.schemes[0]?h=d.schemes[0]+"://"+(k||this.a.host)+h:k&&this.a.host!==k?h=this.a.scheme+"://"+k+h:!0===b&&(h=this.a.scheme+"://"+this.a.host+h);if(0<q(g)){var z,F=[];c=function(a,b){b="function"===typeof b?b():b;F.push(encodeURIComponent(a)+"\x3d"+encodeURIComponent(null===b?"":b))};for(z in g)L(this,z,g[z],c);h=h+"?"+F.join("\x26").replace(/%20/g,"+")}return h};m("fos.Router",K);m("fos.Router.setData",function(a){var c=K.g();c.k(a.base_url);c.h(a.routes);"prefix"in a&&c.l(a.prefix);c.a.host=a.host;c.a.scheme=a.scheme});K.getInstance=K.g;K.prototype.setRoutes=K.prototype.h;K.prototype.getRoutes=K.prototype.o;K.prototype.setBaseUrl=K.prototype.k;K.prototype.getBaseUrl=K.prototype.n;K.prototype.generate=K.prototype.m;K.prototype.setPrefix=K.prototype.l;K.prototype.getRoute=K.prototype.i;window.Routing=K.g();})();