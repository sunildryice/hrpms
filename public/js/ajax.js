/*!
  * https://github.com/paulmillr/es6-shim
  * @license es6-shim Copyright 2013-2016 by Paul Miller (http://paulmillr.com)
  *   and contributors,  MIT License
  * es6-shim: v0.35.1
  * see https://github.com/paulmillr/es6-shim/blob/0.35.1/LICENSE
  * Details and documentation:
  * https://github.com/paulmillr/es6-shim/
  */
(function(e,t){if(typeof define==="function"&&define.amd){define(t)}else if(typeof exports==="object"){module.exports=t()}else{e.returnExports=t()}})(this,function(){"use strict";var e=Function.call.bind(Function.apply);var t=Function.call.bind(Function.call);var r=Array.isArray;var n=Object.keys;var o=function notThunker(t){return function notThunk(){return!e(t,this,arguments)}};var i=function(e){try{e();return false}catch(e){return true}};var a=function valueOrFalseIfThrows(e){try{return e()}catch(e){return false}};var u=o(i);var f=function(){return!i(function(){Object.defineProperty({},"x",{get:function(){}})})};var s=!!Object.defineProperty&&f();var c=function foo(){}.name==="foo";var l=Function.call.bind(Array.prototype.forEach);var p=Function.call.bind(Array.prototype.reduce);var v=Function.call.bind(Array.prototype.filter);var y=Function.call.bind(Array.prototype.some);var h=function(e,t,r,n){if(!n&&t in e){return}if(s){Object.defineProperty(e,t,{configurable:true,enumerable:false,writable:true,value:r})}else{e[t]=r}};var b=function(e,t,r){l(n(t),function(n){var o=t[n];h(e,n,o,!!r)})};var g=Function.call.bind(Object.prototype.toString);var d=typeof/abc/==="function"?function IsCallableSlow(e){return typeof e==="function"&&g(e)==="[object Function]"}:function IsCallableFast(e){return typeof e==="function"};var m={getter:function(e,t,r){if(!s){throw new TypeError("getters require true ES5 support")}Object.defineProperty(e,t,{configurable:true,enumerable:false,get:r})},proxy:function(e,t,r){if(!s){throw new TypeError("getters require true ES5 support")}var n=Object.getOwnPropertyDescriptor(e,t);Object.defineProperty(r,t,{configurable:n.configurable,enumerable:n.enumerable,get:function getKey(){return e[t]},set:function setKey(r){e[t]=r}})},redefine:function(e,t,r){if(s){var n=Object.getOwnPropertyDescriptor(e,t);n.value=r;Object.defineProperty(e,t,n)}else{e[t]=r}},defineByDescriptor:function(e,t,r){if(s){Object.defineProperty(e,t,r)}else if("value"in r){e[t]=r.value}},preserveToString:function(e,t){if(t&&d(t.toString)){h(e,"toString",t.toString.bind(t),true)}}};var O=Object.create||function(e,t){var r=function Prototype(){};r.prototype=e;var o=new r;if(typeof t!=="undefined"){n(t).forEach(function(e){m.defineByDescriptor(o,e,t[e])})}return o};var w=function(e,t){if(!Object.setPrototypeOf){return false}return a(function(){var r=function Subclass(t){var r=new e(t);Object.setPrototypeOf(r,Subclass.prototype);return r};Object.setPrototypeOf(r,e);r.prototype=O(e.prototype,{constructor:{value:r}});return t(r)})};var j=function(){if(typeof self!=="undefined"){return self}if(typeof window!=="undefined"){return window}if(typeof global!=="undefined"){return global}throw new Error("unable to locate global object")};var S=j();var T=S.isFinite;var I=Function.call.bind(String.prototype.indexOf);var E=Function.apply.bind(Array.prototype.indexOf);var P=Function.call.bind(Array.prototype.concat);var C=Function.call.bind(String.prototype.slice);var M=Function.call.bind(Array.prototype.push);var x=Function.apply.bind(Array.prototype.push);var N=Function.call.bind(Array.prototype.shift);var A=Math.max;var R=Math.min;var _=Math.floor;var k=Math.abs;var F=Math.exp;var L=Math.log;var D=Math.sqrt;var z=Function.call.bind(Object.prototype.hasOwnProperty);var q;var W=function(){};var G=S.Map;var H=G&&G.prototype["delete"];var V=G&&G.prototype.get;var B=G&&G.prototype.has;var U=G&&G.prototype.set;var $=S.Symbol||{};var J=$.species||"@@species";var X=Number.isNaN||function isNaN(e){return e!==e};var K=Number.isFinite||function isFinite(e){return typeof e==="number"&&T(e)};var Z=d(Math.sign)?Math.sign:function sign(e){var t=Number(e);if(t===0){return t}if(X(t)){return t}return t<0?-1:1};var Y=function isArguments(e){return g(e)==="[object Arguments]"};var Q=function isArguments(e){return e!==null&&typeof e==="object"&&typeof e.length==="number"&&e.length>=0&&g(e)!=="[object Array]"&&g(e.callee)==="[object Function]"};var ee=Y(arguments)?Y:Q;var te={primitive:function(e){return e===null||typeof e!=="function"&&typeof e!=="object"},string:function(e){return g(e)==="[object String]"},regex:function(e){return g(e)==="[object RegExp]"},symbol:function(e){return typeof S.Symbol==="function"&&typeof e==="symbol"}};var re=function overrideNative(e,t,r){var n=e[t];h(e,t,r,true);m.preserveToString(e[t],n)};var ne=typeof $==="function"&&typeof $["for"]==="function"&&te.symbol($());var oe=te.symbol($.iterator)?$.iterator:"_es6-shim iterator_";if(S.Set&&typeof(new S.Set)["@@iterator"]==="function"){oe="@@iterator"}if(!S.Reflect){h(S,"Reflect",{},true)}var ie=S.Reflect;var ae=String;var ue=typeof document==="undefined"||!document?null:document.all;var fe=ue==null?function isNullOrUndefined(e){return e==null}:function isNullOrUndefinedAndNotDocumentAll(e){return e==null&&e!==ue};var se={Call:function Call(t,r){var n=arguments.length>2?arguments[2]:[];if(!se.IsCallable(t)){throw new TypeError(t+" is not a function")}return e(t,r,n)},RequireObjectCoercible:function(e,t){if(fe(e)){throw new TypeError(t||"Cannot call method on "+e)}return e},TypeIsObject:function(e){if(e===void 0||e===null||e===true||e===false){return false}return typeof e==="function"||typeof e==="object"||e===ue},ToObject:function(e,t){return Object(se.RequireObjectCoercible(e,t))},IsCallable:d,IsConstructor:function(e){return se.IsCallable(e)},ToInt32:function(e){return se.ToNumber(e)>>0},ToUint32:function(e){return se.ToNumber(e)>>>0},ToNumber:function(e){if(g(e)==="[object Symbol]"){throw new TypeError("Cannot convert a Symbol value to a number")}return+e},ToInteger:function(e){var t=se.ToNumber(e);if(X(t)){return 0}if(t===0||!K(t)){return t}return(t>0?1:-1)*_(k(t))},ToLength:function(e){var t=se.ToInteger(e);if(t<=0){return 0}if(t>Number.MAX_SAFE_INTEGER){return Number.MAX_SAFE_INTEGER}return t},SameValue:function(e,t){if(e===t){if(e===0){return 1/e===1/t}return true}return X(e)&&X(t)},SameValueZero:function(e,t){return e===t||X(e)&&X(t)},IsIterable:function(e){return se.TypeIsObject(e)&&(typeof e[oe]!=="undefined"||ee(e))},GetIterator:function(e){if(ee(e)){return new q(e,"value")}var t=se.GetMethod(e,oe);if(!se.IsCallable(t)){throw new TypeError("value is not an iterable")}var r=se.Call(t,e);if(!se.TypeIsObject(r)){throw new TypeError("bad iterator")}return r},GetMethod:function(e,t){var r=se.ToObject(e)[t];if(fe(r)){return void 0}if(!se.IsCallable(r)){throw new TypeError("Method not callable: "+t)}return r},IteratorComplete:function(e){return!!e.done},IteratorClose:function(e,t){var r=se.GetMethod(e,"return");if(r===void 0){return}var n,o;try{n=se.Call(r,e)}catch(e){o=e}if(t){return}if(o){throw o}if(!se.TypeIsObject(n)){throw new TypeError("Iterator's return method returned a non-object.")}},IteratorNext:function(e){var t=arguments.length>1?e.next(arguments[1]):e.next();if(!se.TypeIsObject(t)){throw new TypeError("bad iterator")}return t},IteratorStep:function(e){var t=se.IteratorNext(e);var r=se.IteratorComplete(t);return r?false:t},Construct:function(e,t,r,n){var o=typeof r==="undefined"?e:r;if(!n&&ie.construct){return ie.construct(e,t,o)}var i=o.prototype;if(!se.TypeIsObject(i)){i=Object.prototype}var a=O(i);var u=se.Call(e,a,t);return se.TypeIsObject(u)?u:a},SpeciesConstructor:function(e,t){var r=e.constructor;if(r===void 0){return t}if(!se.TypeIsObject(r)){throw new TypeError("Bad constructor")}var n=r[J];if(fe(n)){return t}if(!se.IsConstructor(n)){throw new TypeError("Bad @@species")}return n},CreateHTML:function(e,t,r,n){var o=se.ToString(e);var i="<"+t;if(r!==""){var a=se.ToString(n);var u=a.replace(/"/g,"&quot;");i+=" "+r+'="'+u+'"'}var f=i+">";var s=f+o;return s+"</"+t+">"},IsRegExp:function IsRegExp(e){if(!se.TypeIsObject(e)){return false}var t=e[$.match];if(typeof t!=="undefined"){return!!t}return te.regex(e)},ToString:function ToString(e){return ae(e)}};if(s&&ne){var ce=function defineWellKnownSymbol(e){if(te.symbol($[e])){return $[e]}var t=$["for"]("Symbol."+e);Object.defineProperty($,e,{configurable:false,enumerable:false,writable:false,value:t});return t};if(!te.symbol($.search)){var le=ce("search");var pe=String.prototype.search;h(RegExp.prototype,le,function search(e){return se.Call(pe,e,[this])});var ve=function search(e){var t=se.RequireObjectCoercible(this);if(!fe(e)){var r=se.GetMethod(e,le);if(typeof r!=="undefined"){return se.Call(r,e,[t])}}return se.Call(pe,t,[se.ToString(e)])};re(String.prototype,"search",ve)}if(!te.symbol($.replace)){var ye=ce("replace");var he=String.prototype.replace;h(RegExp.prototype,ye,function replace(e,t){return se.Call(he,e,[this,t])});var be=function replace(e,t){var r=se.RequireObjectCoercible(this);if(!fe(e)){var n=se.GetMethod(e,ye);if(typeof n!=="undefined"){return se.Call(n,e,[r,t])}}return se.Call(he,r,[se.ToString(e),t])};re(String.prototype,"replace",be)}if(!te.symbol($.split)){var ge=ce("split");var de=String.prototype.split;h(RegExp.prototype,ge,function split(e,t){return se.Call(de,e,[this,t])});var me=function split(e,t){var r=se.RequireObjectCoercible(this);if(!fe(e)){var n=se.GetMethod(e,ge);if(typeof n!=="undefined"){return se.Call(n,e,[r,t])}}return se.Call(de,r,[se.ToString(e),t])};re(String.prototype,"split",me)}var Oe=te.symbol($.match);var we=Oe&&function(){var e={};e[$.match]=function(){return 42};return"a".match(e)!==42}();if(!Oe||we){var je=ce("match");var Se=String.prototype.match;h(RegExp.prototype,je,function match(e){return se.Call(Se,e,[this])});var Te=function match(e){var t=se.RequireObjectCoercible(this);if(!fe(e)){var r=se.GetMethod(e,je);if(typeof r!=="undefined"){return se.Call(r,e,[t])}}return se.Call(Se,t,[se.ToString(e)])};re(String.prototype,"match",Te)}}var Ie=function wrapConstructor(e,t,r){m.preserveToString(t,e);if(Object.setPrototypeOf){Object.setPrototypeOf(e,t)}if(s){l(Object.getOwnPropertyNames(e),function(n){if(n in W||r[n]){return}m.proxy(e,n,t)})}else{l(Object.keys(e),function(n){if(n in W||r[n]){return}t[n]=e[n]})}t.prototype=e.prototype;m.redefine(e.prototype,"constructor",t)};var Ee=function(){return this};var Pe=function(e){if(s&&!z(e,J)){m.getter(e,J,Ee)}};var Ce=function(e,t){var r=t||function iterator(){return this};h(e,oe,r);if(!e[oe]&&te.symbol(oe)){e[oe]=r}};var Me=function createDataProperty(e,t,r){if(s){Object.defineProperty(e,t,{configurable:true,enumerable:true,writable:true,value:r})}else{e[t]=r}};var xe=function createDataPropertyOrThrow(e,t,r){Me(e,t,r);if(!se.SameValue(e[t],r)){throw new TypeError("property is nonconfigurable")}};var Ne=function(e,t,r,n){if(!se.TypeIsObject(e)){throw new TypeError("Constructor requires `new`: "+t.name)}var o=t.prototype;if(!se.TypeIsObject(o)){o=r}var i=O(o);for(var a in n){if(z(n,a)){var u=n[a];h(i,a,u,true)}}return i};if(String.fromCodePoint&&String.fromCodePoint.length!==1){var Ae=String.fromCodePoint;re(String,"fromCodePoint",function fromCodePoint(e){return se.Call(Ae,this,arguments)})}var Re={fromCodePoint:function fromCodePoint(e){var t=[];var r;for(var n=0,o=arguments.length;n<o;n++){r=Number(arguments[n]);if(!se.SameValue(r,se.ToInteger(r))||r<0||r>1114111){throw new RangeError("Invalid code point "+r)}if(r<65536){M(t,String.fromCharCode(r))}else{r-=65536;M(t,String.fromCharCode((r>>10)+55296));M(t,String.fromCharCode(r%1024+56320))}}return t.join("")},raw:function raw(e){var t=se.ToObject(e,"bad callSite");var r=se.ToObject(t.raw,"bad raw value");var n=r.length;var o=se.ToLength(n);if(o<=0){return""}var i=[];var a=0;var u,f,s,c;while(a<o){u=se.ToString(a);s=se.ToString(r[u]);M(i,s);if(a+1>=o){break}f=a+1<arguments.length?arguments[a+1]:"";c=se.ToString(f);M(i,c);a+=1}return i.join("")}};if(String.raw&&String.raw({raw:{0:"x",1:"y",length:2}})!=="xy"){re(String,"raw",Re.raw)}b(String,Re);var _e=function repeat(e,t){if(t<1){return""}if(t%2){return repeat(e,t-1)+e}var r=repeat(e,t/2);return r+r};var ke=Infinity;var Fe={repeat:function repeat(e){var t=se.ToString(se.RequireObjectCoercible(this));var r=se.ToInteger(e);if(r<0||r>=ke){throw new RangeError("repeat count must be less than infinity and not overflow maximum string size")}return _e(t,r)},startsWith:function startsWith(e){var t=se.ToString(se.RequireObjectCoercible(this));if(se.IsRegExp(e)){throw new TypeError('Cannot call method "startsWith" with a regex')}var r=se.ToString(e);var n;if(arguments.length>1){n=arguments[1]}var o=A(se.ToInteger(n),0);return C(t,o,o+r.length)===r},endsWith:function endsWith(e){var t=se.ToString(se.RequireObjectCoercible(this));if(se.IsRegExp(e)){throw new TypeError('Cannot call method "endsWith" with a regex')}var r=se.ToString(e);var n=t.length;var o;if(arguments.length>1){o=arguments[1]}var i=typeof o==="undefined"?n:se.ToInteger(o);var a=R(A(i,0),n);return C(t,a-r.length,a)===r},includes:function includes(e){if(se.IsRegExp(e)){throw new TypeError('"includes" does not accept a RegExp')}var t=se.ToString(e);var r;if(arguments.length>1){r=arguments[1]}return I(this,t,r)!==-1},codePointAt:function codePointAt(e){var t=se.ToString(se.RequireObjectCoercible(this));var r=se.ToInteger(e);var n=t.length;if(r>=0&&r<n){var o=t.charCodeAt(r);var i=r+1===n;if(o<55296||o>56319||i){return o}var a=t.charCodeAt(r+1);if(a<56320||a>57343){return o}return(o-55296)*1024+(a-56320)+65536}}};if(String.prototype.includes&&"a".includes("a",Infinity)!==false){re(String.prototype,"includes",Fe.includes)}if(String.prototype.startsWith&&String.prototype.endsWith){var Le=i(function(){"/a/".startsWith(/a/)});var De=a(function(){return"abc".startsWith("a",Infinity)===false});if(!Le||!De){re(String.prototype,"startsWith",Fe.startsWith);re(String.prototype,"endsWith",Fe.endsWith)}}if(ne){var ze=a(function(){var e=/a/;e[$.match]=false;return"/a/".startsWith(e)});if(!ze){re(String.prototype,"startsWith",Fe.startsWith)}var qe=a(function(){var e=/a/;e[$.match]=false;return"/a/".endsWith(e)});if(!qe){re(String.prototype,"endsWith",Fe.endsWith)}var We=a(function(){var e=/a/;e[$.match]=false;return"/a/".includes(e)});if(!We){re(String.prototype,"includes",Fe.includes)}}b(String.prototype,Fe);var Ge=["\t\n\v\f\r \xa0\u1680\u180e\u2000\u2001\u2002\u2003","\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028","\u2029\ufeff"].join("");var He=new RegExp("(^["+Ge+"]+)|(["+Ge+"]+$)","g");var Ve=function trim(){return se.ToString(se.RequireObjectCoercible(this)).replace(He,"")};var Be=["\x85","\u200b","\ufffe"].join("");var Ue=new RegExp("["+Be+"]","g");var $e=/^[-+]0x[0-9a-f]+$/i;var Je=Be.trim().length!==Be.length;h(String.prototype,"trim",Ve,Je);var Xe=function(e){return{value:e,done:arguments.length===0}};var Ke=function(e){se.RequireObjectCoercible(e);this._s=se.ToString(e);this._i=0};Ke.prototype.next=function(){var e=this._s;var t=this._i;if(typeof e==="undefined"||t>=e.length){this._s=void 0;return Xe()}var r=e.charCodeAt(t);var n,o;if(r<55296||r>56319||t+1===e.length){o=1}else{n=e.charCodeAt(t+1);o=n<56320||n>57343?1:2}this._i=t+o;return Xe(e.substr(t,o))};Ce(Ke.prototype);Ce(String.prototype,function(){return new Ke(this)});var Ze={from:function from(e){var r=this;var n;if(arguments.length>1){n=arguments[1]}var o,i;if(typeof n==="undefined"){o=false}else{if(!se.IsCallable(n)){throw new TypeError("Array.from: when provided, the second argument must be a function")}if(arguments.length>2){i=arguments[2]}o=true}var a=typeof(ee(e)||se.GetMethod(e,oe))!=="undefined";var u,f,s;if(a){f=se.IsConstructor(r)?Object(new r):[];var c=se.GetIterator(e);var l,p;s=0;while(true){l=se.IteratorStep(c);if(l===false){break}p=l.value;try{if(o){p=typeof i==="undefined"?n(p,s):t(n,i,p,s)}f[s]=p}catch(e){se.IteratorClose(c,true);throw e}s+=1}u=s}else{var v=se.ToObject(e);u=se.ToLength(v.length);f=se.IsConstructor(r)?Object(new r(u)):new Array(u);var y;for(s=0;s<u;++s){y=v[s];if(o){y=typeof i==="undefined"?n(y,s):t(n,i,y,s)}xe(f,s,y)}}f.length=u;return f},of:function of(){var e=arguments.length;var t=this;var n=r(t)||!se.IsCallable(t)?new Array(e):se.Construct(t,[e]);for(var o=0;o<e;++o){xe(n,o,arguments[o])}n.length=e;return n}};b(Array,Ze);Pe(Array);q=function(e,t){this.i=0;this.array=e;this.kind=t};b(q.prototype,{next:function(){var e=this.i;var t=this.array;if(!(this instanceof q)){throw new TypeError("Not an ArrayIterator")}if(typeof t!=="undefined"){var r=se.ToLength(t.length);for(;e<r;e++){var n=this.kind;var o;if(n==="key"){o=e}else if(n==="value"){o=t[e]}else if(n==="entry"){o=[e,t[e]]}this.i=e+1;return Xe(o)}}this.array=void 0;return Xe()}});Ce(q.prototype);var Ye=Array.of===Ze.of||function(){var e=function Foo(e){this.length=e};e.prototype=[];var t=Array.of.apply(e,[1,2]);return t instanceof e&&t.length===2}();if(!Ye){re(Array,"of",Ze.of)}var Qe={copyWithin:function copyWithin(e,t){var r=se.ToObject(this);var n=se.ToLength(r.length);var o=se.ToInteger(e);var i=se.ToInteger(t);var a=o<0?A(n+o,0):R(o,n);var u=i<0?A(n+i,0):R(i,n);var f;if(arguments.length>2){f=arguments[2]}var s=typeof f==="undefined"?n:se.ToInteger(f);var c=s<0?A(n+s,0):R(s,n);var l=R(c-u,n-a);var p=1;if(u<a&&a<u+l){p=-1;u+=l-1;a+=l-1}while(l>0){if(u in r){r[a]=r[u]}else{delete r[a]}u+=p;a+=p;l-=1}return r},fill:function fill(e){var t;if(arguments.length>1){t=arguments[1]}var r;if(arguments.length>2){r=arguments[2]}var n=se.ToObject(this);var o=se.ToLength(n.length);t=se.ToInteger(typeof t==="undefined"?0:t);r=se.ToInteger(typeof r==="undefined"?o:r);var i=t<0?A(o+t,0):R(t,o);var a=r<0?o+r:r;for(var u=i;u<o&&u<a;++u){n[u]=e}return n},find:function find(e){var r=se.ToObject(this);var n=se.ToLength(r.length);if(!se.IsCallable(e)){throw new TypeError("Array#find: predicate must be a function")}var o=arguments.length>1?arguments[1]:null;for(var i=0,a;i<n;i++){a=r[i];if(o){if(t(e,o,a,i,r)){return a}}else if(e(a,i,r)){return a}}},findIndex:function findIndex(e){var r=se.ToObject(this);var n=se.ToLength(r.length);if(!se.IsCallable(e)){throw new TypeError("Array#findIndex: predicate must be a function")}var o=arguments.length>1?arguments[1]:null;for(var i=0;i<n;i++){if(o){if(t(e,o,r[i],i,r)){return i}}else if(e(r[i],i,r)){return i}}return-1},keys:function keys(){return new q(this,"key")},values:function values(){return new q(this,"value")},entries:function entries(){return new q(this,"entry")}};if(Array.prototype.keys&&!se.IsCallable([1].keys().next)){delete Array.prototype.keys}if(Array.prototype.entries&&!se.IsCallable([1].entries().next)){delete Array.prototype.entries}if(Array.prototype.keys&&Array.prototype.entries&&!Array.prototype.values&&Array.prototype[oe]){b(Array.prototype,{values:Array.prototype[oe]});if(te.symbol($.unscopables)){Array.prototype[$.unscopables].values=true}}if(c&&Array.prototype.values&&Array.prototype.values.name!=="values"){var et=Array.prototype.values;re(Array.prototype,"values",function values(){return se.Call(et,this,arguments)});h(Array.prototype,oe,Array.prototype.values,true)}b(Array.prototype,Qe);if(1/[true].indexOf(true,-0)<0){h(Array.prototype,"indexOf",function indexOf(e){var t=E(this,arguments);if(t===0&&1/t<0){return 0}return t},true)}Ce(Array.prototype,function(){return this.values()});if(Object.getPrototypeOf){Ce(Object.getPrototypeOf([].values()))}var tt=function(){return a(function(){return Array.from({length:-1}).length===0})}();var rt=function(){var e=Array.from([0].entries());return e.length===1&&r(e[0])&&e[0][0]===0&&e[0][1]===0}();if(!tt||!rt){re(Array,"from",Ze.from)}var nt=function(){return a(function(){return Array.from([0],void 0)})}();if(!nt){var ot=Array.from;re(Array,"from",function from(e){if(arguments.length>1&&typeof arguments[1]!=="undefined"){return se.Call(ot,this,arguments)}else{return t(ot,this,e)}})}var it=-(Math.pow(2,32)-1);var at=function(e,r){var n={length:it};n[r?(n.length>>>0)-1:0]=true;return a(function(){t(e,n,function(){throw new RangeError("should not reach here")},[]);return true})};if(!at(Array.prototype.forEach)){var ut=Array.prototype.forEach;re(Array.prototype,"forEach",function forEach(e){return se.Call(ut,this.length>=0?this:[],arguments)},true)}if(!at(Array.prototype.map)){var ft=Array.prototype.map;re(Array.prototype,"map",function map(e){return se.Call(ft,this.length>=0?this:[],arguments)},true)}if(!at(Array.prototype.filter)){var st=Array.prototype.filter;re(Array.prototype,"filter",function filter(e){return se.Call(st,this.length>=0?this:[],arguments)},true)}if(!at(Array.prototype.some)){var ct=Array.prototype.some;re(Array.prototype,"some",function some(e){return se.Call(ct,this.length>=0?this:[],arguments)},true)}if(!at(Array.prototype.every)){var lt=Array.prototype.every;re(Array.prototype,"every",function every(e){return se.Call(lt,this.length>=0?this:[],arguments)},true)}if(!at(Array.prototype.reduce)){var pt=Array.prototype.reduce;re(Array.prototype,"reduce",function reduce(e){return se.Call(pt,this.length>=0?this:[],arguments)},true)}if(!at(Array.prototype.reduceRight,true)){var vt=Array.prototype.reduceRight;re(Array.prototype,"reduceRight",function reduceRight(e){return se.Call(vt,this.length>=0?this:[],arguments)},true)}var yt=Number("0o10")!==8;var ht=Number("0b10")!==2;var bt=y(Be,function(e){return Number(e+0+e)===0});if(yt||ht||bt){var gt=Number;var dt=/^0b[01]+$/i;var mt=/^0o[0-7]+$/i;var Ot=dt.test.bind(dt);var wt=mt.test.bind(mt);var jt=function(e){var t;if(typeof e.valueOf==="function"){t=e.valueOf();if(te.primitive(t)){return t}}if(typeof e.toString==="function"){t=e.toString();if(te.primitive(t)){return t}}throw new TypeError("No default value")};var St=Ue.test.bind(Ue);var Tt=$e.test.bind($e);var It=function(){var e=function Number(t){var r;if(arguments.length>0){r=te.primitive(t)?t:jt(t,"number")}else{r=0}if(typeof r==="string"){r=se.Call(Ve,r);if(Ot(r)){r=parseInt(C(r,2),2)}else if(wt(r)){r=parseInt(C(r,2),8)}else if(St(r)||Tt(r)){r=NaN}}var n=this;var o=a(function(){gt.prototype.valueOf.call(n);return true});if(n instanceof e&&!o){return new gt(r)}return gt(r)};return e}();Ie(gt,It,{});b(It,{NaN:gt.NaN,MAX_VALUE:gt.MAX_VALUE,MIN_VALUE:gt.MIN_VALUE,NEGATIVE_INFINITY:gt.NEGATIVE_INFINITY,POSITIVE_INFINITY:gt.POSITIVE_INFINITY});Number=It;m.redefine(S,"Number",It)}var Et=Math.pow(2,53)-1;b(Number,{MAX_SAFE_INTEGER:Et,MIN_SAFE_INTEGER:-Et,EPSILON:2.220446049250313e-16,parseInt:S.parseInt,parseFloat:S.parseFloat,isFinite:K,isInteger:function isInteger(e){return K(e)&&se.ToInteger(e)===e},isSafeInteger:function isSafeInteger(e){return Number.isInteger(e)&&k(e)<=Number.MAX_SAFE_INTEGER},isNaN:X});h(Number,"parseInt",S.parseInt,Number.parseInt!==S.parseInt);if([,1].find(function(){return true})===1){re(Array.prototype,"find",Qe.find)}if([,1].findIndex(function(){return true})!==0){re(Array.prototype,"findIndex",Qe.findIndex)}var Pt=Function.bind.call(Function.bind,Object.prototype.propertyIsEnumerable);var Ct=function ensureEnumerable(e,t){if(s&&Pt(e,t)){Object.defineProperty(e,t,{enumerable:false})}};var Mt=function sliceArgs(){var e=Number(this);var t=arguments.length;var r=t-e;var n=new Array(r<0?0:r);for(var o=e;o<t;++o){n[o-e]=arguments[o]}return n};var xt=function assignTo(e){return function assignToSource(t,r){t[r]=e[r];return t}};var Nt=function(e,t){var r=n(Object(t));var o;if(se.IsCallable(Object.getOwnPropertySymbols)){o=v(Object.getOwnPropertySymbols(Object(t)),Pt(t))}return p(P(r,o||[]),xt(t),e)};var At={assign:function(e,t){var r=se.ToObject(e,"Cannot convert undefined or null to object");return p(se.Call(Mt,1,arguments),Nt,r)},is:function is(e,t){return se.SameValue(e,t)}};var Rt=Object.assign&&Object.preventExtensions&&function(){var e=Object.preventExtensions({1:2});try{Object.assign(e,"xy")}catch(t){return e[1]==="y"}}();if(Rt){re(Object,"assign",At.assign)}b(Object,At);if(s){var _t={setPrototypeOf:function(e,r){var n;var o=function(e,t){if(!se.TypeIsObject(e)){throw new TypeError("cannot set prototype on a non-object")}if(!(t===null||se.TypeIsObject(t))){throw new TypeError("can only set prototype to an object or null"+t)}};var i=function(e,r){o(e,r);t(n,e,r);return e};try{n=e.getOwnPropertyDescriptor(e.prototype,r).set;t(n,{},null)}catch(t){if(e.prototype!=={}[r]){return}n=function(e){this[r]=e};i.polyfill=i(i({},null),e.prototype)instanceof e}return i}(Object,"__proto__")};b(Object,_t)}if(Object.setPrototypeOf&&Object.getPrototypeOf&&Object.getPrototypeOf(Object.setPrototypeOf({},null))!==null&&Object.getPrototypeOf(Object.create(null))===null){(function(){var e=Object.create(null);var t=Object.getPrototypeOf;var r=Object.setPrototypeOf;Object.getPrototypeOf=function(r){var n=t(r);return n===e?null:n};Object.setPrototypeOf=function(t,n){var o=n===null?e:n;return r(t,o)};Object.setPrototypeOf.polyfill=false})()}var kt=!i(function(){Object.keys("foo")});if(!kt){var Ft=Object.keys;re(Object,"keys",function keys(e){return Ft(se.ToObject(e))});n=Object.keys}var Lt=i(function(){Object.keys(/a/g)});if(Lt){var Dt=Object.keys;re(Object,"keys",function keys(e){if(te.regex(e)){var t=[];for(var r in e){if(z(e,r)){M(t,r)}}return t}return Dt(e)});n=Object.keys}if(Object.getOwnPropertyNames){var zt=!i(function(){Object.getOwnPropertyNames("foo")});if(!zt){var qt=typeof window==="object"?Object.getOwnPropertyNames(window):[];var Wt=Object.getOwnPropertyNames;re(Object,"getOwnPropertyNames",function getOwnPropertyNames(e){var t=se.ToObject(e);if(g(t)==="[object Window]"){try{return Wt(t)}catch(e){return P([],qt)}}return Wt(t)})}}if(Object.getOwnPropertyDescriptor){var Gt=!i(function(){Object.getOwnPropertyDescriptor("foo","bar")});if(!Gt){var Ht=Object.getOwnPropertyDescriptor;re(Object,"getOwnPropertyDescriptor",function getOwnPropertyDescriptor(e,t){return Ht(se.ToObject(e),t)})}}if(Object.seal){var Vt=!i(function(){Object.seal("foo")});if(!Vt){var Bt=Object.seal;re(Object,"seal",function seal(e){if(!se.TypeIsObject(e)){return e}return Bt(e)})}}if(Object.isSealed){var Ut=!i(function(){Object.isSealed("foo")});if(!Ut){var $t=Object.isSealed;re(Object,"isSealed",function isSealed(e){if(!se.TypeIsObject(e)){return true}return $t(e)})}}if(Object.freeze){var Jt=!i(function(){Object.freeze("foo")});if(!Jt){var Xt=Object.freeze;re(Object,"freeze",function freeze(e){if(!se.TypeIsObject(e)){return e}return Xt(e)})}}if(Object.isFrozen){var Kt=!i(function(){Object.isFrozen("foo")});if(!Kt){var Zt=Object.isFrozen;re(Object,"isFrozen",function isFrozen(e){if(!se.TypeIsObject(e)){return true}return Zt(e)})}}if(Object.preventExtensions){var Yt=!i(function(){Object.preventExtensions("foo")});if(!Yt){var Qt=Object.preventExtensions;re(Object,"preventExtensions",function preventExtensions(e){if(!se.TypeIsObject(e)){return e}return Qt(e)})}}if(Object.isExtensible){var er=!i(function(){Object.isExtensible("foo")});if(!er){var tr=Object.isExtensible;re(Object,"isExtensible",function isExtensible(e){if(!se.TypeIsObject(e)){return false}return tr(e)})}}if(Object.getPrototypeOf){var rr=!i(function(){Object.getPrototypeOf("foo")});if(!rr){var nr=Object.getPrototypeOf;re(Object,"getPrototypeOf",function getPrototypeOf(e){return nr(se.ToObject(e))})}}var or=s&&function(){var e=Object.getOwnPropertyDescriptor(RegExp.prototype,"flags");return e&&se.IsCallable(e.get)}();if(s&&!or){var ir=function flags(){if(!se.TypeIsObject(this)){throw new TypeError("Method called on incompatible type: must be an object.")}var e="";if(this.global){e+="g"}if(this.ignoreCase){e+="i"}if(this.multiline){e+="m"}if(this.unicode){e+="u"}if(this.sticky){e+="y"}return e};m.getter(RegExp.prototype,"flags",ir)}var ar=s&&a(function(){return String(new RegExp(/a/g,"i"))==="/a/i"});var ur=ne&&s&&function(){var e=/./;e[$.match]=false;return RegExp(e)===e}();var fr=a(function(){return RegExp.prototype.toString.call({source:"abc"})==="/abc/"});var sr=fr&&a(function(){return RegExp.prototype.toString.call({source:"a",flags:"b"})==="/a/b"});if(!fr||!sr){var cr=RegExp.prototype.toString;h(RegExp.prototype,"toString",function toString(){var e=se.RequireObjectCoercible(this);if(te.regex(e)){return t(cr,e)}var r=ae(e.source);var n=ae(e.flags);return"/"+r+"/"+n},true);m.preserveToString(RegExp.prototype.toString,cr)}if(s&&(!ar||ur)){var lr=Object.getOwnPropertyDescriptor(RegExp.prototype,"flags").get;var pr=Object.getOwnPropertyDescriptor(RegExp.prototype,"source")||{};var vr=function(){return this.source};var yr=se.IsCallable(pr.get)?pr.get:vr;var hr=RegExp;var br=function(){return function RegExp(e,t){var r=se.IsRegExp(e);var n=this instanceof RegExp;if(!n&&r&&typeof t==="undefined"&&e.constructor===RegExp){return e}var o=e;var i=t;if(te.regex(e)){o=se.Call(yr,e);i=typeof t==="undefined"?se.Call(lr,e):t;return new RegExp(o,i)}else if(r){o=e.source;i=typeof t==="undefined"?e.flags:t}return new hr(e,t)}}();Ie(hr,br,{$input:true});RegExp=br;m.redefine(S,"RegExp",br)}if(s){var gr={input:"$_",lastMatch:"$&",lastParen:"$+",leftContext:"$`",rightContext:"$'"};l(n(gr),function(e){if(e in RegExp&&!(gr[e]in RegExp)){m.getter(RegExp,gr[e],function get(){return RegExp[e]})}})}Pe(RegExp);var dr=1/Number.EPSILON;var mr=function roundTiesToEven(e){return e+dr-dr};var Or=Math.pow(2,-23);var wr=Math.pow(2,127)*(2-Or);var jr=Math.pow(2,-126);var Sr=Math.E;var Tr=Math.LOG2E;var Ir=Math.LOG10E;var Er=Number.prototype.clz;delete Number.prototype.clz;var Pr={acosh:function acosh(e){var t=Number(e);if(X(t)||e<1){return NaN}if(t===1){return 0}if(t===Infinity){return t}return L(t/Sr+D(t+1)*D(t-1)/Sr)+1},asinh:function asinh(e){var t=Number(e);if(t===0||!T(t)){return t}return t<0?-asinh(-t):L(t+D(t*t+1))},atanh:function atanh(e){var t=Number(e);if(X(t)||t<-1||t>1){return NaN}if(t===-1){return-Infinity}if(t===1){return Infinity}if(t===0){return t}return.5*L((1+t)/(1-t))},cbrt:function cbrt(e){var t=Number(e);if(t===0){return t}var r=t<0;var n;if(r){t=-t}if(t===Infinity){n=Infinity}else{n=F(L(t)/3);n=(t/(n*n)+2*n)/3}return r?-n:n},clz32:function clz32(e){var t=Number(e);var r=se.ToUint32(t);if(r===0){return 32}return Er?se.Call(Er,r):31-_(L(r+.5)*Tr)},cosh:function cosh(e){var t=Number(e);if(t===0){return 1}if(X(t)){return NaN}if(!T(t)){return Infinity}if(t<0){t=-t}if(t>21){return F(t)/2}return(F(t)+F(-t))/2},expm1:function expm1(e){var t=Number(e);if(t===-Infinity){return-1}if(!T(t)||t===0){return t}if(k(t)>.5){return F(t)-1}var r=t;var n=0;var o=1;while(n+r!==n){n+=r;o+=1;r*=t/o}return n},hypot:function hypot(e,t){var r=0;var n=0;for(var o=0;o<arguments.length;++o){var i=k(Number(arguments[o]));if(n<i){r*=n/i*(n/i);r+=1;n=i}else{r+=i>0?i/n*(i/n):i}}return n===Infinity?Infinity:n*D(r)},log2:function log2(e){return L(e)*Tr},log10:function log10(e){return L(e)*Ir},log1p:function log1p(e){var t=Number(e);if(t<-1||X(t)){return NaN}if(t===0||t===Infinity){return t}if(t===-1){return-Infinity}return 1+t-1===0?t:t*(L(1+t)/(1+t-1))},sign:Z,sinh:function sinh(e){var t=Number(e);if(!T(t)||t===0){return t}if(k(t)<1){return(Math.expm1(t)-Math.expm1(-t))/2}return(F(t-1)-F(-t-1))*Sr/2},tanh:function tanh(e){var t=Number(e);if(X(t)||t===0){return t}if(t>=20){return 1}if(t<=-20){return-1}return(Math.expm1(t)-Math.expm1(-t))/(F(t)+F(-t))},trunc:function trunc(e){var t=Number(e);return t<0?-_(-t):_(t)},imul:function imul(e,t){var r=se.ToUint32(e);var n=se.ToUint32(t);var o=r>>>16&65535;var i=r&65535;var a=n>>>16&65535;var u=n&65535;return i*u+(o*u+i*a<<16>>>0)|0},fround:function fround(e){var t=Number(e);if(t===0||t===Infinity||t===-Infinity||X(t)){return t}var r=Z(t);var n=k(t);if(n<jr){return r*mr(n/jr/Or)*jr*Or}var o=(1+Or/Number.EPSILON)*n;var i=o-(o-n);if(i>wr||X(i)){return r*Infinity}return r*i}};b(Math,Pr);h(Math,"log1p",Pr.log1p,Math.log1p(-1e-17)!==-1e-17);h(Math,"asinh",Pr.asinh,Math.asinh(-1e7)!==-Math.asinh(1e7));h(Math,"tanh",Pr.tanh,Math.tanh(-2e-17)!==-2e-17);h(Math,"acosh",Pr.acosh,Math.acosh(Number.MAX_VALUE)===Infinity);h(Math,"cbrt",Pr.cbrt,Math.abs(1-Math.cbrt(1e-300)/1e-100)/Number.EPSILON>8);h(Math,"sinh",Pr.sinh,Math.sinh(-2e-17)!==-2e-17);var Cr=Math.expm1(10);h(Math,"expm1",Pr.expm1,Cr>22025.465794806718||Cr<22025.465794806718);var Mr=Math.round;var xr=Math.round(.5-Number.EPSILON/4)===0&&Math.round(-.5+Number.EPSILON/3.99)===1;var Nr=dr+1;var Ar=2*dr-1;var Rr=[Nr,Ar].every(function(e){return Math.round(e)===e});h(Math,"round",function round(e){var t=_(e);var r=t===-1?-0:t+1;
    return e-t<.5?t:r},!xr||!Rr);m.preserveToString(Math.round,Mr);var _r=Math.imul;if(Math.imul(4294967295,5)!==-5){Math.imul=Pr.imul;m.preserveToString(Math.imul,_r)}if(Math.imul.length!==2){re(Math,"imul",function imul(e,t){return se.Call(_r,Math,arguments)})}var kr=function(){var e=S.setTimeout;if(typeof e!=="function"&&typeof e!=="object"){return}se.IsPromise=function(e){if(!se.TypeIsObject(e)){return false}if(typeof e._promise==="undefined"){return false}return true};var r=function(e){if(!se.IsConstructor(e)){throw new TypeError("Bad promise constructor")}var t=this;var r=function(e,r){if(t.resolve!==void 0||t.reject!==void 0){throw new TypeError("Bad Promise implementation!")}t.resolve=e;t.reject=r};t.resolve=void 0;t.reject=void 0;t.promise=new e(r);if(!(se.IsCallable(t.resolve)&&se.IsCallable(t.reject))){throw new TypeError("Bad promise constructor")}};var n;if(typeof window!=="undefined"&&se.IsCallable(window.postMessage)){n=function(){var e=[];var t="zero-timeout-message";var r=function(r){M(e,r);window.postMessage(t,"*")};var n=function(r){if(r.source===window&&r.data===t){r.stopPropagation();if(e.length===0){return}var n=N(e);n()}};window.addEventListener("message",n,true);return r}}var o=function(){var e=S.Promise;var t=e&&e.resolve&&e.resolve();return t&&function(e){return t.then(e)}};var i=se.IsCallable(S.setImmediate)?S.setImmediate:typeof process==="object"&&process.nextTick?process.nextTick:o()||(se.IsCallable(n)?n():function(t){e(t,0)});var a=function(e){return e};var u=function(e){throw e};var f=0;var s=1;var c=2;var l=0;var p=1;var v=2;var y={};var h=function(e,t,r){i(function(){g(e,t,r)})};var g=function(e,t,r){var n,o;if(t===y){return e(r)}try{n=e(r);o=t.resolve}catch(e){n=e;o=t.reject}o(n)};var d=function(e,t){var r=e._promise;var n=r.reactionLength;if(n>0){h(r.fulfillReactionHandler0,r.reactionCapability0,t);r.fulfillReactionHandler0=void 0;r.rejectReactions0=void 0;r.reactionCapability0=void 0;if(n>1){for(var o=1,i=0;o<n;o++,i+=3){h(r[i+l],r[i+v],t);e[i+l]=void 0;e[i+p]=void 0;e[i+v]=void 0}}}r.result=t;r.state=s;r.reactionLength=0};var m=function(e,t){var r=e._promise;var n=r.reactionLength;if(n>0){h(r.rejectReactionHandler0,r.reactionCapability0,t);r.fulfillReactionHandler0=void 0;r.rejectReactions0=void 0;r.reactionCapability0=void 0;if(n>1){for(var o=1,i=0;o<n;o++,i+=3){h(r[i+p],r[i+v],t);e[i+l]=void 0;e[i+p]=void 0;e[i+v]=void 0}}}r.result=t;r.state=c;r.reactionLength=0};var O=function(e){var t=false;var r=function(r){var n;if(t){return}t=true;if(r===e){return m(e,new TypeError("Self resolution"))}if(!se.TypeIsObject(r)){return d(e,r)}try{n=r.then}catch(t){return m(e,t)}if(!se.IsCallable(n)){return d(e,r)}i(function(){j(e,r,n)})};var n=function(r){if(t){return}t=true;return m(e,r)};return{resolve:r,reject:n}};var w=function(e,r,n,o){if(e===I){t(e,r,n,o,y)}else{t(e,r,n,o)}};var j=function(e,t,r){var n=O(e);var o=n.resolve;var i=n.reject;try{w(r,t,o,i)}catch(e){i(e)}};var T,I;var E=function(){var e=function Promise(t){if(!(this instanceof e)){throw new TypeError('Constructor Promise requires "new"')}if(this&&this._promise){throw new TypeError("Bad construction")}if(!se.IsCallable(t)){throw new TypeError("not a valid resolver")}var r=Ne(this,e,T,{_promise:{result:void 0,state:f,reactionLength:0,fulfillReactionHandler0:void 0,rejectReactionHandler0:void 0,reactionCapability0:void 0}});var n=O(r);var o=n.reject;try{t(n.resolve,o)}catch(e){o(e)}return r};return e}();T=E.prototype;var P=function(e,t,r,n){var o=false;return function(i){if(o){return}o=true;t[e]=i;if(--n.count===0){var a=r.resolve;a(t)}}};var C=function(e,t,r){var n=e.iterator;var o=[];var i={count:1};var a,u;var f=0;while(true){try{a=se.IteratorStep(n);if(a===false){e.done=true;break}u=a.value}catch(t){e.done=true;throw t}o[f]=void 0;var s=t.resolve(u);var c=P(f,o,r,i);i.count+=1;w(s.then,s,c,r.reject);f+=1}if(--i.count===0){var l=r.resolve;l(o)}return r.promise};var x=function(e,t,r){var n=e.iterator;var o,i,a;while(true){try{o=se.IteratorStep(n);if(o===false){e.done=true;break}i=o.value}catch(t){e.done=true;throw t}a=t.resolve(i);w(a.then,a,r.resolve,r.reject)}return r.promise};b(E,{all:function all(e){var t=this;if(!se.TypeIsObject(t)){throw new TypeError("Promise is not object")}var n=new r(t);var o,i;try{o=se.GetIterator(e);i={iterator:o,done:false};return C(i,t,n)}catch(e){var a=e;if(i&&!i.done){try{se.IteratorClose(o,true)}catch(e){a=e}}var u=n.reject;u(a);return n.promise}},race:function race(e){var t=this;if(!se.TypeIsObject(t)){throw new TypeError("Promise is not object")}var n=new r(t);var o,i;try{o=se.GetIterator(e);i={iterator:o,done:false};return x(i,t,n)}catch(e){var a=e;if(i&&!i.done){try{se.IteratorClose(o,true)}catch(e){a=e}}var u=n.reject;u(a);return n.promise}},reject:function reject(e){var t=this;if(!se.TypeIsObject(t)){throw new TypeError("Bad promise constructor")}var n=new r(t);var o=n.reject;o(e);return n.promise},resolve:function resolve(e){var t=this;if(!se.TypeIsObject(t)){throw new TypeError("Bad promise constructor")}if(se.IsPromise(e)){var n=e.constructor;if(n===t){return e}}var o=new r(t);var i=o.resolve;i(e);return o.promise}});b(T,{catch:function(e){return this.then(null,e)},then:function then(e,t){var n=this;if(!se.IsPromise(n)){throw new TypeError("not a promise")}var o=se.SpeciesConstructor(n,E);var i;var b=arguments.length>2&&arguments[2]===y;if(b&&o===E){i=y}else{i=new r(o)}var g=se.IsCallable(e)?e:a;var d=se.IsCallable(t)?t:u;var m=n._promise;var O;if(m.state===f){if(m.reactionLength===0){m.fulfillReactionHandler0=g;m.rejectReactionHandler0=d;m.reactionCapability0=i}else{var w=3*(m.reactionLength-1);m[w+l]=g;m[w+p]=d;m[w+v]=i}m.reactionLength+=1}else if(m.state===s){O=m.result;h(g,i,O)}else if(m.state===c){O=m.result;h(d,i,O)}else{throw new TypeError("unexpected Promise state")}return i.promise}});y=new r(E);I=T.then;return E}();if(S.Promise){delete S.Promise.accept;delete S.Promise.defer;delete S.Promise.prototype.chain}if(typeof kr==="function"){b(S,{Promise:kr});var Fr=w(S.Promise,function(e){return e.resolve(42).then(function(){})instanceof e});var Lr=!i(function(){S.Promise.reject(42).then(null,5).then(null,W)});var Dr=i(function(){S.Promise.call(3,W)});var zr=function(e){var t=e.resolve(5);t.constructor={};var r=e.resolve(t);try{r.then(null,W).then(null,W)}catch(e){return true}return t===r}(S.Promise);var qr=s&&function(){var e=0;var t=Object.defineProperty({},"then",{get:function(){e+=1}});Promise.resolve(t);return e===1}();var Wr=function BadResolverPromise(e){var t=new Promise(e);e(3,function(){});this.then=t.then;this.constructor=BadResolverPromise};Wr.prototype=Promise.prototype;Wr.all=Promise.all;var Gr=a(function(){return!!Wr.all([1,2])});if(!Fr||!Lr||!Dr||zr||!qr||Gr){Promise=kr;re(S,"Promise",kr)}if(Promise.all.length!==1){var Hr=Promise.all;re(Promise,"all",function all(e){return se.Call(Hr,this,arguments)})}if(Promise.race.length!==1){var Vr=Promise.race;re(Promise,"race",function race(e){return se.Call(Vr,this,arguments)})}if(Promise.resolve.length!==1){var Br=Promise.resolve;re(Promise,"resolve",function resolve(e){return se.Call(Br,this,arguments)})}if(Promise.reject.length!==1){var Ur=Promise.reject;re(Promise,"reject",function reject(e){return se.Call(Ur,this,arguments)})}Ct(Promise,"all");Ct(Promise,"race");Ct(Promise,"resolve");Ct(Promise,"reject");Pe(Promise)}var $r=function(e){var t=n(p(e,function(e,t){e[t]=true;return e},{}));return e.join(":")===t.join(":")};var Jr=$r(["z","a","bb"]);var Xr=$r(["z",1,"a","3",2]);if(s){var Kr=function fastkey(e,t){if(!t&&!Jr){return null}if(fe(e)){return"^"+se.ToString(e)}else if(typeof e==="string"){return"$"+e}else if(typeof e==="number"){if(!Xr){return"n"+e}return e}else if(typeof e==="boolean"){return"b"+e}return null};var Zr=function emptyObject(){return Object.create?Object.create(null):{}};var Yr=function addIterableToMap(e,n,o){if(r(o)||te.string(o)){l(o,function(e){if(!se.TypeIsObject(e)){throw new TypeError("Iterator value "+e+" is not an entry object")}n.set(e[0],e[1])})}else if(o instanceof e){t(e.prototype.forEach,o,function(e,t){n.set(t,e)})}else{var i,a;if(!fe(o)){a=n.set;if(!se.IsCallable(a)){throw new TypeError("bad map")}i=se.GetIterator(o)}if(typeof i!=="undefined"){while(true){var u=se.IteratorStep(i);if(u===false){break}var f=u.value;try{if(!se.TypeIsObject(f)){throw new TypeError("Iterator value "+f+" is not an entry object")}t(a,n,f[0],f[1])}catch(e){se.IteratorClose(i,true);throw e}}}}};var Qr=function addIterableToSet(e,n,o){if(r(o)||te.string(o)){l(o,function(e){n.add(e)})}else if(o instanceof e){t(e.prototype.forEach,o,function(e){n.add(e)})}else{var i,a;if(!fe(o)){a=n.add;if(!se.IsCallable(a)){throw new TypeError("bad set")}i=se.GetIterator(o)}if(typeof i!=="undefined"){while(true){var u=se.IteratorStep(i);if(u===false){break}var f=u.value;try{t(a,n,f)}catch(e){se.IteratorClose(i,true);throw e}}}}};var en={Map:function(){var e={};var r=function MapEntry(e,t){this.key=e;this.value=t;this.next=null;this.prev=null};r.prototype.isRemoved=function isRemoved(){return this.key===e};var n=function isMap(e){return!!e._es6map};var o=function requireMapSlot(e,t){if(!se.TypeIsObject(e)||!n(e)){throw new TypeError("Method Map.prototype."+t+" called on incompatible receiver "+se.ToString(e))}};var i=function MapIterator(e,t){o(e,"[[MapIterator]]");this.head=e._head;this.i=this.head;this.kind=t};i.prototype={next:function next(){var e=this.i;var t=this.kind;var r=this.head;if(typeof this.i==="undefined"){return Xe()}while(e.isRemoved()&&e!==r){e=e.prev}var n;while(e.next!==r){e=e.next;if(!e.isRemoved()){if(t==="key"){n=e.key}else if(t==="value"){n=e.value}else{n=[e.key,e.value]}this.i=e;return Xe(n)}}this.i=void 0;return Xe()}};Ce(i.prototype);var a;var u=function Map(){if(!(this instanceof Map)){throw new TypeError('Constructor Map requires "new"')}if(this&&this._es6map){throw new TypeError("Bad construction")}var e=Ne(this,Map,a,{_es6map:true,_head:null,_map:G?new G:null,_size:0,_storage:Zr()});var t=new r(null,null);t.next=t.prev=t;e._head=t;if(arguments.length>0){Yr(Map,e,arguments[0])}return e};a=u.prototype;m.getter(a,"size",function(){if(typeof this._size==="undefined"){throw new TypeError("size method called on incompatible Map")}return this._size});b(a,{get:function get(e){o(this,"get");var t;var r=Kr(e,true);if(r!==null){t=this._storage[r];if(t){return t.value}else{return}}if(this._map){t=V.call(this._map,e);if(t){return t.value}else{return}}var n=this._head;var i=n;while((i=i.next)!==n){if(se.SameValueZero(i.key,e)){return i.value}}},has:function has(e){o(this,"has");var t=Kr(e,true);if(t!==null){return typeof this._storage[t]!=="undefined"}if(this._map){return B.call(this._map,e)}var r=this._head;var n=r;while((n=n.next)!==r){if(se.SameValueZero(n.key,e)){return true}}return false},set:function set(e,t){o(this,"set");var n=this._head;var i=n;var a;var u=Kr(e,true);if(u!==null){if(typeof this._storage[u]!=="undefined"){this._storage[u].value=t;return this}else{a=this._storage[u]=new r(e,t);i=n.prev}}else if(this._map){if(B.call(this._map,e)){V.call(this._map,e).value=t}else{a=new r(e,t);U.call(this._map,e,a);i=n.prev}}while((i=i.next)!==n){if(se.SameValueZero(i.key,e)){i.value=t;return this}}a=a||new r(e,t);if(se.SameValue(-0,e)){a.key=+0}a.next=this._head;a.prev=this._head.prev;a.prev.next=a;a.next.prev=a;this._size+=1;return this},delete:function(t){o(this,"delete");var r=this._head;var n=r;var i=Kr(t,true);if(i!==null){if(typeof this._storage[i]==="undefined"){return false}n=this._storage[i].prev;delete this._storage[i]}else if(this._map){if(!B.call(this._map,t)){return false}n=V.call(this._map,t).prev;H.call(this._map,t)}while((n=n.next)!==r){if(se.SameValueZero(n.key,t)){n.key=e;n.value=e;n.prev.next=n.next;n.next.prev=n.prev;this._size-=1;return true}}return false},clear:function clear(){o(this,"clear");this._map=G?new G:null;this._size=0;this._storage=Zr();var t=this._head;var r=t;var n=r.next;while((r=n)!==t){r.key=e;r.value=e;n=r.next;r.next=r.prev=t}t.next=t.prev=t},keys:function keys(){o(this,"keys");return new i(this,"key")},values:function values(){o(this,"values");return new i(this,"value")},entries:function entries(){o(this,"entries");return new i(this,"key+value")},forEach:function forEach(e){o(this,"forEach");var r=arguments.length>1?arguments[1]:null;var n=this.entries();for(var i=n.next();!i.done;i=n.next()){if(r){t(e,r,i.value[1],i.value[0],this)}else{e(i.value[1],i.value[0],this)}}}});Ce(a,a.entries);return u}(),Set:function(){var e=function isSet(e){return e._es6set&&typeof e._storage!=="undefined"};var r=function requireSetSlot(t,r){if(!se.TypeIsObject(t)||!e(t)){throw new TypeError("Set.prototype."+r+" called on incompatible receiver "+se.ToString(t))}};var o;var i=function Set(){if(!(this instanceof Set)){throw new TypeError('Constructor Set requires "new"')}if(this&&this._es6set){throw new TypeError("Bad construction")}var e=Ne(this,Set,o,{_es6set:true,"[[SetData]]":null,_storage:Zr()});if(!e._es6set){throw new TypeError("bad set")}if(arguments.length>0){Qr(Set,e,arguments[0])}return e};o=i.prototype;var a=function(e){var t=e;if(t==="^null"){return null}else if(t==="^undefined"){return void 0}else{var r=t.charAt(0);if(r==="$"){return C(t,1)}else if(r==="n"){return+C(t,1)}else if(r==="b"){return t==="btrue"}}return+t};var u=function ensureMap(e){if(!e["[[SetData]]"]){var t=new en.Map;e["[[SetData]]"]=t;l(n(e._storage),function(e){var r=a(e);t.set(r,r)});e["[[SetData]]"]=t}e._storage=null};m.getter(i.prototype,"size",function(){r(this,"size");if(this._storage){return n(this._storage).length}u(this);return this["[[SetData]]"].size});b(i.prototype,{has:function has(e){r(this,"has");var t;if(this._storage&&(t=Kr(e))!==null){return!!this._storage[t]}u(this);return this["[[SetData]]"].has(e)},add:function add(e){r(this,"add");var t;if(this._storage&&(t=Kr(e))!==null){this._storage[t]=true;return this}u(this);this["[[SetData]]"].set(e,e);return this},delete:function(e){r(this,"delete");var t;if(this._storage&&(t=Kr(e))!==null){var n=z(this._storage,t);return delete this._storage[t]&&n}u(this);return this["[[SetData]]"]["delete"](e)},clear:function clear(){r(this,"clear");if(this._storage){this._storage=Zr()}if(this["[[SetData]]"]){this["[[SetData]]"].clear()}},values:function values(){r(this,"values");u(this);return this["[[SetData]]"].values()},entries:function entries(){r(this,"entries");u(this);return this["[[SetData]]"].entries()},forEach:function forEach(e){r(this,"forEach");var n=arguments.length>1?arguments[1]:null;var o=this;u(o);this["[[SetData]]"].forEach(function(r,i){if(n){t(e,n,i,i,o)}else{e(i,i,o)}})}});h(i.prototype,"keys",i.prototype.values,true);Ce(i.prototype,i.prototype.values);return i}()};if(S.Map||S.Set){var tn=a(function(){return new Map([[1,2]]).get(1)===2});if(!tn){S.Map=function Map(){if(!(this instanceof Map)){throw new TypeError('Constructor Map requires "new"')}var e=new G;if(arguments.length>0){Yr(Map,e,arguments[0])}delete e.constructor;Object.setPrototypeOf(e,S.Map.prototype);return e};S.Map.prototype=O(G.prototype);h(S.Map.prototype,"constructor",S.Map,true);m.preserveToString(S.Map,G)}var rn=new Map;var nn=function(){var e=new Map([[1,0],[2,0],[3,0],[4,0]]);e.set(-0,e);return e.get(0)===e&&e.get(-0)===e&&e.has(0)&&e.has(-0)}();var on=rn.set(1,2)===rn;if(!nn||!on){re(Map.prototype,"set",function set(e,r){t(U,this,e===0?0:e,r);return this})}if(!nn){b(Map.prototype,{get:function get(e){return t(V,this,e===0?0:e)},has:function has(e){return t(B,this,e===0?0:e)}},true);m.preserveToString(Map.prototype.get,V);m.preserveToString(Map.prototype.has,B)}var an=new Set;var un=function(e){e["delete"](0);e.add(-0);return!e.has(0)}(an);var fn=an.add(1)===an;if(!un||!fn){var sn=Set.prototype.add;Set.prototype.add=function add(e){t(sn,this,e===0?0:e);return this};m.preserveToString(Set.prototype.add,sn)}if(!un){var cn=Set.prototype.has;Set.prototype.has=function has(e){return t(cn,this,e===0?0:e)};m.preserveToString(Set.prototype.has,cn);var ln=Set.prototype["delete"];Set.prototype["delete"]=function SetDelete(e){return t(ln,this,e===0?0:e)};m.preserveToString(Set.prototype["delete"],ln)}var pn=w(S.Map,function(e){var t=new e([]);t.set(42,42);return t instanceof e});var vn=Object.setPrototypeOf&&!pn;var yn=function(){try{return!(S.Map()instanceof S.Map)}catch(e){return e instanceof TypeError}}();if(S.Map.length!==0||vn||!yn){S.Map=function Map(){if(!(this instanceof Map)){throw new TypeError('Constructor Map requires "new"')}var e=new G;if(arguments.length>0){Yr(Map,e,arguments[0])}delete e.constructor;Object.setPrototypeOf(e,Map.prototype);return e};S.Map.prototype=G.prototype;h(S.Map.prototype,"constructor",S.Map,true);m.preserveToString(S.Map,G)}var hn=w(S.Set,function(e){var t=new e([]);t.add(42,42);return t instanceof e});var bn=Object.setPrototypeOf&&!hn;var gn=function(){try{return!(S.Set()instanceof S.Set)}catch(e){return e instanceof TypeError}}();if(S.Set.length!==0||bn||!gn){var dn=S.Set;S.Set=function Set(){if(!(this instanceof Set)){throw new TypeError('Constructor Set requires "new"')}var e=new dn;if(arguments.length>0){Qr(Set,e,arguments[0])}delete e.constructor;Object.setPrototypeOf(e,Set.prototype);return e};S.Set.prototype=dn.prototype;h(S.Set.prototype,"constructor",S.Set,true);m.preserveToString(S.Set,dn)}var mn=new S.Map;var On=!a(function(){return mn.keys().next().done});if(typeof S.Map.prototype.clear!=="function"||(new S.Set).size!==0||mn.size!==0||typeof S.Map.prototype.keys!=="function"||typeof S.Set.prototype.keys!=="function"||typeof S.Map.prototype.forEach!=="function"||typeof S.Set.prototype.forEach!=="function"||u(S.Map)||u(S.Set)||typeof mn.keys().next!=="function"||On||!pn){b(S,{Map:en.Map,Set:en.Set},true)}if(S.Set.prototype.keys!==S.Set.prototype.values){h(S.Set.prototype,"keys",S.Set.prototype.values,true)}Ce(Object.getPrototypeOf((new S.Map).keys()));Ce(Object.getPrototypeOf((new S.Set).keys()));if(c&&S.Set.prototype.has.name!=="has"){var wn=S.Set.prototype.has;re(S.Set.prototype,"has",function has(e){return t(wn,this,e)})}}b(S,en);Pe(S.Map);Pe(S.Set)}var jn=function throwUnlessTargetIsObject(e){if(!se.TypeIsObject(e)){throw new TypeError("target must be an object")}};var Sn={apply:function apply(){return se.Call(se.Call,null,arguments)},construct:function construct(e,t){if(!se.IsConstructor(e)){throw new TypeError("First argument must be a constructor.")}var r=arguments.length>2?arguments[2]:e;if(!se.IsConstructor(r)){throw new TypeError("new.target must be a constructor.")}return se.Construct(e,t,r,"internal")},deleteProperty:function deleteProperty(e,t){jn(e);if(s){var r=Object.getOwnPropertyDescriptor(e,t);if(r&&!r.configurable){return false}}return delete e[t]},has:function has(e,t){jn(e);return t in e}};if(Object.getOwnPropertyNames){Object.assign(Sn,{ownKeys:function ownKeys(e){jn(e);var t=Object.getOwnPropertyNames(e);if(se.IsCallable(Object.getOwnPropertySymbols)){x(t,Object.getOwnPropertySymbols(e))}return t}})}var Tn=function ConvertExceptionToBoolean(e){return!i(e)};if(Object.preventExtensions){Object.assign(Sn,{isExtensible:function isExtensible(e){jn(e);return Object.isExtensible(e)},preventExtensions:function preventExtensions(e){jn(e);return Tn(function(){Object.preventExtensions(e)})}})}if(s){var In=function get(e,t,r){var n=Object.getOwnPropertyDescriptor(e,t);if(!n){var o=Object.getPrototypeOf(e);if(o===null){return void 0}return In(o,t,r)}if("value"in n){return n.value}if(n.get){return se.Call(n.get,r)}return void 0};var En=function set(e,r,n,o){var i=Object.getOwnPropertyDescriptor(e,r);if(!i){var a=Object.getPrototypeOf(e);if(a!==null){return En(a,r,n,o)}i={value:void 0,writable:true,enumerable:true,configurable:true}}if("value"in i){if(!i.writable){return false}if(!se.TypeIsObject(o)){return false}var u=Object.getOwnPropertyDescriptor(o,r);if(u){return ie.defineProperty(o,r,{value:n})}else{return ie.defineProperty(o,r,{value:n,writable:true,enumerable:true,configurable:true})}}if(i.set){t(i.set,o,n);return true}return false};Object.assign(Sn,{defineProperty:function defineProperty(e,t,r){jn(e);return Tn(function(){Object.defineProperty(e,t,r)})},getOwnPropertyDescriptor:function getOwnPropertyDescriptor(e,t){jn(e);return Object.getOwnPropertyDescriptor(e,t)},get:function get(e,t){jn(e);var r=arguments.length>2?arguments[2]:e;return In(e,t,r)},set:function set(e,t,r){jn(e);var n=arguments.length>3?arguments[3]:e;return En(e,t,r,n)}})}if(Object.getPrototypeOf){var Pn=Object.getPrototypeOf;Sn.getPrototypeOf=function getPrototypeOf(e){jn(e);return Pn(e)}}if(Object.setPrototypeOf&&Sn.getPrototypeOf){var Cn=function(e,t){var r=t;while(r){if(e===r){return true}r=Sn.getPrototypeOf(r)}return false};Object.assign(Sn,{setPrototypeOf:function setPrototypeOf(e,t){jn(e);if(t!==null&&!se.TypeIsObject(t)){throw new TypeError("proto must be an object or null")}if(t===ie.getPrototypeOf(e)){return true}if(ie.isExtensible&&!ie.isExtensible(e)){return false}if(Cn(e,t)){return false}Object.setPrototypeOf(e,t);return true}})}var Mn=function(e,t){if(!se.IsCallable(S.Reflect[e])){h(S.Reflect,e,t)}else{var r=a(function(){S.Reflect[e](1);S.Reflect[e](NaN);S.Reflect[e](true);return true});if(r){re(S.Reflect,e,t)}}};Object.keys(Sn).forEach(function(e){Mn(e,Sn[e])});var xn=S.Reflect.getPrototypeOf;if(c&&xn&&xn.name!=="getPrototypeOf"){re(S.Reflect,"getPrototypeOf",function getPrototypeOf(e){return t(xn,S.Reflect,e)})}if(S.Reflect.setPrototypeOf){if(a(function(){S.Reflect.setPrototypeOf(1,{});return true})){re(S.Reflect,"setPrototypeOf",Sn.setPrototypeOf)}}if(S.Reflect.defineProperty){if(!a(function(){var e=!S.Reflect.defineProperty(1,"test",{value:1});var t=typeof Object.preventExtensions!=="function"||!S.Reflect.defineProperty(Object.preventExtensions({}),"test",{});return e&&t})){re(S.Reflect,"defineProperty",Sn.defineProperty)}}if(S.Reflect.construct){if(!a(function(){var e=function F(){};return S.Reflect.construct(function(){},[],e)instanceof e})){re(S.Reflect,"construct",Sn.construct)}}if(String(new Date(NaN))!=="Invalid Date"){var Nn=Date.prototype.toString;var An=function toString(){var e=+this;if(e!==e){return"Invalid Date"}return se.Call(Nn,this)};re(Date.prototype,"toString",An)}var Rn={anchor:function anchor(e){return se.CreateHTML(this,"a","name",e)},big:function big(){return se.CreateHTML(this,"big","","")},blink:function blink(){return se.CreateHTML(this,"blink","","")},bold:function bold(){return se.CreateHTML(this,"b","","")},fixed:function fixed(){return se.CreateHTML(this,"tt","","")},fontcolor:function fontcolor(e){return se.CreateHTML(this,"font","color",e)},fontsize:function fontsize(e){return se.CreateHTML(this,"font","size",e)},italics:function italics(){return se.CreateHTML(this,"i","","")},link:function link(e){return se.CreateHTML(this,"a","href",e)},small:function small(){return se.CreateHTML(this,"small","","")},strike:function strike(){return se.CreateHTML(this,"strike","","")},sub:function sub(){return se.CreateHTML(this,"sub","","")},sup:function sub(){return se.CreateHTML(this,"sup","","")}};l(Object.keys(Rn),function(e){var r=String.prototype[e];var n=false;if(se.IsCallable(r)){var o=t(r,"",' " ');var i=P([],o.match(/"/g)).length;n=o!==o.toLowerCase()||i>2}else{n=true}if(n){re(String.prototype,e,Rn[e])}});var _n=function(){if(!ne){return false}var e=typeof JSON==="object"&&typeof JSON.stringify==="function"?JSON.stringify:null;if(!e){return false}if(typeof e($())!=="undefined"){return true}if(e([$()])!=="[null]"){return true}var t={a:$()};t[$()]=true;if(e(t)!=="{}"){return true}return false}();var kn=a(function(){if(!ne){return true}return JSON.stringify(Object($()))==="{}"&&JSON.stringify([Object($())])==="[{}]"});if(_n||!kn){var Fn=JSON.stringify;re(JSON,"stringify",function stringify(e){if(typeof e==="symbol"){return}var n;if(arguments.length>1){n=arguments[1]}var o=[e];if(!r(n)){var i=se.IsCallable(n)?n:null;var a=function(e,r){var n=i?t(i,this,e,r):r;if(typeof n!=="symbol"){if(te.symbol(n)){return xt({})(n)}else{return n}}};o.push(a)}else{o.push(n)}if(arguments.length>2){o.push(arguments[2])}return Fn.apply(this,o)})}return S});
//# sourceMappingURL=es6-shim.map

/**
 * FormValidation (https://formvalidation.io), v1.9.0 (cbf8fab)
 * The best validation library for JavaScript
 * (c) 2013 - 2021 Nguyen Huu Phuoc <me@phuoc.ng>
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
  typeof define === 'function' && define.amd ? define(['exports'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.FormValidation = {}));
})(this, (function (exports) { 'use strict';

  function t$i(t) {
    var e = t.length;
    var l = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [0, 2, 4, 6, 8, 1, 3, 5, 7, 9]];
    var n = 0;
    var r = 0;

    while (e--) {
      r += l[n][parseInt(t.charAt(e), 10)];
      n = 1 - n;
    }

    return r % 10 === 0 && r > 0;
  }

  function t$h(t) {
    var e = t.length;
    var n = 5;

    for (var r = 0; r < e; r++) {
      n = ((n || 10) * 2 % 11 + parseInt(t.charAt(r), 10)) % 10;
    }

    return n === 1;
  }

  function t$g(t) {
    var e = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var n = t.length;
    var o = e.length;
    var l = Math.floor(o / 2);

    for (var r = 0; r < n; r++) {
      l = ((l || o) * 2 % (o + 1) + e.indexOf(t.charAt(r))) % o;
    }

    return l === 1;
  }

  function t$f(t) {
    var e = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [1, 2, 3, 4, 0, 6, 7, 8, 9, 5], [2, 3, 4, 0, 1, 7, 8, 9, 5, 6], [3, 4, 0, 1, 2, 8, 9, 5, 6, 7], [4, 0, 1, 2, 3, 9, 5, 6, 7, 8], [5, 9, 8, 7, 6, 0, 4, 3, 2, 1], [6, 5, 9, 8, 7, 1, 0, 4, 3, 2], [7, 6, 5, 9, 8, 2, 1, 0, 4, 3], [8, 7, 6, 5, 9, 3, 2, 1, 0, 4], [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]];
    var n = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [1, 5, 7, 6, 2, 8, 3, 0, 9, 4], [5, 8, 0, 3, 7, 9, 6, 1, 4, 2], [8, 9, 1, 6, 0, 4, 3, 5, 2, 7], [9, 4, 5, 3, 1, 2, 6, 8, 7, 0], [4, 2, 8, 6, 5, 7, 3, 9, 0, 1], [2, 7, 9, 3, 8, 0, 6, 4, 1, 5], [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]];
    var o = t.reverse();
    var r = 0;

    for (var _t = 0; _t < o.length; _t++) {
      r = e[r][n[_t % 8][o[_t]]];
    }

    return r === 0;
  }

  var index$3 = {
    luhn: t$i,
    mod11And10: t$h,
    mod37And36: t$g,
    verhoeff: t$f
  };

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }

    subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    if (superClass) _setPrototypeOf(subClass, superClass);
  }

  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }

  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };

    return _setPrototypeOf(o, p);
  }

  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;

    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }

  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }

    return self;
  }

  function _possibleConstructorReturn(self, call) {
    if (call && (typeof call === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }

    return _assertThisInitialized(self);
  }

  function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct();

    return function _createSuperInternal() {
      var Super = _getPrototypeOf(Derived),
          result;

      if (hasNativeReflectConstruct) {
        var NewTarget = _getPrototypeOf(this).constructor;

        result = Reflect.construct(Super, arguments, NewTarget);
      } else {
        result = Super.apply(this, arguments);
      }

      return _possibleConstructorReturn(this, result);
    };
  }

  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }

  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;

    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];

    return arr2;
  }

  function _createForOfIteratorHelper(o, allowArrayLike) {
    var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];

    if (!it) {
      if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
        if (it) o = it;
        var i = 0;

        var F = function () {};

        return {
          s: F,
          n: function () {
            if (i >= o.length) return {
              done: true
            };
            return {
              done: false,
              value: o[i++]
            };
          },
          e: function (e) {
            throw e;
          },
          f: F
        };
      }

      throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }

    var normalCompletion = true,
        didErr = false,
        err;
    return {
      s: function () {
        it = it.call(o);
      },
      n: function () {
        var step = it.next();
        normalCompletion = step.done;
        return step;
      },
      e: function (e) {
        didErr = true;
        err = e;
      },
      f: function () {
        try {
          if (!normalCompletion && it.return != null) it.return();
        } finally {
          if (didErr) throw err;
        }
      }
    };
  }

  function s$6() {
    return {
      fns: {},
      clear: function clear() {
        this.fns = {};
      },
      emit: function emit(s) {
        for (var _len = arguments.length, f = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          f[_key - 1] = arguments[_key];
        }

        (this.fns[s] || []).map(function (s) {
          return s.apply(s, f);
        });
      },
      off: function off(s, f) {
        if (this.fns[s]) {
          var n = this.fns[s].indexOf(f);

          if (n >= 0) {
            this.fns[s].splice(n, 1);
          }
        }
      },
      on: function on(s, f) {
        (this.fns[s] = this.fns[s] || []).push(f);
      }
    };
  }

  function t$e() {
    return {
      filters: {},
      add: function add(t, e) {
        (this.filters[t] = this.filters[t] || []).push(e);
      },
      clear: function clear() {
        this.filters = {};
      },
      execute: function execute(t, e, i) {
        if (!this.filters[t] || !this.filters[t].length) {
          return e;
        }

        var s = e;
        var r = this.filters[t];
        var l = r.length;

        for (var _t = 0; _t < l; _t++) {
          s = r[_t].apply(s, i);
        }

        return s;
      },
      remove: function remove(t, e) {
        if (this.filters[t]) {
          this.filters[t] = this.filters[t].filter(function (t) {
            return t !== e;
          });
        }
      }
    };
  }

  function e$a(e, t, r, n) {
    var o = (r.getAttribute("type") || "").toLowerCase();
    var c = r.tagName.toLowerCase();

    if (c === "textarea") {
      return r.value;
    }

    if (c === "select") {
      var _e = r;
      var _t = _e.selectedIndex;
      return _t >= 0 ? _e.options.item(_t).value : "";
    }

    if (c === "input") {
      if ("radio" === o || "checkbox" === o) {
        var _e2 = n.filter(function (e) {
          return e.checked;
        }).length;
        return _e2 === 0 ? "" : _e2 + "";
      } else {
        return r.value;
      }
    }

    return "";
  }

  function r$2(r, e) {
    var t = Array.isArray(e) ? e : [e];
    var a = r;
    t.forEach(function (r) {
      a = a.replace("%s", r);
    });
    return a;
  }

  function s$5() {
    var s = function s(e) {
      return parseFloat("".concat(e).replace(",", "."));
    };

    return {
      validate: function validate(a) {
        var t = a.value;

        if (t === "") {
          return {
            valid: true
          };
        }

        var n = Object.assign({}, {
          inclusive: true,
          message: ""
        }, a.options);
        var l = s(n.min);
        var o = s(n.max);
        return n.inclusive ? {
          message: r$2(a.l10n ? n.message || a.l10n.between["default"] : n.message, ["".concat(l), "".concat(o)]),
          valid: parseFloat(t) >= l && parseFloat(t) <= o
        } : {
          message: r$2(a.l10n ? n.message || a.l10n.between.notInclusive : n.message, ["".concat(l), "".concat(o)]),
          valid: parseFloat(t) > l && parseFloat(t) < o
        };
      }
    };
  }

  function t$d() {
    return {
      validate: function validate(t) {
        return {
          valid: true
        };
      }
    };
  }

  function t$c(t, n) {
    if ("function" === typeof t) {
      return t.apply(this, n);
    } else if ("string" === typeof t) {
      var e = t;

      if ("()" === e.substring(e.length - 2)) {
        e = e.substring(0, e.length - 2);
      }

      var i = e.split(".");
      var o = i.pop();
      var f = window;

      var _iterator = _createForOfIteratorHelper(i),
          _step;

      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var _t = _step.value;
          f = f[_t];
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }

      return typeof f[o] === "undefined" ? null : f[o].apply(this, n);
    }
  }

  function o$3() {
    return {
      validate: function validate(o) {
        var l = t$c(o.options.callback, [o]);
        return "boolean" === typeof l ? {
          valid: l
        } : l;
      }
    };
  }

  function t$b() {
    return {
      validate: function validate(t) {
        var o = "select" === t.element.tagName.toLowerCase() ? t.element.querySelectorAll("option:checked").length : t.elements.filter(function (e) {
          return e.checked;
        }).length;
        var s = t.options.min ? "".concat(t.options.min) : "";
        var n = t.options.max ? "".concat(t.options.max) : "";
        var a = t.l10n ? t.options.message || t.l10n.choice["default"] : t.options.message;
        var l = !(s && o < parseInt(s, 10) || n && o > parseInt(n, 10));

        switch (true) {
          case !!s && !!n:
            a = r$2(t.l10n ? t.l10n.choice.between : t.options.message, [s, n]);
            break;

          case !!s:
            a = r$2(t.l10n ? t.l10n.choice.more : t.options.message, s);
            break;

          case !!n:
            a = r$2(t.l10n ? t.l10n.choice.less : t.options.message, n);
            break;
        }

        return {
          message: a,
          valid: l
        };
      }
    };
  }

  var t$a = {
    AMERICAN_EXPRESS: {
      length: [15],
      prefix: ["34", "37"]
    },
    DANKORT: {
      length: [16],
      prefix: ["5019"]
    },
    DINERS_CLUB: {
      length: [14],
      prefix: ["300", "301", "302", "303", "304", "305", "36"]
    },
    DINERS_CLUB_US: {
      length: [16],
      prefix: ["54", "55"]
    },
    DISCOVER: {
      length: [16],
      prefix: ["6011", "622126", "622127", "622128", "622129", "62213", "62214", "62215", "62216", "62217", "62218", "62219", "6222", "6223", "6224", "6225", "6226", "6227", "6228", "62290", "62291", "622920", "622921", "622922", "622923", "622924", "622925", "644", "645", "646", "647", "648", "649", "65"]
    },
    ELO: {
      length: [16],
      prefix: ["4011", "4312", "4389", "4514", "4573", "4576", "5041", "5066", "5067", "509", "6277", "6362", "6363", "650", "6516", "6550"]
    },
    FORBRUGSFORENINGEN: {
      length: [16],
      prefix: ["600722"]
    },
    JCB: {
      length: [16],
      prefix: ["3528", "3529", "353", "354", "355", "356", "357", "358"]
    },
    LASER: {
      length: [16, 17, 18, 19],
      prefix: ["6304", "6706", "6771", "6709"]
    },
    MAESTRO: {
      length: [12, 13, 14, 15, 16, 17, 18, 19],
      prefix: ["5018", "5020", "5038", "5868", "6304", "6759", "6761", "6762", "6763", "6764", "6765", "6766"]
    },
    MASTERCARD: {
      length: [16],
      prefix: ["51", "52", "53", "54", "55"]
    },
    SOLO: {
      length: [16, 18, 19],
      prefix: ["6334", "6767"]
    },
    UNIONPAY: {
      length: [16, 17, 18, 19],
      prefix: ["622126", "622127", "622128", "622129", "62213", "62214", "62215", "62216", "62217", "62218", "62219", "6222", "6223", "6224", "6225", "6226", "6227", "6228", "62290", "62291", "622920", "622921", "622922", "622923", "622924", "622925"]
    },
    VISA: {
      length: [16],
      prefix: ["4"]
    },
    VISA_ELECTRON: {
      length: [16],
      prefix: ["4026", "417500", "4405", "4508", "4844", "4913", "4917"]
    }
  };
  function l$2() {
    return {
      validate: function validate(l) {
        if (l.value === "") {
          return {
            meta: {
              type: null
            },
            valid: true
          };
        }

        if (/[^0-9-\s]+/.test(l.value)) {
          return {
            meta: {
              type: null
            },
            valid: false
          };
        }

        var r = l.value.replace(/\D/g, "");

        if (!t$i(r)) {
          return {
            meta: {
              type: null
            },
            valid: false
          };
        }

        for (var _i = 0, _Object$keys = Object.keys(t$a); _i < _Object$keys.length; _i++) {
          var _e = _Object$keys[_i];

          for (var n in t$a[_e].prefix) {
            if (l.value.substr(0, t$a[_e].prefix[n].length) === t$a[_e].prefix[n] && t$a[_e].length.indexOf(r.length) !== -1) {
              return {
                meta: {
                  type: _e
                },
                valid: true
              };
            }
          }
        }

        return {
          meta: {
            type: null
          },
          valid: false
        };
      }
    };
  }

  function t$9(t, e, n, r) {
    if (isNaN(t) || isNaN(e) || isNaN(n)) {
      return false;
    }

    if (t < 1e3 || t > 9999 || e <= 0 || e > 12) {
      return false;
    }

    var s = [31, t % 400 === 0 || t % 100 !== 0 && t % 4 === 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    if (n <= 0 || n > s[e - 1]) {
      return false;
    }

    if (r === true) {
      var _r = new Date();

      var _s = _r.getFullYear();

      var a = _r.getMonth();

      var u = _r.getDate();

      return t < _s || t === _s && e - 1 < a || t === _s && e - 1 === a && n < u;
    }

    return true;
  }

  function n() {
    var n = function n(t, e, _n) {
      var s = e.indexOf("YYYY");
      var a = e.indexOf("MM");
      var l = e.indexOf("DD");

      if (s === -1 || a === -1 || l === -1) {
        return null;
      }

      var o = t.split(" ");
      var r = o[0].split(_n);

      if (r.length < 3) {
        return null;
      }

      var c = new Date(parseInt(r[s], 10), parseInt(r[a], 10) - 1, parseInt(r[l], 10));

      if (o.length > 1) {
        var _t = o[1].split(":");

        c.setHours(_t.length > 0 ? parseInt(_t[0], 10) : 0);
        c.setMinutes(_t.length > 1 ? parseInt(_t[1], 10) : 0);
        c.setSeconds(_t.length > 2 ? parseInt(_t[2], 10) : 0);
      }

      return c;
    };

    var s = function s(t, e) {
      var n = e.replace(/Y/g, "y").replace(/M/g, "m").replace(/D/g, "d").replace(/:m/g, ":M").replace(/:mm/g, ":MM").replace(/:S/, ":s").replace(/:SS/, ":ss");
      var s = t.getDate();
      var a = s < 10 ? "0".concat(s) : s;
      var l = t.getMonth() + 1;
      var o = l < 10 ? "0".concat(l) : l;
      var r = "".concat(t.getFullYear()).substr(2);
      var c = t.getFullYear();
      var i = t.getHours() % 12 || 12;
      var g = i < 10 ? "0".concat(i) : i;
      var u = t.getHours();
      var m = u < 10 ? "0".concat(u) : u;
      var d = t.getMinutes();
      var f = d < 10 ? "0".concat(d) : d;
      var p = t.getSeconds();
      var h = p < 10 ? "0".concat(p) : p;
      var $ = {
        H: "".concat(u),
        HH: "".concat(m),
        M: "".concat(d),
        MM: "".concat(f),
        d: "".concat(s),
        dd: "".concat(a),
        h: "".concat(i),
        hh: "".concat(g),
        m: "".concat(l),
        mm: "".concat(o),
        s: "".concat(p),
        ss: "".concat(h),
        yy: "".concat(r),
        yyyy: "".concat(c)
      };
      return n.replace(/d{1,4}|m{1,4}|yy(?:yy)?|([HhMs])\1?|"[^"]*"|'[^']*'/g, function (t) {
        return $[t] ? $[t] : t.slice(1, t.length - 1);
      });
    };

    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            meta: {
              date: null
            },
            valid: true
          };
        }

        var l = Object.assign({}, {
          format: a.element && a.element.getAttribute("type") === "date" ? "YYYY-MM-DD" : "MM/DD/YYYY",
          message: ""
        }, a.options);
        var o = a.l10n ? a.l10n.date["default"] : l.message;
        var r = {
          message: "".concat(o),
          meta: {
            date: null
          },
          valid: false
        };
        var c = l.format.split(" ");
        var i = c.length > 1 ? c[1] : null;
        var g = c.length > 2 ? c[2] : null;
        var u = a.value.split(" ");
        var m = u[0];
        var d = u.length > 1 ? u[1] : null;

        if (c.length !== u.length) {
          return r;
        }

        var f = l.separator || (m.indexOf("/") !== -1 ? "/" : m.indexOf("-") !== -1 ? "-" : m.indexOf(".") !== -1 ? "." : "/");

        if (f === null || m.indexOf(f) === -1) {
          return r;
        }

        var p = m.split(f);
        var h = c[0].split(f);

        if (p.length !== h.length) {
          return r;
        }

        var $ = p[h.indexOf("YYYY")];
        var M = p[h.indexOf("MM")];
        var Y = p[h.indexOf("DD")];

        if (!/^\d+$/.test($) || !/^\d+$/.test(M) || !/^\d+$/.test(Y) || $.length > 4 || M.length > 2 || Y.length > 2) {
          return r;
        }

        var D = parseInt($, 10);
        var x = parseInt(M, 10);
        var y = parseInt(Y, 10);

        if (!t$9(D, x, y)) {
          return r;
        }

        var I = new Date(D, x - 1, y);

        if (i) {
          var _t2 = d.split(":");

          if (i.split(":").length !== _t2.length) {
            return r;
          }

          var _e = _t2.length > 0 ? _t2[0].length <= 2 && /^\d+$/.test(_t2[0]) ? parseInt(_t2[0], 10) : -1 : 0;

          var _n2 = _t2.length > 1 ? _t2[1].length <= 2 && /^\d+$/.test(_t2[1]) ? parseInt(_t2[1], 10) : -1 : 0;

          var _s = _t2.length > 2 ? _t2[2].length <= 2 && /^\d+$/.test(_t2[2]) ? parseInt(_t2[2], 10) : -1 : 0;

          if (_e === -1 || _n2 === -1 || _s === -1) {
            return r;
          }

          if (_s < 0 || _s > 60) {
            return r;
          }

          if (_e < 0 || _e >= 24 || g && _e > 12) {
            return r;
          }

          if (_n2 < 0 || _n2 > 59) {
            return r;
          }

          I.setHours(_e);
          I.setMinutes(_n2);
          I.setSeconds(_s);
        }

        var O = typeof l.min === "function" ? l.min() : l.min;
        var v = O instanceof Date ? O : O ? n(O, h, f) : I;
        var H = typeof l.max === "function" ? l.max() : l.max;
        var T = H instanceof Date ? H : H ? n(H, h, f) : I;
        var S = O instanceof Date ? s(v, l.format) : O;
        var b = H instanceof Date ? s(T, l.format) : H;

        switch (true) {
          case !!S && !b:
            return {
              message: r$2(a.l10n ? a.l10n.date.min : o, S),
              meta: {
                date: I
              },
              valid: I.getTime() >= v.getTime()
            };

          case !!b && !S:
            return {
              message: r$2(a.l10n ? a.l10n.date.max : o, b),
              meta: {
                date: I
              },
              valid: I.getTime() <= T.getTime()
            };

          case !!b && !!S:
            return {
              message: r$2(a.l10n ? a.l10n.date.range : o, [S, b]),
              meta: {
                date: I
              },
              valid: I.getTime() <= T.getTime() && I.getTime() >= v.getTime()
            };

          default:
            return {
              message: "".concat(o),
              meta: {
                date: I
              },
              valid: true
            };
        }
      }
    };
  }

  function o$2() {
    return {
      validate: function validate(o) {
        var t = "function" === typeof o.options.compare ? o.options.compare.call(this) : o.options.compare;
        return {
          valid: t === "" || o.value !== t
        };
      }
    };
  }

  function e$9() {
    return {
      validate: function validate(e) {
        return {
          valid: e.value === "" || /^\d+$/.test(e.value)
        };
      }
    };
  }

  function t$8() {
    var t = function t(_t3, e) {
      var s = _t3.split(/"/);

      var l = s.length;
      var n = [];
      var r = "";

      for (var _t = 0; _t < l; _t++) {
        if (_t % 2 === 0) {
          var _l = s[_t].split(e);

          var a = _l.length;

          if (a === 1) {
            r += _l[0];
          } else {
            n.push(r + _l[0]);

            for (var _t2 = 1; _t2 < a - 1; _t2++) {
              n.push(_l[_t2]);
            }

            r = _l[a - 1];
          }
        } else {
          r += '"' + s[_t];

          if (_t < l - 1) {
            r += '"';
          }
        }
      }

      n.push(r);
      return n;
    };

    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var s = Object.assign({}, {
          multiple: false,
          separator: /[,;]/
        }, e.options);
        var l = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        var n = s.multiple === true || "".concat(s.multiple) === "true";

        if (n) {
          var _n = s.separator || /[,;]/;

          var r = t(e.value, _n);
          var a = r.length;

          for (var _t4 = 0; _t4 < a; _t4++) {
            if (!l.test(r[_t4])) {
              return {
                valid: false
              };
            }
          }

          return {
            valid: true
          };
        } else {
          return {
            valid: l.test(e.value)
          };
        }
      }
    };
  }

  function e$8() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var t;
        var i = e.options.extension ? e.options.extension.toLowerCase().split(",") : null;
        var s = e.options.type ? e.options.type.toLowerCase().split(",") : null;
        var n = window["File"] && window["FileList"] && window["FileReader"];

        if (n) {
          var _n = e.element.files;
          var o = _n.length;
          var a = 0;

          if (e.options.maxFiles && o > parseInt("".concat(e.options.maxFiles), 10)) {
            return {
              meta: {
                error: "INVALID_MAX_FILES"
              },
              valid: false
            };
          }

          if (e.options.minFiles && o < parseInt("".concat(e.options.minFiles), 10)) {
            return {
              meta: {
                error: "INVALID_MIN_FILES"
              },
              valid: false
            };
          }

          var r = {};

          for (var l = 0; l < o; l++) {
            a += _n[l].size;
            t = _n[l].name.substr(_n[l].name.lastIndexOf(".") + 1);
            r = {
              ext: t,
              file: _n[l],
              size: _n[l].size,
              type: _n[l].type
            };

            if (e.options.minSize && _n[l].size < parseInt("".concat(e.options.minSize), 10)) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_MIN_SIZE"
                }, r),
                valid: false
              };
            }

            if (e.options.maxSize && _n[l].size > parseInt("".concat(e.options.maxSize), 10)) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_MAX_SIZE"
                }, r),
                valid: false
              };
            }

            if (i && i.indexOf(t.toLowerCase()) === -1) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_EXTENSION"
                }, r),
                valid: false
              };
            }

            if (_n[l].type && s && s.indexOf(_n[l].type.toLowerCase()) === -1) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_TYPE"
                }, r),
                valid: false
              };
            }
          }

          if (e.options.maxTotalSize && a > parseInt("".concat(e.options.maxTotalSize), 10)) {
            return {
              meta: Object.assign({}, {
                error: "INVALID_MAX_TOTAL_SIZE",
                totalSize: a
              }, r),
              valid: false
            };
          }

          if (e.options.minTotalSize && a < parseInt("".concat(e.options.minTotalSize), 10)) {
            return {
              meta: Object.assign({}, {
                error: "INVALID_MIN_TOTAL_SIZE",
                totalSize: a
              }, r),
              valid: false
            };
          }
        } else {
          t = e.value.substr(e.value.lastIndexOf(".") + 1);

          if (i && i.indexOf(t.toLowerCase()) === -1) {
            return {
              meta: {
                error: "INVALID_EXTENSION",
                ext: t
              },
              valid: false
            };
          }
        }

        return {
          valid: true
        };
      }
    };
  }

  function a$4() {
    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var s = Object.assign({}, {
          inclusive: true,
          message: ""
        }, a.options);
        var t = parseFloat("".concat(s.min).replace(",", "."));
        return s.inclusive ? {
          message: r$2(a.l10n ? s.message || a.l10n.greaterThan["default"] : s.message, "".concat(t)),
          valid: parseFloat(a.value) >= t
        } : {
          message: r$2(a.l10n ? s.message || a.l10n.greaterThan.notInclusive : s.message, "".concat(t)),
          valid: parseFloat(a.value) > t
        };
      }
    };
  }

  function o$1() {
    return {
      validate: function validate(o) {
        var t = "function" === typeof o.options.compare ? o.options.compare.call(this) : o.options.compare;
        return {
          valid: t === "" || o.value === t
        };
      }
    };
  }

  function a$3() {
    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var e = Object.assign({}, {
          decimalSeparator: ".",
          thousandsSeparator: ""
        }, a.options);
        var t = e.decimalSeparator === "." ? "\\." : e.decimalSeparator;
        var r = e.thousandsSeparator === "." ? "\\." : e.thousandsSeparator;
        var o = new RegExp("^-?[0-9]{1,3}(".concat(r, "[0-9]{3})*(").concat(t, "[0-9]+)?$"));
        var n = new RegExp(r, "g");
        var s = "".concat(a.value);

        if (!o.test(s)) {
          return {
            valid: false
          };
        }

        if (r) {
          s = s.replace(n, "");
        }

        if (t) {
          s = s.replace(t, ".");
        }

        var i = parseFloat(s);
        return {
          valid: !isNaN(i) && isFinite(i) && Math.floor(i) === i
        };
      }
    };
  }

  function d() {
    return {
      validate: function validate(d) {
        if (d.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          ipv4: true,
          ipv6: true
        }, d.options);
        var e = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\/([0-9]|[1-2][0-9]|3[0-2]))?$/;
        var s = /^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*(\/(\d|\d\d|1[0-1]\d|12[0-8]))?$/;

        switch (true) {
          case a.ipv4 && !a.ipv6:
            return {
              message: d.l10n ? a.message || d.l10n.ip.ipv4 : a.message,
              valid: e.test(d.value)
            };

          case !a.ipv4 && a.ipv6:
            return {
              message: d.l10n ? a.message || d.l10n.ip.ipv6 : a.message,
              valid: s.test(d.value)
            };

          case a.ipv4 && a.ipv6:
          default:
            return {
              message: d.l10n ? a.message || d.l10n.ip["default"] : a.message,
              valid: e.test(d.value) || s.test(d.value)
            };
        }
      }
    };
  }

  function s$4() {
    return {
      validate: function validate(s) {
        if (s.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          inclusive: true,
          message: ""
        }, s.options);
        var l = parseFloat("".concat(a.max).replace(",", "."));
        return a.inclusive ? {
          message: r$2(s.l10n ? a.message || s.l10n.lessThan["default"] : a.message, "".concat(l)),
          valid: parseFloat(s.value) <= l
        } : {
          message: r$2(s.l10n ? a.message || s.l10n.lessThan.notInclusive : a.message, "".concat(l)),
          valid: parseFloat(s.value) < l
        };
      }
    };
  }

  function t$7() {
    return {
      validate: function validate(t) {
        var n = !!t.options && !!t.options.trim;
        var o = t.value;
        return {
          valid: !n && o !== "" || n && o !== "" && o.trim() !== ""
        };
      }
    };
  }

  function a$2() {
    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var e = Object.assign({}, {
          decimalSeparator: ".",
          thousandsSeparator: ""
        }, a.options);
        var t = "".concat(a.value);

        if (t.substr(0, 1) === e.decimalSeparator) {
          t = "0".concat(e.decimalSeparator).concat(t.substr(1));
        } else if (t.substr(0, 2) === "-".concat(e.decimalSeparator)) {
          t = "-0".concat(e.decimalSeparator).concat(t.substr(2));
        }

        var r = e.decimalSeparator === "." ? "\\." : e.decimalSeparator;
        var s = e.thousandsSeparator === "." ? "\\." : e.thousandsSeparator;
        var i = new RegExp("^-?[0-9]{1,3}(".concat(s, "[0-9]{3})*(").concat(r, "[0-9]+)?$"));
        var o = new RegExp(s, "g");

        if (!i.test(t)) {
          return {
            valid: false
          };
        }

        if (s) {
          t = t.replace(o, "");
        }

        if (r) {
          t = t.replace(r, ".");
        }

        var l = parseFloat(t);
        return {
          valid: !isNaN(l) && isFinite(l)
        };
      }
    };
  }

  function r$1() {
    return {
      validate: function validate(r) {
        return t$c(r.options.promise, [r]);
      }
    };
  }

  function e$7() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var t = e.options.regexp;

        if (t instanceof RegExp) {
          return {
            valid: t.test(e.value)
          };
        } else {
          var n = t.toString();
          var o = e.options.flags ? new RegExp(n, e.options.flags) : new RegExp(n);
          return {
            valid: o.test(e.value)
          };
        }
      }
    };
  }

  function e$6(e, t) {
    var n = function n(e) {
      return Object.keys(e).map(function (t) {
        return "".concat(encodeURIComponent(t), "=").concat(encodeURIComponent(e[t]));
      }).join("&");
    };

    return new Promise(function (o, s) {
      var d = Object.assign({}, {
        crossDomain: false,
        headers: {},
        method: "GET",
        params: {}
      }, t);
      var a = Object.keys(d.params).map(function (e) {
        return "".concat(encodeURIComponent(e), "=").concat(encodeURIComponent(d.params[e]));
      }).join("&");
      var r = e.indexOf("?");
      var c = "GET" === d.method ? "".concat(e).concat(r ? "?" : "&").concat(a) : e;

      if (d.crossDomain) {
        var _e = document.createElement("script");

        var _t = "___fetch".concat(Date.now(), "___");

        window[_t] = function (e) {
          delete window[_t];
          o(e);
        };

        _e.src = "".concat(c).concat(r ? "&" : "?", "callback=").concat(_t);
        _e.async = true;

        _e.addEventListener("load", function () {
          _e.parentNode.removeChild(_e);
        });

        _e.addEventListener("error", function () {
          return s;
        });

        document.head.appendChild(_e);
      } else {
        var _e2 = new XMLHttpRequest();

        _e2.open(d.method, c);

        _e2.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        if ("POST" === d.method) {
          _e2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        }

        Object.keys(d.headers).forEach(function (t) {
          return _e2.setRequestHeader(t, d.headers[t]);
        });

        _e2.addEventListener("load", function () {
          o(JSON.parse(this.responseText));
        });

        _e2.addEventListener("error", function () {
          return s;
        });

        _e2.send(n(d.params));
      }
    });
  }

  function a$1() {
    var a = {
      crossDomain: false,
      data: {},
      headers: {},
      method: "GET",
      validKey: "valid"
    };
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return Promise.resolve({
            valid: true
          });
        }

        var s = Object.assign({}, a, t.options);
        var r = s.data;

        if ("function" === typeof s.data) {
          r = s.data.call(this, t);
        }

        if ("string" === typeof r) {
          r = JSON.parse(r);
        }

        r[s.name || t.field] = t.value;
        var o = "function" === typeof s.url ? s.url.call(this, t) : s.url;
        return e$6(o, {
          crossDomain: s.crossDomain,
          headers: s.headers,
          method: s.method,
          params: r
        }).then(function (e) {
          return Promise.resolve({
            message: e["message"],
            meta: e,
            valid: "".concat(e[s.validKey]) === "true"
          });
        })["catch"](function (e) {
          return Promise.reject({
            valid: false
          });
        });
      }
    };
  }

  function e$5() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          "case": "lower"
        }, e.options);
        var s = (a["case"] || "lower").toLowerCase();
        return {
          message: a.message || (e.l10n ? "upper" === s ? e.l10n.stringCase.upper : e.l10n.stringCase["default"] : a.message),
          valid: "upper" === s ? e.value === e.value.toUpperCase() : e.value === e.value.toLowerCase()
        };
      }
    };
  }

  function t$6() {
    var t = function t(e) {
      var t = e.length;

      for (var s = e.length - 1; s >= 0; s--) {
        var n = e.charCodeAt(s);

        if (n > 127 && n <= 2047) {
          t++;
        } else if (n > 2047 && n <= 65535) {
          t += 2;
        }

        if (n >= 56320 && n <= 57343) {
          s--;
        }
      }

      return "".concat(t);
    };

    return {
      validate: function validate(s) {
        var n = Object.assign({}, {
          message: "",
          trim: false,
          utf8Bytes: false
        }, s.options);
        var a = n.trim === true || "".concat(n.trim) === "true" ? s.value.trim() : s.value;

        if (a === "") {
          return {
            valid: true
          };
        }

        var r = n.min ? "".concat(n.min) : "";
        var l = n.max ? "".concat(n.max) : "";
        var i = n.utf8Bytes ? t(a) : a.length;
        var g = true;
        var m = s.l10n ? n.message || s.l10n.stringLength["default"] : n.message;

        if (r && i < parseInt(r, 10) || l && i > parseInt(l, 10)) {
          g = false;
        }

        switch (true) {
          case !!r && !!l:
            m = r$2(s.l10n ? n.message || s.l10n.stringLength.between : n.message, [r, l]);
            break;

          case !!r:
            m = r$2(s.l10n ? n.message || s.l10n.stringLength.more : n.message, "".concat(parseInt(r, 10)));
            break;

          case !!l:
            m = r$2(s.l10n ? n.message || s.l10n.stringLength.less : n.message, "".concat(parseInt(l, 10)));
            break;
        }

        return {
          message: m,
          valid: g
        };
      }
    };
  }

  function t$5() {
    var t = {
      allowEmptyProtocol: false,
      allowLocal: false,
      protocol: "http, https, ftp"
    };
    return {
      validate: function validate(o) {
        if (o.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, t, o.options);
        var l = a.allowLocal === true || "".concat(a.allowLocal) === "true";
        var f = a.allowEmptyProtocol === true || "".concat(a.allowEmptyProtocol) === "true";
        var u = a.protocol.split(",").join("|").replace(/\s/g, "");
        var e = new RegExp("^" + "(?:(?:" + u + ")://)" + (f ? "?" : "") + "(?:\\S+(?::\\S*)?@)?" + "(?:" + (l ? "" : "(?!(?:10|127)(?:\\.\\d{1,3}){3})" + "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" + "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})") + "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" + "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" + "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" + "|" + "(?:(?:[a-z\\u00a1-\\uffff0-9]-?)*[a-z\\u00a1-\\uffff0-9]+)" + "(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-?)*[a-z\\u00a1-\\uffff0-9])*" + "(?:\\.(?:[a-z\\u00a1-\\uffff]{2,}))" + (l ? "?" : "") + ")" + "(?::\\d{2,5})?" + "(?:/[^\\s]*)?$", "i");
        return {
          valid: e.test(o.value)
        };
      }
    };
  }

  var s$3 = {
    between: s$5,
    blank: t$d,
    callback: o$3,
    choice: t$b,
    creditCard: l$2,
    date: n,
    different: o$2,
    digits: e$9,
    emailAddress: t$8,
    file: e$8,
    greaterThan: a$4,
    identical: o$1,
    integer: a$3,
    ip: d,
    lessThan: s$4,
    notEmpty: t$7,
    numeric: a$2,
    promise: r$1,
    regexp: e$7,
    remote: a$1,
    stringCase: e$5,
    stringLength: t$6,
    uri: t$5
  };

  var l$1 = /*#__PURE__*/function () {
    function l(i, s) {
      _classCallCheck(this, l);

      this.elements = {};
      this.ee = s$6();
      this.filter = t$e();
      this.plugins = {};
      this.results = new Map();
      this.validators = {};
      this.form = i;
      this.fields = s;
    }

    _createClass(l, [{
      key: "on",
      value: function on(e, t) {
        this.ee.on(e, t);
        return this;
      }
    }, {
      key: "off",
      value: function off(e, t) {
        this.ee.off(e, t);
        return this;
      }
    }, {
      key: "emit",
      value: function emit(e) {
        var _this$ee;

        for (var _len = arguments.length, t = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          t[_key - 1] = arguments[_key];
        }

        (_this$ee = this.ee).emit.apply(_this$ee, [e].concat(t));

        return this;
      }
    }, {
      key: "registerPlugin",
      value: function registerPlugin(e, t) {
        if (this.plugins[e]) {
          throw new Error("The plguin ".concat(e, " is registered"));
        }

        t.setCore(this);
        t.install();
        this.plugins[e] = t;
        return this;
      }
    }, {
      key: "deregisterPlugin",
      value: function deregisterPlugin(e) {
        var t = this.plugins[e];

        if (t) {
          t.uninstall();
        }

        delete this.plugins[e];
        return this;
      }
    }, {
      key: "registerValidator",
      value: function registerValidator(e, t) {
        if (this.validators[e]) {
          throw new Error("The validator ".concat(e, " is registered"));
        }

        this.validators[e] = t;
        return this;
      }
    }, {
      key: "registerFilter",
      value: function registerFilter(e, t) {
        this.filter.add(e, t);
        return this;
      }
    }, {
      key: "deregisterFilter",
      value: function deregisterFilter(e, t) {
        this.filter.remove(e, t);
        return this;
      }
    }, {
      key: "executeFilter",
      value: function executeFilter(e, t, i) {
        return this.filter.execute(e, t, i);
      }
    }, {
      key: "addField",
      value: function addField(e, t) {
        var i = Object.assign({}, {
          selector: "",
          validators: {}
        }, t);
        this.fields[e] = this.fields[e] ? {
          selector: i.selector || this.fields[e].selector,
          validators: Object.assign({}, this.fields[e].validators, i.validators)
        } : i;
        this.elements[e] = this.queryElements(e);
        this.emit("core.field.added", {
          elements: this.elements[e],
          field: e,
          options: this.fields[e]
        });
        return this;
      }
    }, {
      key: "removeField",
      value: function removeField(e) {
        if (!this.fields[e]) {
          throw new Error("The field ".concat(e, " validators are not defined. Please ensure the field is added first"));
        }

        var t = this.elements[e];
        var i = this.fields[e];
        delete this.elements[e];
        delete this.fields[e];
        this.emit("core.field.removed", {
          elements: t,
          field: e,
          options: i
        });
        return this;
      }
    }, {
      key: "validate",
      value: function validate() {
        var _this = this;

        this.emit("core.form.validating", {
          formValidation: this
        });
        return this.filter.execute("validate-pre", Promise.resolve(), []).then(function () {
          return Promise.all(Object.keys(_this.fields).map(function (e) {
            return _this.validateField(e);
          })).then(function (e) {
            switch (true) {
              case e.indexOf("Invalid") !== -1:
                _this.emit("core.form.invalid", {
                  formValidation: _this
                });

                return Promise.resolve("Invalid");

              case e.indexOf("NotValidated") !== -1:
                _this.emit("core.form.notvalidated", {
                  formValidation: _this
                });

                return Promise.resolve("NotValidated");

              default:
                _this.emit("core.form.valid", {
                  formValidation: _this
                });

                return Promise.resolve("Valid");
            }
          });
        });
      }
    }, {
      key: "validateField",
      value: function validateField(e) {
        var _this2 = this;

        var t = this.results.get(e);

        if (t === "Valid" || t === "Invalid") {
          return Promise.resolve(t);
        }

        this.emit("core.field.validating", e);
        var i = this.elements[e];

        if (i.length === 0) {
          this.emit("core.field.valid", e);
          return Promise.resolve("Valid");
        }

        var s = i[0].getAttribute("type");

        if ("radio" === s || "checkbox" === s || i.length === 1) {
          return this.validateElement(e, i[0]);
        } else {
          return Promise.all(i.map(function (t) {
            return _this2.validateElement(e, t);
          })).then(function (t) {
            switch (true) {
              case t.indexOf("Invalid") !== -1:
                _this2.emit("core.field.invalid", e);

                _this2.results.set(e, "Invalid");

                return Promise.resolve("Invalid");

              case t.indexOf("NotValidated") !== -1:
                _this2.emit("core.field.notvalidated", e);

                _this2.results["delete"](e);

                return Promise.resolve("NotValidated");

              default:
                _this2.emit("core.field.valid", e);

                _this2.results.set(e, "Valid");

                return Promise.resolve("Valid");
            }
          });
        }
      }
    }, {
      key: "validateElement",
      value: function validateElement(e, t) {
        var _this3 = this;

        this.results["delete"](e);
        var i = this.elements[e];
        var s = this.filter.execute("element-ignored", false, [e, t, i]);

        if (s) {
          this.emit("core.element.ignored", {
            element: t,
            elements: i,
            field: e
          });
          return Promise.resolve("Ignored");
        }

        var _l = this.fields[e].validators;
        this.emit("core.element.validating", {
          element: t,
          elements: i,
          field: e
        });
        var r = Object.keys(_l).map(function (i) {
          return function () {
            return _this3.executeValidator(e, t, i, _l[i]);
          };
        });
        return this.waterfall(r).then(function (s) {
          var _l2 = s.indexOf("Invalid") === -1;

          _this3.emit("core.element.validated", {
            element: t,
            elements: i,
            field: e,
            valid: _l2
          });

          var r = t.getAttribute("type");

          if ("radio" === r || "checkbox" === r || i.length === 1) {
            _this3.emit(_l2 ? "core.field.valid" : "core.field.invalid", e);
          }

          return Promise.resolve(_l2 ? "Valid" : "Invalid");
        })["catch"](function (s) {
          _this3.emit("core.element.notvalidated", {
            element: t,
            elements: i,
            field: e
          });

          return Promise.resolve(s);
        });
      }
    }, {
      key: "executeValidator",
      value: function executeValidator(e, t, i, s) {
        var _this4 = this;

        var _l3 = this.elements[e];
        var r = this.filter.execute("validator-name", i, [i, e]);
        s.message = this.filter.execute("validator-message", s.message, [this.locale, e, r]);

        if (!this.validators[r] || s.enabled === false) {
          this.emit("core.validator.validated", {
            element: t,
            elements: _l3,
            field: e,
            result: this.normalizeResult(e, r, {
              valid: true
            }),
            validator: r
          });
          return Promise.resolve("Valid");
        }

        var a = this.validators[r];
        var d = this.getElementValue(e, t, r);
        var o = this.filter.execute("field-should-validate", true, [e, t, d, i]);

        if (!o) {
          this.emit("core.validator.notvalidated", {
            element: t,
            elements: _l3,
            field: e,
            validator: i
          });
          return Promise.resolve("NotValidated");
        }

        this.emit("core.validator.validating", {
          element: t,
          elements: _l3,
          field: e,
          validator: i
        });
        var n = a().validate({
          element: t,
          elements: _l3,
          field: e,
          l10n: this.localization,
          options: s,
          value: d
        });
        var h = "function" === typeof n["then"];

        if (h) {
          return n.then(function (s) {
            var r = _this4.normalizeResult(e, i, s);

            _this4.emit("core.validator.validated", {
              element: t,
              elements: _l3,
              field: e,
              result: r,
              validator: i
            });

            return r.valid ? "Valid" : "Invalid";
          });
        } else {
          var _s = this.normalizeResult(e, i, n);

          this.emit("core.validator.validated", {
            element: t,
            elements: _l3,
            field: e,
            result: _s,
            validator: i
          });
          return Promise.resolve(_s.valid ? "Valid" : "Invalid");
        }
      }
    }, {
      key: "getElementValue",
      value: function getElementValue(e, t, s) {
        var _l4 = e$a(this.form, e, t, this.elements[e]);

        return this.filter.execute("field-value", _l4, [_l4, e, t, s]);
      }
    }, {
      key: "getElements",
      value: function getElements(e) {
        return this.elements[e];
      }
    }, {
      key: "getFields",
      value: function getFields() {
        return this.fields;
      }
    }, {
      key: "getFormElement",
      value: function getFormElement() {
        return this.form;
      }
    }, {
      key: "getLocale",
      value: function getLocale() {
        return this.locale;
      }
    }, {
      key: "getPlugin",
      value: function getPlugin(e) {
        return this.plugins[e];
      }
    }, {
      key: "updateFieldStatus",
      value: function updateFieldStatus(e, t, i) {
        var _this5 = this;

        var s = this.elements[e];

        var _l5 = s[0].getAttribute("type");

        var r = "radio" === _l5 || "checkbox" === _l5 ? [s[0]] : s;
        r.forEach(function (s) {
          return _this5.updateElementStatus(e, s, t, i);
        });

        if (!i) {
          switch (t) {
            case "NotValidated":
              this.emit("core.field.notvalidated", e);
              this.results["delete"](e);
              break;

            case "Validating":
              this.emit("core.field.validating", e);
              this.results["delete"](e);
              break;

            case "Valid":
              this.emit("core.field.valid", e);
              this.results.set(e, "Valid");
              break;

            case "Invalid":
              this.emit("core.field.invalid", e);
              this.results.set(e, "Invalid");
              break;
          }
        }

        return this;
      }
    }, {
      key: "updateElementStatus",
      value: function updateElementStatus(e, t, i, s) {
        var _this6 = this;

        var _l6 = this.elements[e];
        var r = this.fields[e].validators;
        var a = s ? [s] : Object.keys(r);

        switch (i) {
          case "NotValidated":
            a.forEach(function (i) {
              return _this6.emit("core.validator.notvalidated", {
                element: t,
                elements: _l6,
                field: e,
                validator: i
              });
            });
            this.emit("core.element.notvalidated", {
              element: t,
              elements: _l6,
              field: e
            });
            break;

          case "Validating":
            a.forEach(function (i) {
              return _this6.emit("core.validator.validating", {
                element: t,
                elements: _l6,
                field: e,
                validator: i
              });
            });
            this.emit("core.element.validating", {
              element: t,
              elements: _l6,
              field: e
            });
            break;

          case "Valid":
            a.forEach(function (i) {
              return _this6.emit("core.validator.validated", {
                element: t,
                elements: _l6,
                field: e,
                result: {
                  message: r[i].message,
                  valid: true
                },
                validator: i
              });
            });
            this.emit("core.element.validated", {
              element: t,
              elements: _l6,
              field: e,
              valid: true
            });
            break;

          case "Invalid":
            a.forEach(function (i) {
              return _this6.emit("core.validator.validated", {
                element: t,
                elements: _l6,
                field: e,
                result: {
                  message: r[i].message,
                  valid: false
                },
                validator: i
              });
            });
            this.emit("core.element.validated", {
              element: t,
              elements: _l6,
              field: e,
              valid: false
            });
            break;
        }

        return this;
      }
    }, {
      key: "resetForm",
      value: function resetForm(e) {
        var _this7 = this;

        Object.keys(this.fields).forEach(function (t) {
          return _this7.resetField(t, e);
        });
        this.emit("core.form.reset", {
          formValidation: this,
          reset: e
        });
        return this;
      }
    }, {
      key: "resetField",
      value: function resetField(e, t) {
        if (t) {
          var _t = this.elements[e];

          var _i = _t[0].getAttribute("type");

          _t.forEach(function (e) {
            if ("radio" === _i || "checkbox" === _i) {
              e.removeAttribute("selected");
              e.removeAttribute("checked");
              e.checked = false;
            } else {
              e.setAttribute("value", "");

              if (e instanceof HTMLInputElement || e instanceof HTMLTextAreaElement) {
                e.value = "";
              }
            }
          });
        }

        this.updateFieldStatus(e, "NotValidated");
        this.emit("core.field.reset", {
          field: e,
          reset: t
        });
        return this;
      }
    }, {
      key: "revalidateField",
      value: function revalidateField(e) {
        this.updateFieldStatus(e, "NotValidated");
        return this.validateField(e);
      }
    }, {
      key: "disableValidator",
      value: function disableValidator(e, t) {
        return this.toggleValidator(false, e, t);
      }
    }, {
      key: "enableValidator",
      value: function enableValidator(e, t) {
        return this.toggleValidator(true, e, t);
      }
    }, {
      key: "updateValidatorOption",
      value: function updateValidatorOption(e, t, i, s) {
        if (this.fields[e] && this.fields[e].validators && this.fields[e].validators[t]) {
          this.fields[e].validators[t][i] = s;
        }

        return this;
      }
    }, {
      key: "setFieldOptions",
      value: function setFieldOptions(e, t) {
        this.fields[e] = t;
        return this;
      }
    }, {
      key: "destroy",
      value: function destroy() {
        var _this8 = this;

        Object.keys(this.plugins).forEach(function (e) {
          return _this8.plugins[e].uninstall();
        });
        this.ee.clear();
        this.filter.clear();
        this.results.clear();
        this.plugins = {};
        return this;
      }
    }, {
      key: "setLocale",
      value: function setLocale(e, t) {
        this.locale = e;
        this.localization = t;
        return this;
      }
    }, {
      key: "waterfall",
      value: function waterfall(e) {
        return e.reduce(function (e, t) {
          return e.then(function (e) {
            return t().then(function (t) {
              e.push(t);
              return e;
            });
          });
        }, Promise.resolve([]));
      }
    }, {
      key: "queryElements",
      value: function queryElements(e) {
        var t = this.fields[e].selector ? "#" === this.fields[e].selector.charAt(0) ? "[id=\"".concat(this.fields[e].selector.substring(1), "\"]") : this.fields[e].selector : "[name=\"".concat(e, "\"]");
        return [].slice.call(this.form.querySelectorAll(t));
      }
    }, {
      key: "normalizeResult",
      value: function normalizeResult(e, t, i) {
        var s = this.fields[e].validators[t];
        return Object.assign({}, i, {
          message: i.message || (s ? s.message : "") || (this.localization && this.localization[t] && this.localization[t]["default"] ? this.localization[t]["default"] : "") || "The field ".concat(e, " is not valid")
        });
      }
    }, {
      key: "toggleValidator",
      value: function toggleValidator(e, t, i) {
        var _this9 = this;

        var s = this.fields[t].validators;

        if (i && s && s[i]) {
          this.fields[t].validators[i].enabled = e;
        } else if (!i) {
          Object.keys(s).forEach(function (i) {
            return _this9.fields[t].validators[i].enabled = e;
          });
        }

        return this.updateFieldStatus(t, "NotValidated", i);
      }
    }]);

    return l;
  }();

  function r(e, t) {
    var i = Object.assign({}, {
      fields: {},
      locale: "en_US",
      plugins: {},
      init: function init(e) {}
    }, t);
    var r = new l$1(e, i.fields);
    r.setLocale(i.locale, i.localization);
    Object.keys(i.plugins).forEach(function (e) {
      return r.registerPlugin(e, i.plugins[e]);
    });
    Object.keys(s$3).forEach(function (e) {
      return r.registerValidator(e, s$3[e]);
    });
    i.init(r);
    Object.keys(i.fields).forEach(function (e) {
      return r.addField(e, i.fields[e]);
    });
    return r;
  }

  var t$4 = /*#__PURE__*/function () {
    function t(_t) {
      _classCallCheck(this, t);

      this.opts = _t;
    }

    _createClass(t, [{
      key: "setCore",
      value: function setCore(_t2) {
        this.core = _t2;
        return this;
      }
    }, {
      key: "install",
      value: function install() {}
    }, {
      key: "uninstall",
      value: function uninstall() {}
    }]);

    return t;
  }();

  var index$2 = {
    getFieldValue: e$a
  };

  var e$4 = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.opts = t || {};
      _this.validatorNameFilter = _this.getValidatorName.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        this.core.registerFilter("validator-name", this.validatorNameFilter);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.deregisterFilter("validator-name", this.validatorNameFilter);
      }
    }, {
      key: "getValidatorName",
      value: function getValidatorName(t, _e) {
        return this.opts[t] || t;
      }
    }]);

    return e;
  }(t$4);

  var i$3 = /*#__PURE__*/function (_e) {
    _inherits(i, _e);

    var _super = _createSuper(i);

    function i() {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, {});
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.fieldValidHandler = _this.onFieldValid.bind(_assertThisInitialized(_this));
      _this.fieldInvalidHandler = _this.onFieldInvalid.bind(_assertThisInitialized(_this));
      _this.messageDisplayedHandler = _this.onMessageDisplayed.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.core.on("core.field.valid", this.fieldValidHandler).on("core.field.invalid", this.fieldInvalidHandler).on("core.element.validated", this.elementValidatedHandler).on("plugins.message.displayed", this.messageDisplayedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.off("core.field.valid", this.fieldValidHandler).off("core.field.invalid", this.fieldInvalidHandler).off("core.element.validated", this.elementValidatedHandler).off("plugins.message.displayed", this.messageDisplayedHandler);
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        if (e.valid) {
          e.element.setAttribute("aria-invalid", "false");
          e.element.removeAttribute("aria-describedby");
        }
      }
    }, {
      key: "onFieldValid",
      value: function onFieldValid(e) {
        var _i = this.core.getElements(e);

        if (_i) {
          _i.forEach(function (e) {
            e.setAttribute("aria-invalid", "false");
            e.removeAttribute("aria-describedby");
          });
        }
      }
    }, {
      key: "onFieldInvalid",
      value: function onFieldInvalid(e) {
        var _i2 = this.core.getElements(e);

        if (_i2) {
          _i2.forEach(function (e) {
            return e.setAttribute("aria-invalid", "true");
          });
        }
      }
    }, {
      key: "onMessageDisplayed",
      value: function onMessageDisplayed(e) {
        e.messageElement.setAttribute("role", "alert");
        e.messageElement.setAttribute("aria-hidden", "false");

        var _i3 = this.core.getElements(e.field);

        var t = _i3.indexOf(e.element);

        var l = "js-fv-".concat(e.field, "-").concat(t, "-").concat(Date.now(), "-message");
        e.messageElement.setAttribute("id", l);
        e.element.setAttribute("aria-describedby", l);
        var a = e.element.getAttribute("type");

        if ("radio" === a || "checkbox" === a) {
          _i3.forEach(function (e) {
            return e.setAttribute("aria-describedby", l);
          });
        }
      }
    }]);

    return i;
  }(t$4);

  var t$3 = /*#__PURE__*/function (_e) {
    _inherits(t, _e);

    var _super = _createSuper(t);

    function t(e) {
      var _this;

      _classCallCheck(this, t);

      _this = _super.call(this, e);
      _this.addedFields = new Map();
      _this.opts = Object.assign({}, {
        html5Input: false,
        pluginPrefix: "data-fvp-",
        prefix: "data-fv-"
      }, e);
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(t, [{
      key: "install",
      value: function install() {
        var _this2 = this;

        this.parsePlugins();
        var e = this.parseOptions();
        Object.keys(e).forEach(function (_t) {
          if (!_this2.addedFields.has(_t)) {
            _this2.addedFields.set(_t, true);
          }

          _this2.core.addField(_t, e[_t]);
        });
        this.core.on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.addedFields.clear();
        this.core.off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this3 = this;

        var _t2 = e.elements;

        if (!_t2 || _t2.length === 0 || this.addedFields.has(e.field)) {
          return;
        }

        this.addedFields.set(e.field, true);

        _t2.forEach(function (_t3) {
          var s = _this3.parseElement(_t3);

          if (!_this3.isEmptyOption(s)) {
            var _t12 = {
              selector: e.options.selector,
              validators: Object.assign({}, e.options.validators || {}, s.validators)
            };

            _this3.core.setFieldOptions(e.field, _t12);
          }
        });
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        if (e.field && this.addedFields.has(e.field)) {
          this.addedFields["delete"](e.field);
        }
      }
    }, {
      key: "parseOptions",
      value: function parseOptions() {
        var _this4 = this;

        var e = this.opts.prefix;
        var _t5 = {};
        var s = this.core.getFields();
        var a = this.core.getFormElement();
        var i = [].slice.call(a.querySelectorAll("[name], [".concat(e, "field]")));
        i.forEach(function (s) {
          var a = _this4.parseElement(s);

          if (!_this4.isEmptyOption(a)) {
            var _i = s.getAttribute("name") || s.getAttribute("".concat(e, "field"));

            _t5[_i] = Object.assign({}, _t5[_i], a);
          }
        });
        Object.keys(_t5).forEach(function (e) {
          Object.keys(_t5[e].validators).forEach(function (a) {
            _t5[e].validators[a].enabled = _t5[e].validators[a].enabled || false;

            if (s[e] && s[e].validators && s[e].validators[a]) {
              Object.assign(_t5[e].validators[a], s[e].validators[a]);
            }
          });
        });
        return Object.assign({}, s, _t5);
      }
    }, {
      key: "createPluginInstance",
      value: function createPluginInstance(e, _t6) {
        var s = e.split(".");
        var a = window || this;

        for (var _e2 = 0, _t13 = s.length; _e2 < _t13; _e2++) {
          a = a[s[_e2]];
        }

        if (typeof a !== "function") {
          throw new Error("the plugin ".concat(e, " doesn't exist"));
        }

        return new a(_t6);
      }
    }, {
      key: "parsePlugins",
      value: function parsePlugins() {
        var _this5 = this;

        var e = this.core.getFormElement();

        var _t8 = new RegExp("^".concat(this.opts.pluginPrefix, "([a-z0-9-]+)(___)*([a-z0-9-]+)*$"));

        var s = e.attributes.length;
        var a = {};

        for (var i = 0; i < s; i++) {
          var _s = e.attributes[i].name;
          var n = e.attributes[i].value;

          var r = _t8.exec(_s);

          if (r && r.length === 4) {
            var _e3 = this.toCamelCase(r[1]);

            a[_e3] = Object.assign({}, r[3] ? _defineProperty({}, this.toCamelCase(r[3]), n) : {
              enabled: "" === n || "true" === n
            }, a[_e3]);
          }
        }

        Object.keys(a).forEach(function (e) {
          var _t9 = a[e];
          var s = _t9["enabled"];
          var i = _t9["class"];

          if (s && i) {
            delete _t9["enabled"];
            delete _t9["clazz"];

            var _s2 = _this5.createPluginInstance(i, _t9);

            _this5.core.registerPlugin(e, _s2);
          }
        });
      }
    }, {
      key: "isEmptyOption",
      value: function isEmptyOption(e) {
        var _t10 = e.validators;
        return Object.keys(_t10).length === 0 && _t10.constructor === Object;
      }
    }, {
      key: "parseElement",
      value: function parseElement(e) {
        var _t11 = new RegExp("^".concat(this.opts.prefix, "([a-z0-9-]+)(___)*([a-z0-9-]+)*$"));

        var s = e.attributes.length;
        var a = {};
        var i = e.getAttribute("type");

        for (var n = 0; n < s; n++) {
          var _s3 = e.attributes[n].name;
          var r = e.attributes[n].value;

          if (this.opts.html5Input) {
            switch (true) {
              case "minlength" === _s3:
                a["stringLength"] = Object.assign({}, {
                  enabled: true,
                  min: parseInt(r, 10)
                }, a["stringLength"]);
                break;

              case "maxlength" === _s3:
                a["stringLength"] = Object.assign({}, {
                  enabled: true,
                  max: parseInt(r, 10)
                }, a["stringLength"]);
                break;

              case "pattern" === _s3:
                a["regexp"] = Object.assign({}, {
                  enabled: true,
                  regexp: r
                }, a["regexp"]);
                break;

              case "required" === _s3:
                a["notEmpty"] = Object.assign({}, {
                  enabled: true
                }, a["notEmpty"]);
                break;

              case "type" === _s3 && "color" === r:
                a["color"] = Object.assign({}, {
                  enabled: true,
                  type: "hex"
                }, a["color"]);
                break;

              case "type" === _s3 && "email" === r:
                a["emailAddress"] = Object.assign({}, {
                  enabled: true
                }, a["emailAddress"]);
                break;

              case "type" === _s3 && "url" === r:
                a["uri"] = Object.assign({}, {
                  enabled: true
                }, a["uri"]);
                break;

              case "type" === _s3 && "range" === r:
                a["between"] = Object.assign({}, {
                  enabled: true,
                  max: parseFloat(e.getAttribute("max")),
                  min: parseFloat(e.getAttribute("min"))
                }, a["between"]);
                break;

              case "min" === _s3 && i !== "date" && i !== "range":
                a["greaterThan"] = Object.assign({}, {
                  enabled: true,
                  min: parseFloat(r)
                }, a["greaterThan"]);
                break;

              case "max" === _s3 && i !== "date" && i !== "range":
                a["lessThan"] = Object.assign({}, {
                  enabled: true,
                  max: parseFloat(r)
                }, a["lessThan"]);
                break;
            }
          }

          var l = _t11.exec(_s3);

          if (l && l.length === 4) {
            var _e4 = this.toCamelCase(l[1]);

            a[_e4] = Object.assign({}, l[3] ? _defineProperty({}, this.toCamelCase(l[3]), this.normalizeValue(r)) : {
              enabled: "" === r || "true" === r
            }, a[_e4]);
          }
        }

        return {
          validators: a
        };
      }
    }, {
      key: "normalizeValue",
      value: function normalizeValue(e) {
        return e === "true" ? true : e === "false" ? false : e;
      }
    }, {
      key: "toUpperCase",
      value: function toUpperCase(e) {
        return e.charAt(1).toUpperCase();
      }
    }, {
      key: "toCamelCase",
      value: function toCamelCase(e) {
        return e.replace(/-./g, this.toUpperCase);
      }
    }]);

    return t;
  }(t$4);

  var o = /*#__PURE__*/function (_t) {
    _inherits(o, _t);

    var _super = _createSuper(o);

    function o() {
      var _this;

      _classCallCheck(this, o);

      _this = _super.call(this, {});
      _this.onValidHandler = _this.onFormValid.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(o, [{
      key: "install",
      value: function install() {
        var t = this.core.getFormElement();

        if (t.querySelectorAll('[type="submit"][name="submit"]').length) {
          throw new Error("Do not use `submit` for the name attribute of submit button");
        }

        this.core.on("core.form.valid", this.onValidHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.off("core.form.valid", this.onValidHandler);
      }
    }, {
      key: "onFormValid",
      value: function onFormValid() {
        var t = this.core.getFormElement();

        if (t instanceof HTMLFormElement) {
          t.submit();
        }
      }
    }]);

    return o;
  }(t$4);

  var e$3 = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.opts = t || {};
      _this.triggerExecutedHandler = _this.onTriggerExecuted.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        this.core.on("plugins.trigger.executed", this.triggerExecutedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.off("plugins.trigger.executed", this.triggerExecutedHandler);
      }
    }, {
      key: "onTriggerExecuted",
      value: function onTriggerExecuted(t) {
        if (this.opts[t.field]) {
          var _e3 = this.opts[t.field].split(" ");

          var _iterator = _createForOfIteratorHelper(_e3),
              _step;

          try {
            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              var _t2 = _step.value;

              var _e4 = _t2.trim();

              if (this.opts[_e4]) {
                this.core.revalidateField(_e4);
              }
            }
          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }
        }
      }
    }]);

    return e;
  }(t$4);

  var e$2 = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.opts = Object.assign({}, {
        excluded: e.defaultIgnore
      }, t);
      _this.ignoreValidationFilter = _this.ignoreValidation.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        this.core.registerFilter("element-ignored", this.ignoreValidationFilter);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.deregisterFilter("element-ignored", this.ignoreValidationFilter);
      }
    }, {
      key: "ignoreValidation",
      value: function ignoreValidation(t, _e2, i) {
        return this.opts.excluded.apply(this, [t, _e2, i]);
      }
    }], [{
      key: "defaultIgnore",
      value: function defaultIgnore(t, _e, i) {
        var r = !!(_e.offsetWidth || _e.offsetHeight || _e.getClientRects().length);

        var n = _e.getAttribute("disabled");

        return n === "" || n === "disabled" || _e.getAttribute("type") === "hidden" || !r;
      }
    }]);

    return e;
  }(t$4);

  var t$2 = /*#__PURE__*/function (_e) {
    _inherits(t, _e);

    var _super = _createSuper(t);

    function t(e) {
      var _this;

      _classCallCheck(this, t);

      _this = _super.call(this, e);
      _this.statuses = new Map();
      _this.opts = Object.assign({}, {
        onStatusChanged: function onStatusChanged() {}
      }, e);
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(t, [{
      key: "install",
      value: function install() {
        this.core.on("core.element.validating", this.elementValidatingHandler).on("core.element.validated", this.elementValidatedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("core.element.ignored", this.elementIgnoredHandler).on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.statuses.clear();
        this.core.off("core.element.validating", this.elementValidatingHandler).off("core.element.validated", this.elementValidatedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("core.element.ignored", this.elementIgnoredHandler).off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "areFieldsValid",
      value: function areFieldsValid() {
        return Array.from(this.statuses.values()).every(function (e) {
          return e === "Valid" || e === "NotValidated" || e === "Ignored";
        });
      }
    }, {
      key: "getStatuses",
      value: function getStatuses() {
        return this.statuses;
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        this.statuses.set(e.field, "NotValidated");
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        if (this.statuses.has(e.field)) {
          this.statuses["delete"](e.field);
        }

        this.opts.onStatusChanged(this.areFieldsValid());
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        this.statuses.set(e.field, "Validating");
        this.opts.onStatusChanged(false);
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        this.statuses.set(e.field, e.valid ? "Valid" : "Invalid");

        if (e.valid) {
          this.opts.onStatusChanged(this.areFieldsValid());
        } else {
          this.opts.onStatusChanged(false);
        }
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        this.statuses.set(e.field, "NotValidated");
        this.opts.onStatusChanged(false);
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        this.statuses.set(e.field, "Ignored");
        this.opts.onStatusChanged(this.areFieldsValid());
      }
    }]);

    return t;
  }(t$4);

  function s$2(s, a) {
    a.split(" ").forEach(function (a) {
      if (s.classList) {
        s.classList.add(a);
      } else if (" ".concat(s.className, " ").indexOf(" ".concat(a, " "))) {
        s.className += " ".concat(a);
      }
    });
  }

  function a(s, a) {
    a.split(" ").forEach(function (a) {
      s.classList ? s.classList.remove(a) : s.className = s.className.replace(a, "");
    });
  }

  function c(c, e) {
    var t = [];
    var f = [];
    Object.keys(e).forEach(function (s) {
      if (s) {
        e[s] ? t.push(s) : f.push(s);
      }
    });
    f.forEach(function (s) {
      return a(c, s);
    });
    t.forEach(function (a) {
      return s$2(c, a);
    });
  }

  function e$1(e, t) {
    var l = e.matches || e.webkitMatchesSelector || e["mozMatchesSelector"] || e["msMatchesSelector"];

    if (l) {
      return l.call(e, t);
    }

    var c = [].slice.call(e.parentElement.querySelectorAll(t));
    return c.indexOf(e) >= 0;
  }

  function t$1(t, l) {
    var c = t;

    while (c) {
      if (e$1(c, l)) {
        break;
      }

      c = c.parentElement;
    }

    return c;
  }

  var s$1 = /*#__PURE__*/function (_e) {
    _inherits(s, _e);

    var _super = _createSuper(s);

    function s(e) {
      var _this;

      _classCallCheck(this, s);

      _this = _super.call(this, e);
      _this.messages = new Map();
      _this.defaultContainer = document.createElement("div");
      _this.opts = Object.assign({}, {
        container: function container(e, t) {
          return _this.defaultContainer;
        }
      }, e);
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      _this.validatorValidatedHandler = _this.onValidatorValidated.bind(_assertThisInitialized(_this));
      _this.validatorNotValidatedHandler = _this.onValidatorNotValidated.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(s, [{
      key: "install",
      value: function install() {
        this.core.getFormElement().appendChild(this.defaultContainer);
        this.core.on("core.element.ignored", this.elementIgnoredHandler).on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler).on("core.validator.validated", this.validatorValidatedHandler).on("core.validator.notvalidated", this.validatorNotValidatedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.getFormElement().removeChild(this.defaultContainer);
        this.messages.forEach(function (e) {
          return e.parentNode.removeChild(e);
        });
        this.messages.clear();
        this.core.off("core.element.ignored", this.elementIgnoredHandler).off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler).off("core.validator.validated", this.validatorValidatedHandler).off("core.validator.notvalidated", this.validatorNotValidatedHandler);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this2 = this;

        var t = e.elements;

        if (t) {
          t.forEach(function (e) {
            var t = _this2.messages.get(e);

            if (t) {
              t.parentNode.removeChild(t);

              _this2.messages["delete"](e);
            }
          });
          this.prepareFieldContainer(e.field, t);
        }
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        var _this3 = this;

        if (!e.elements.length || !e.field) {
          return;
        }

        var t = e.elements[0].getAttribute("type");

        var _s2 = "radio" === t || "checkbox" === t ? [e.elements[0]] : e.elements;

        _s2.forEach(function (e) {
          if (_this3.messages.has(e)) {
            var _t = _this3.messages.get(e);

            _t.parentNode.removeChild(_t);

            _this3.messages["delete"](e);
          }
        });
      }
    }, {
      key: "prepareFieldContainer",
      value: function prepareFieldContainer(e, t) {
        var _this4 = this;

        if (t.length) {
          var _s12 = t[0].getAttribute("type");

          if ("radio" === _s12 || "checkbox" === _s12) {
            this.prepareElementContainer(e, t[0], t);
          } else {
            t.forEach(function (_s4) {
              return _this4.prepareElementContainer(e, _s4, t);
            });
          }
        }
      }
    }, {
      key: "prepareElementContainer",
      value: function prepareElementContainer(e, _s5, i) {
        var a;

        if ("string" === typeof this.opts.container) {
          var _e2 = "#" === this.opts.container.charAt(0) ? "[id=\"".concat(this.opts.container.substring(1), "\"]") : this.opts.container;

          a = this.core.getFormElement().querySelector(_e2);
        } else {
          a = this.opts.container(e, _s5);
        }

        var l = document.createElement("div");
        a.appendChild(l);
        c(l, {
          "fv-plugins-message-container": true
        });
        this.core.emit("plugins.message.placed", {
          element: _s5,
          elements: i,
          field: e,
          messageElement: l
        });
        this.messages.set(_s5, l);
      }
    }, {
      key: "getMessage",
      value: function getMessage(e) {
        return typeof e.message === "string" ? e.message : e.message[this.core.getLocale()];
      }
    }, {
      key: "onValidatorValidated",
      value: function onValidatorValidated(e) {
        var _s6 = e.elements;
        var i = e.element.getAttribute("type");
        var a = ("radio" === i || "checkbox" === i) && _s6.length > 0 ? _s6[0] : e.element;

        if (this.messages.has(a)) {
          var _s13 = this.messages.get(a);

          var _i = _s13.querySelector("[data-field=\"".concat(e.field, "\"][data-validator=\"").concat(e.validator, "\"]"));

          if (!_i && !e.result.valid) {
            var _i2 = document.createElement("div");

            _i2.innerHTML = this.getMessage(e.result);

            _i2.setAttribute("data-field", e.field);

            _i2.setAttribute("data-validator", e.validator);

            if (this.opts.clazz) {
              c(_i2, _defineProperty({}, this.opts.clazz, true));
            }

            _s13.appendChild(_i2);

            this.core.emit("plugins.message.displayed", {
              element: e.element,
              field: e.field,
              message: e.result.message,
              messageElement: _i2,
              meta: e.result.meta,
              validator: e.validator
            });
          } else if (_i && !e.result.valid) {
            _i.innerHTML = this.getMessage(e.result);
            this.core.emit("plugins.message.displayed", {
              element: e.element,
              field: e.field,
              message: e.result.message,
              messageElement: _i,
              meta: e.result.meta,
              validator: e.validator
            });
          } else if (_i && e.result.valid) {
            _s13.removeChild(_i);
          }
        }
      }
    }, {
      key: "onValidatorNotValidated",
      value: function onValidatorNotValidated(e) {
        var t = e.elements;

        var _s8 = e.element.getAttribute("type");

        var i = "radio" === _s8 || "checkbox" === _s8 ? t[0] : e.element;

        if (this.messages.has(i)) {
          var _t3 = this.messages.get(i);

          var _s14 = _t3.querySelector("[data-field=\"".concat(e.field, "\"][data-validator=\"").concat(e.validator, "\"]"));

          if (_s14) {
            _t3.removeChild(_s14);
          }
        }
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        var t = e.elements;

        var _s10 = e.element.getAttribute("type");

        var i = "radio" === _s10 || "checkbox" === _s10 ? t[0] : e.element;

        if (this.messages.has(i)) {
          var _t4 = this.messages.get(i);

          var _s15 = [].slice.call(_t4.querySelectorAll("[data-field=\"".concat(e.field, "\"]")));

          _s15.forEach(function (e) {
            _t4.removeChild(e);
          });
        }
      }
    }], [{
      key: "getClosestContainer",
      value: function getClosestContainer(e, t, _s) {
        var i = e;

        while (i) {
          if (i === t) {
            break;
          }

          i = i.parentElement;

          if (_s.test(i.className)) {
            break;
          }
        }

        return i;
      }
    }]);

    return s;
  }(t$4);

  var l = /*#__PURE__*/function (_e) {
    _inherits(l, _e);

    var _super = _createSuper(l);

    function l(e) {
      var _this;

      _classCallCheck(this, l);

      _this = _super.call(this, e);
      _this.results = new Map();
      _this.containers = new Map();
      _this.opts = Object.assign({}, {
        defaultMessageContainer: true,
        eleInvalidClass: "",
        eleValidClass: "",
        rowClasses: "",
        rowValidatingClass: ""
      }, e);
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.iconPlacedHandler = _this.onIconPlaced.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      _this.messagePlacedHandler = _this.onMessagePlaced.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(l, [{
      key: "install",
      value: function install() {
        var _t,
            _this2 = this;

        c(this.core.getFormElement(), (_t = {}, _defineProperty(_t, this.opts.formClass, true), _defineProperty(_t, "fv-plugins-framework", true), _t));
        this.core.on("core.element.ignored", this.elementIgnoredHandler).on("core.element.validating", this.elementValidatingHandler).on("core.element.validated", this.elementValidatedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("plugins.icon.placed", this.iconPlacedHandler).on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);

        if (this.opts.defaultMessageContainer) {
          this.core.registerPlugin("___frameworkMessage", new s$1({
            clazz: this.opts.messageClass,
            container: function container(e, t) {
              var _l = "string" === typeof _this2.opts.rowSelector ? _this2.opts.rowSelector : _this2.opts.rowSelector(e, t);

              var a = t$1(t, _l);
              return s$1.getClosestContainer(t, a, _this2.opts.rowPattern);
            }
          }));
          this.core.on("plugins.message.placed", this.messagePlacedHandler);
        }
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        var _t2;

        this.results.clear();
        this.containers.clear();
        c(this.core.getFormElement(), (_t2 = {}, _defineProperty(_t2, this.opts.formClass, false), _defineProperty(_t2, "fv-plugins-framework", false), _t2));
        this.core.off("core.element.ignored", this.elementIgnoredHandler).off("core.element.validating", this.elementValidatingHandler).off("core.element.validated", this.elementValidatedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("plugins.icon.placed", this.iconPlacedHandler).off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);

        if (this.opts.defaultMessageContainer) {
          this.core.off("plugins.message.placed", this.messagePlacedHandler);
        }
      }
    }, {
      key: "onIconPlaced",
      value: function onIconPlaced(e) {}
    }, {
      key: "onMessagePlaced",
      value: function onMessagePlaced(e) {}
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this3 = this;

        var s = e.elements;

        if (s) {
          s.forEach(function (e) {
            var s = _this3.containers.get(e);

            if (s) {
              var _t3;

              c(s, (_t3 = {}, _defineProperty(_t3, _this3.opts.rowInvalidClass, false), _defineProperty(_t3, _this3.opts.rowValidatingClass, false), _defineProperty(_t3, _this3.opts.rowValidClass, false), _defineProperty(_t3, "fv-plugins-icon-container", false), _t3));

              _this3.containers["delete"](e);
            }
          });
          this.prepareFieldContainer(e.field, s);
        }
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        var _this4 = this;

        e.elements.forEach(function (e) {
          var s = _this4.containers.get(e);

          if (s) {
            var _t4;

            c(s, (_t4 = {}, _defineProperty(_t4, _this4.opts.rowInvalidClass, false), _defineProperty(_t4, _this4.opts.rowValidatingClass, false), _defineProperty(_t4, _this4.opts.rowValidClass, false), _t4));
          }
        });
      }
    }, {
      key: "prepareFieldContainer",
      value: function prepareFieldContainer(e, t) {
        var _this5 = this;

        if (t.length) {
          var _s = t[0].getAttribute("type");

          if ("radio" === _s || "checkbox" === _s) {
            this.prepareElementContainer(e, t[0]);
          } else {
            t.forEach(function (t) {
              return _this5.prepareElementContainer(e, t);
            });
          }
        }
      }
    }, {
      key: "prepareElementContainer",
      value: function prepareElementContainer(e, i) {
        var _l2 = "string" === typeof this.opts.rowSelector ? this.opts.rowSelector : this.opts.rowSelector(e, i);

        var a = t$1(i, _l2);

        if (a !== i) {
          var _t5;

          c(a, (_t5 = {}, _defineProperty(_t5, this.opts.rowClasses, true), _defineProperty(_t5, "fv-plugins-icon-container", true), _t5));
          this.containers.set(i, a);
        }
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        var s = e.elements;
        var i = e.element.getAttribute("type");

        var _l3 = "radio" === i || "checkbox" === i ? s[0] : e.element;

        var a = this.containers.get(_l3);

        if (a) {
          var _t6;

          c(a, (_t6 = {}, _defineProperty(_t6, this.opts.rowInvalidClass, false), _defineProperty(_t6, this.opts.rowValidatingClass, true), _defineProperty(_t6, this.opts.rowValidClass, false), _t6));
        }
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        this.removeClasses(e.element, e.elements);
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        this.removeClasses(e.element, e.elements);
      }
    }, {
      key: "removeClasses",
      value: function removeClasses(e, s) {
        var _this6 = this;

        var i = e.getAttribute("type");

        var _l4 = "radio" === i || "checkbox" === i ? s[0] : e;

        s.forEach(function (e) {
          var _t7;

          c(e, (_t7 = {}, _defineProperty(_t7, _this6.opts.eleValidClass, false), _defineProperty(_t7, _this6.opts.eleInvalidClass, false), _t7));
        });
        var a = this.containers.get(_l4);

        if (a) {
          var _t8;

          c(a, (_t8 = {}, _defineProperty(_t8, this.opts.rowInvalidClass, false), _defineProperty(_t8, this.opts.rowValidatingClass, false), _defineProperty(_t8, this.opts.rowValidClass, false), _t8));
        }
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        var _this7 = this;

        var s = e.elements;
        var i = e.element.getAttribute("type");

        var _l5 = "radio" === i || "checkbox" === i ? s[0] : e.element;

        s.forEach(function (s) {
          var _t9;

          c(s, (_t9 = {}, _defineProperty(_t9, _this7.opts.eleValidClass, e.valid), _defineProperty(_t9, _this7.opts.eleInvalidClass, !e.valid), _t9));
        });
        var a = this.containers.get(_l5);

        if (a) {
          if (!e.valid) {
            var _t10;

            this.results.set(_l5, false);
            c(a, (_t10 = {}, _defineProperty(_t10, this.opts.rowInvalidClass, true), _defineProperty(_t10, this.opts.rowValidatingClass, false), _defineProperty(_t10, this.opts.rowValidClass, false), _t10));
          } else {
            this.results["delete"](_l5);
            var _e2 = true;
            this.containers.forEach(function (t, s) {
              if (t === a && _this7.results.get(s) === false) {
                _e2 = false;
              }
            });

            if (_e2) {
              var _t11;

              c(a, (_t11 = {}, _defineProperty(_t11, this.opts.rowInvalidClass, false), _defineProperty(_t11, this.opts.rowValidatingClass, false), _defineProperty(_t11, this.opts.rowValidClass, true), _t11));
            }
          }
        }
      }
    }]);

    return l;
  }(t$4);

  var i$2 = /*#__PURE__*/function (_e) {
    _inherits(i, _e);

    var _super = _createSuper(i);

    function i(e) {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, e);
      _this.icons = new Map();
      _this.opts = Object.assign({}, {
        invalid: "fv-plugins-icon--invalid",
        onPlaced: function onPlaced() {},
        onSet: function onSet() {},
        valid: "fv-plugins-icon--valid",
        validating: "fv-plugins-icon--validating"
      }, e);
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.core.on("core.element.validating", this.elementValidatingHandler).on("core.element.validated", this.elementValidatedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("core.element.ignored", this.elementIgnoredHandler).on("core.field.added", this.fieldAddedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.icons.forEach(function (e) {
          return e.parentNode.removeChild(e);
        });
        this.icons.clear();
        this.core.off("core.element.validating", this.elementValidatingHandler).off("core.element.validated", this.elementValidatedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("core.element.ignored", this.elementIgnoredHandler).off("core.field.added", this.fieldAddedHandler);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this2 = this;

        var t = e.elements;

        if (t) {
          t.forEach(function (e) {
            var t = _this2.icons.get(e);

            if (t) {
              t.parentNode.removeChild(t);

              _this2.icons["delete"](e);
            }
          });
          this.prepareFieldIcon(e.field, t);
        }
      }
    }, {
      key: "prepareFieldIcon",
      value: function prepareFieldIcon(e, t) {
        var _this3 = this;

        if (t.length) {
          var _i8 = t[0].getAttribute("type");

          if ("radio" === _i8 || "checkbox" === _i8) {
            this.prepareElementIcon(e, t[0]);
          } else {
            t.forEach(function (t) {
              return _this3.prepareElementIcon(e, t);
            });
          }
        }
      }
    }, {
      key: "prepareElementIcon",
      value: function prepareElementIcon(e, _i2) {
        var n = document.createElement("i");
        n.setAttribute("data-field", e);

        _i2.parentNode.insertBefore(n, _i2.nextSibling);

        c(n, {
          "fv-plugins-icon": true
        });
        var l = {
          classes: {
            invalid: this.opts.invalid,
            valid: this.opts.valid,
            validating: this.opts.validating
          },
          element: _i2,
          field: e,
          iconElement: n
        };
        this.core.emit("plugins.icon.placed", l);
        this.opts.onPlaced(l);
        this.icons.set(_i2, n);
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        var _this$setClasses;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses = {}, _defineProperty(_this$setClasses, this.opts.invalid, false), _defineProperty(_this$setClasses, this.opts.valid, false), _defineProperty(_this$setClasses, this.opts.validating, true), _this$setClasses));
        var _i3 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: "Validating"
        };
        this.core.emit("plugins.icon.set", _i3);
        this.opts.onSet(_i3);
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        var _this$setClasses2;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses2 = {}, _defineProperty(_this$setClasses2, this.opts.invalid, !e.valid), _defineProperty(_this$setClasses2, this.opts.valid, e.valid), _defineProperty(_this$setClasses2, this.opts.validating, false), _this$setClasses2));
        var _i4 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: e.valid ? "Valid" : "Invalid"
        };
        this.core.emit("plugins.icon.set", _i4);
        this.opts.onSet(_i4);
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        var _this$setClasses3;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses3 = {}, _defineProperty(_this$setClasses3, this.opts.invalid, false), _defineProperty(_this$setClasses3, this.opts.valid, false), _defineProperty(_this$setClasses3, this.opts.validating, false), _this$setClasses3));
        var _i5 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: "NotValidated"
        };
        this.core.emit("plugins.icon.set", _i5);
        this.opts.onSet(_i5);
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        var _this$setClasses4;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses4 = {}, _defineProperty(_this$setClasses4, this.opts.invalid, false), _defineProperty(_this$setClasses4, this.opts.valid, false), _defineProperty(_this$setClasses4, this.opts.validating, false), _this$setClasses4));
        var _i6 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: "Ignored"
        };
        this.core.emit("plugins.icon.set", _i6);
        this.opts.onSet(_i6);
      }
    }, {
      key: "setClasses",
      value: function setClasses(e, _i7, n, l) {
        var s = _i7.getAttribute("type");

        var d = "radio" === s || "checkbox" === s ? n[0] : _i7;

        if (this.icons.has(d)) {
          var _e2 = this.icons.get(d);

          c(_e2, l);
          return _e2;
        } else {
          return null;
        }
      }
    }]);

    return i;
  }(t$4);

  var i$1 = /*#__PURE__*/function (_e) {
    _inherits(i, _e);

    var _super = _createSuper(i);

    function i(e) {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, e);
      _this.invalidFields = new Map();
      _this.opts = Object.assign({}, {
        enabled: true
      }, e);
      _this.validatorHandler = _this.onValidatorValidated.bind(_assertThisInitialized(_this));
      _this.shouldValidateFilter = _this.shouldValidate.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.core.on("core.validator.validated", this.validatorHandler).on("core.field.added", this.fieldAddedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("core.element.validating", this.elementValidatingHandler).registerFilter("field-should-validate", this.shouldValidateFilter);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.invalidFields.clear();
        this.core.off("core.validator.validated", this.validatorHandler).off("core.field.added", this.fieldAddedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("core.element.validating", this.elementValidatingHandler).deregisterFilter("field-should-validate", this.shouldValidateFilter);
      }
    }, {
      key: "shouldValidate",
      value: function shouldValidate(e, _i, t, l) {
        var d = (this.opts.enabled === true || this.opts.enabled[e] === true) && this.invalidFields.has(_i) && !!this.invalidFields.get(_i).length && this.invalidFields.get(_i).indexOf(l) === -1;
        return !d;
      }
    }, {
      key: "onValidatorValidated",
      value: function onValidatorValidated(e) {
        var _i2 = this.invalidFields.has(e.element) ? this.invalidFields.get(e.element) : [];

        var t = _i2.indexOf(e.validator);

        if (e.result.valid && t >= 0) {
          _i2.splice(t, 1);
        } else if (!e.result.valid && t === -1) {
          _i2.push(e.validator);
        }

        this.invalidFields.set(e.element, _i2);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        if (e.elements) {
          this.clearInvalidFields(e.elements);
        }
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        this.clearInvalidFields(e.elements);
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        this.clearInvalidFields(e.elements);
      }
    }, {
      key: "clearInvalidFields",
      value: function clearInvalidFields(e) {
        var _this2 = this;

        e.forEach(function (e) {
          return _this2.invalidFields["delete"](e);
        });
      }
    }]);

    return i;
  }(t$4);

  var e = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.isFormValid = false;
      _this.opts = Object.assign({}, {
        aspNetButton: false,
        buttons: function buttons(t) {
          return [].slice.call(t.querySelectorAll('[type="submit"]:not([formnovalidate])'));
        }
      }, t);
      _this.submitHandler = _this.handleSubmitEvent.bind(_assertThisInitialized(_this));
      _this.buttonClickHandler = _this.handleClickEvent.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        var _this2 = this;

        if (!(this.core.getFormElement() instanceof HTMLFormElement)) {
          return;
        }

        var t = this.core.getFormElement();
        this.submitButtons = this.opts.buttons(t);
        t.setAttribute("novalidate", "novalidate");
        t.addEventListener("submit", this.submitHandler);
        this.hiddenClickedEle = document.createElement("input");
        this.hiddenClickedEle.setAttribute("type", "hidden");
        t.appendChild(this.hiddenClickedEle);
        this.submitButtons.forEach(function (t) {
          t.addEventListener("click", _this2.buttonClickHandler);
        });
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        var _this3 = this;

        var t = this.core.getFormElement();

        if (t instanceof HTMLFormElement) {
          t.removeEventListener("submit", this.submitHandler);
        }

        this.submitButtons.forEach(function (t) {
          t.removeEventListener("click", _this3.buttonClickHandler);
        });
        this.hiddenClickedEle.parentElement.removeChild(this.hiddenClickedEle);
      }
    }, {
      key: "handleSubmitEvent",
      value: function handleSubmitEvent(t) {
        this.validateForm(t);
      }
    }, {
      key: "handleClickEvent",
      value: function handleClickEvent(t) {
        var _e = t.currentTarget;

        if (_e instanceof HTMLElement) {
          if (this.opts.aspNetButton && this.isFormValid === true) ; else {
            var _e3 = this.core.getFormElement();

            _e3.removeEventListener("submit", this.submitHandler);

            this.clickedButton = t.target;
            var i = this.clickedButton.getAttribute("name");
            var s = this.clickedButton.getAttribute("value");

            if (i && s) {
              this.hiddenClickedEle.setAttribute("name", i);
              this.hiddenClickedEle.setAttribute("value", s);
            }

            this.validateForm(t);
          }
        }
      }
    }, {
      key: "validateForm",
      value: function validateForm(t) {
        var _this4 = this;

        t.preventDefault();
        this.core.validate().then(function (t) {
          if (t === "Valid" && _this4.opts.aspNetButton && !_this4.isFormValid && _this4.clickedButton) {
            _this4.isFormValid = true;

            _this4.clickedButton.removeEventListener("click", _this4.buttonClickHandler);

            _this4.clickedButton.click();
          }
        });
      }
    }]);

    return e;
  }(t$4);

  var i = /*#__PURE__*/function (_t) {
    _inherits(i, _t);

    var _super = _createSuper(i);

    function i(t) {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, t);
      _this.messages = new Map();
      _this.opts = Object.assign({}, {
        placement: "top",
        trigger: "click"
      }, t);
      _this.iconPlacedHandler = _this.onIconPlaced.bind(_assertThisInitialized(_this));
      _this.validatorValidatedHandler = _this.onValidatorValidated.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.documentClickHandler = _this.onDocumentClicked.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.tip = document.createElement("div");
        c(this.tip, _defineProperty({
          "fv-plugins-tooltip": true
        }, "fv-plugins-tooltip--".concat(this.opts.placement), true));
        document.body.appendChild(this.tip);
        this.core.on("plugins.icon.placed", this.iconPlacedHandler).on("core.validator.validated", this.validatorValidatedHandler).on("core.element.validated", this.elementValidatedHandler);

        if ("click" === this.opts.trigger) {
          document.addEventListener("click", this.documentClickHandler);
        }
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.messages.clear();
        document.body.removeChild(this.tip);
        this.core.off("plugins.icon.placed", this.iconPlacedHandler).off("core.validator.validated", this.validatorValidatedHandler).off("core.element.validated", this.elementValidatedHandler);

        if ("click" === this.opts.trigger) {
          document.removeEventListener("click", this.documentClickHandler);
        }
      }
    }, {
      key: "onIconPlaced",
      value: function onIconPlaced(t) {
        var _this2 = this;

        c(t.iconElement, {
          "fv-plugins-tooltip-icon": true
        });

        switch (this.opts.trigger) {
          case "hover":
            t.iconElement.addEventListener("mouseenter", function (e) {
              return _this2.show(t.element, e);
            });
            t.iconElement.addEventListener("mouseleave", function (t) {
              return _this2.hide();
            });
            break;

          case "click":
          default:
            t.iconElement.addEventListener("click", function (e) {
              return _this2.show(t.element, e);
            });
            break;
        }
      }
    }, {
      key: "onValidatorValidated",
      value: function onValidatorValidated(t) {
        if (!t.result.valid) {
          var _e2 = t.elements;

          var _i4 = t.element.getAttribute("type");

          var s = "radio" === _i4 || "checkbox" === _i4 ? _e2[0] : t.element;
          var o = typeof t.result.message === "string" ? t.result.message : t.result.message[this.core.getLocale()];
          this.messages.set(s, o);
        }
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(t) {
        if (t.valid) {
          var _e3 = t.elements;

          var _i5 = t.element.getAttribute("type");

          var s = "radio" === _i5 || "checkbox" === _i5 ? _e3[0] : t.element;
          this.messages["delete"](s);
        }
      }
    }, {
      key: "onDocumentClicked",
      value: function onDocumentClicked(t) {
        this.hide();
      }
    }, {
      key: "show",
      value: function show(t, _i3) {
        _i3.preventDefault();

        _i3.stopPropagation();

        if (!this.messages.has(t)) {
          return;
        }

        c(this.tip, {
          "fv-plugins-tooltip--hide": false
        });
        this.tip.innerHTML = "<div class=\"fv-plugins-tooltip__content\">".concat(this.messages.get(t), "</div>");
        var s = _i3.target;
        var o = s.getBoundingClientRect();

        var _this$tip$getBounding = this.tip.getBoundingClientRect(),
            l = _this$tip$getBounding.height,
            n = _this$tip$getBounding.width;

        var a = 0;
        var d = 0;

        switch (this.opts.placement) {
          case "bottom":
            a = o.top + o.height;
            d = o.left + o.width / 2 - n / 2;
            break;

          case "bottom-left":
            a = o.top + o.height;
            d = o.left;
            break;

          case "bottom-right":
            a = o.top + o.height;
            d = o.left + o.width - n;
            break;

          case "left":
            a = o.top + o.height / 2 - l / 2;
            d = o.left - n;
            break;

          case "right":
            a = o.top + o.height / 2 - l / 2;
            d = o.left + o.width;
            break;

          case "top-left":
            a = o.top - l;
            d = o.left;
            break;

          case "top-right":
            a = o.top - l;
            d = o.left + o.width - n;
            break;

          case "top":
          default:
            a = o.top - l;
            d = o.left + o.width / 2 - n / 2;
            break;
        }

        var c$1 = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        var r = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0;
        a = a + c$1;
        d = d + r;
        this.tip.setAttribute("style", "top: ".concat(a, "px; left: ").concat(d, "px"));
      }
    }, {
      key: "hide",
      value: function hide() {
        c(this.tip, {
          "fv-plugins-tooltip--hide": true
        });
      }
    }]);

    return i;
  }(t$4);

  var t = /*#__PURE__*/function (_e) {
    _inherits(t, _e);

    var _super = _createSuper(t);

    function t(e) {
      var _this;

      _classCallCheck(this, t);

      _this = _super.call(this, e);
      _this.handlers = [];
      _this.timers = new Map();

      var _t = document.createElement("div");

      _this.defaultEvent = !("oninput" in _t) ? "keyup" : "input";
      _this.opts = Object.assign({}, {
        delay: 0,
        event: _this.defaultEvent,
        threshold: 0
      }, e);
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(t, [{
      key: "install",
      value: function install() {
        this.core.on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.handlers.forEach(function (e) {
          return e.element.removeEventListener(e.event, e.handler);
        });
        this.handlers = [];
        this.timers.forEach(function (e) {
          return window.clearTimeout(e);
        });
        this.timers.clear();
        this.core.off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "prepareHandler",
      value: function prepareHandler(e, _t2) {
        var _this2 = this;

        _t2.forEach(function (_t3) {
          var i = [];

          if (!!_this2.opts.event && _this2.opts.event[e] === false) {
            i = [];
          } else if (!!_this2.opts.event && !!_this2.opts.event[e]) {
            i = _this2.opts.event[e].split(" ");
          } else if ("string" === typeof _this2.opts.event && _this2.opts.event !== _this2.defaultEvent) {
            i = _this2.opts.event.split(" ");
          } else {
            var _e2 = _t3.getAttribute("type");

            var s = _t3.tagName.toLowerCase();

            var n = "radio" === _e2 || "checkbox" === _e2 || "file" === _e2 || "select" === s ? "change" : _this2.ieVersion >= 10 && _t3.getAttribute("placeholder") ? "keyup" : _this2.defaultEvent;
            i = [n];
          }

          i.forEach(function (i) {
            var s = function s(i) {
              return _this2.handleEvent(i, e, _t3);
            };

            _this2.handlers.push({
              element: _t3,
              event: i,
              field: e,
              handler: s
            });

            _t3.addEventListener(i, s);
          });
        });
      }
    }, {
      key: "handleEvent",
      value: function handleEvent(e, _t4, i) {
        var _this3 = this;

        if (this.exceedThreshold(_t4, i) && this.core.executeFilter("plugins-trigger-should-validate", true, [_t4, i])) {
          var s = function s() {
            return _this3.core.validateElement(_t4, i).then(function (s) {
              _this3.core.emit("plugins.trigger.executed", {
                element: i,
                event: e,
                field: _t4
              });
            });
          };

          var n = this.opts.delay[_t4] || this.opts.delay;

          if (n === 0) {
            s();
          } else {
            var _e3 = this.timers.get(i);

            if (_e3) {
              window.clearTimeout(_e3);
            }

            this.timers.set(i, window.setTimeout(s, n * 1e3));
          }
        }
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        this.handlers.filter(function (_t5) {
          return _t5.field === e.field;
        }).forEach(function (e) {
          return e.element.removeEventListener(e.event, e.handler);
        });
        this.prepareHandler(e.field, e.elements);
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        this.handlers.filter(function (_t6) {
          return _t6.field === e.field && e.elements.indexOf(_t6.element) >= 0;
        }).forEach(function (e) {
          return e.element.removeEventListener(e.event, e.handler);
        });
      }
    }, {
      key: "exceedThreshold",
      value: function exceedThreshold(e, _t7) {
        var i = this.opts.threshold[e] === 0 || this.opts.threshold === 0 ? false : this.opts.threshold[e] || this.opts.threshold;

        if (!i) {
          return true;
        }

        var s = _t7.getAttribute("type");

        if (["button", "checkbox", "file", "hidden", "image", "radio", "reset", "submit"].indexOf(s) !== -1) {
          return true;
        }

        var n = this.core.getElementValue(e, _t7);
        return n.length >= i;
      }
    }]);

    return t;
  }(t$4);

  var index$1 = {
    Alias: e$4,
    Aria: i$3,
    Declarative: t$3,
    DefaultSubmit: o,
    Dependency: e$3,
    Excluded: e$2,
    FieldStatus: t$2,
    Framework: l,
    Icon: i$2,
    Message: s$1,
    Sequence: i$1,
    SubmitButton: e,
    Tooltip: i,
    Trigger: t
  };

  function s(s, t) {
    return s.classList ? s.classList.contains(t) : new RegExp("(^| )".concat(t, "( |$)"), "gi").test(s.className);
  }

  var index = {
    call: t$c,
    classSet: c,
    closest: t$1,
    fetch: e$6,
    format: r$2,
    hasClass: s,
    isValidDate: t$9
  };

  var p = {};

  exports.Plugin = t$4;
  exports.algorithms = index$3;
  exports.filters = index$2;
  exports.formValidation = r;
  exports.locales = p;
  exports.plugins = index$1;
  exports.utils = index;
  exports.validators = s$3;

  Object.defineProperty(exports, '__esModule', { value: true });

}));

/**
 * FormValidation (https://formvalidation.io), v1.9.0 (cbf8fab)
 * The best validation library for JavaScript
 * (c) 2013 - 2021 Nguyen Huu Phuoc <me@phuoc.ng>
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, (global.FormValidation = global.FormValidation || {}, global.FormValidation.plugins = global.FormValidation.plugins || {}, global.FormValidation.plugins.Bootstrap5 = factory()));
})(this, (function () { 'use strict';

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }

    subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    if (superClass) _setPrototypeOf(subClass, superClass);
  }

  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }

  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };

    return _setPrototypeOf(o, p);
  }

  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;

    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }

  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }

    return self;
  }

  function _possibleConstructorReturn(self, call) {
    if (call && (typeof call === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }

    return _assertThisInitialized(self);
  }

  function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct();

    return function _createSuperInternal() {
      var Super = _getPrototypeOf(Derived),
          result;

      if (hasNativeReflectConstruct) {
        var NewTarget = _getPrototypeOf(this).constructor;

        result = Reflect.construct(Super, arguments, NewTarget);
      } else {
        result = Super.apply(this, arguments);
      }

      return _possibleConstructorReturn(this, result);
    };
  }

  function _superPropBase(object, property) {
    while (!Object.prototype.hasOwnProperty.call(object, property)) {
      object = _getPrototypeOf(object);
      if (object === null) break;
    }

    return object;
  }

  function _get(target, property, receiver) {
    if (typeof Reflect !== "undefined" && Reflect.get) {
      _get = Reflect.get;
    } else {
      _get = function _get(target, property, receiver) {
        var base = _superPropBase(target, property);

        if (!base) return;
        var desc = Object.getOwnPropertyDescriptor(base, property);

        if (desc.get) {
          return desc.get.call(receiver);
        }

        return desc.value;
      };
    }

    return _get(target, property, receiver || target);
  }

  var e = FormValidation.utils.classSet;

  var t = FormValidation.utils.hasClass;

  var n = FormValidation.plugins.Framework;

  var l = /*#__PURE__*/function (_n) {
    _inherits(l, _n);

    var _super = _createSuper(l);

    function l(e) {
      var _this;

      _classCallCheck(this, l);

      _this = _super.call(this, Object.assign({}, {
        eleInvalidClass: "is-invalid",
        eleValidClass: "is-valid",
        formClass: "fv-plugins-bootstrap5",
        rowInvalidClass: "fv-plugins-bootstrap5-row-invalid",
        rowPattern: /^(.*)(col|offset)(-(sm|md|lg|xl))*-[0-9]+(.*)$/,
        rowSelector: ".row",
        rowValidClass: "fv-plugins-bootstrap5-row-valid"
      }, e));
      _this.eleValidatedHandler = _this.handleElementValidated.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(l, [{
      key: "install",
      value: function install() {
        _get(_getPrototypeOf(l.prototype), "install", this).call(this);

        this.core.on("core.element.validated", this.eleValidatedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        _get(_getPrototypeOf(l.prototype), "install", this).call(this);

        this.core.off("core.element.validated", this.eleValidatedHandler);
      }
    }, {
      key: "handleElementValidated",
      value: function handleElementValidated(n) {
        var _l = n.element.getAttribute("type");

        if (("checkbox" === _l || "radio" === _l) && n.elements.length > 1 && t(n.element, "form-check-input")) {
          var _l5 = n.element.parentElement;

          if (t(_l5, "form-check") && t(_l5, "form-check-inline")) {
            e(_l5, {
              "is-invalid": !n.valid,
              "is-valid": n.valid
            });
          }
        }
      }
    }, {
      key: "onIconPlaced",
      value: function onIconPlaced(n) {
        e(n.element, {
          "fv-plugins-icon-input": true
        });
        var _l3 = n.element.parentElement;

        if (t(_l3, "input-group")) {
          _l3.parentElement.insertBefore(n.iconElement, _l3.nextSibling);

          if (n.element.nextElementSibling && t(n.element.nextElementSibling, "input-group-text")) {
            e(n.iconElement, {
              "fv-plugins-icon-input-group": true
            });
          }
        }

        var i = n.element.getAttribute("type");

        if ("checkbox" === i || "radio" === i) {
          var _i = _l3.parentElement;

          if (t(_l3, "form-check")) {
            e(n.iconElement, {
              "fv-plugins-icon-check": true
            });

            _l3.parentElement.insertBefore(n.iconElement, _l3.nextSibling);
          } else if (t(_l3.parentElement, "form-check")) {
            e(n.iconElement, {
              "fv-plugins-icon-check": true
            });

            _i.parentElement.insertBefore(n.iconElement, _i.nextSibling);
          }
        }
      }
    }, {
      key: "onMessagePlaced",
      value: function onMessagePlaced(n) {
        n.messageElement.classList.add("invalid-feedback");
        var _l4 = n.element.parentElement;

        if (t(_l4, "input-group")) {
          _l4.appendChild(n.messageElement);

          e(_l4, {
            "has-validation": true
          });
          return;
        }

        var i = n.element.getAttribute("type");

        if (("checkbox" === i || "radio" === i) && t(n.element, "form-check-input") && t(_l4, "form-check") && !t(_l4, "form-check-inline")) {
          n.elements[n.elements.length - 1].parentElement.appendChild(n.messageElement);
        }
      }
    }]);

    return l;
  }(n);

  return l;

}));

/**
 * FormValidation (https://formvalidation.io), v1.9.0 (cbf8fab)
 * The best validation library for JavaScript
 * (c) 2013 - 2021 Nguyen Huu Phuoc <me@phuoc.ng>
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, (global.FormValidation = global.FormValidation || {}, global.FormValidation.plugins = global.FormValidation.plugins || {}, global.FormValidation.plugins.StartEndDate = factory()));
})(this, (function () { 'use strict';

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }

    subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    if (superClass) _setPrototypeOf(subClass, superClass);
  }

  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }

  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };

    return _setPrototypeOf(o, p);
  }

  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;

    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }

  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }

    return self;
  }

  function _possibleConstructorReturn(self, call) {
    if (call && (typeof call === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }

    return _assertThisInitialized(self);
  }

  function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct();

    return function _createSuperInternal() {
      var Super = _getPrototypeOf(Derived),
          result;

      if (hasNativeReflectConstruct) {
        var NewTarget = _getPrototypeOf(this).constructor;

        result = Reflect.construct(Super, arguments, NewTarget);
      } else {
        result = Super.apply(this, arguments);
      }

      return _possibleConstructorReturn(this, result);
    };
  }

  var t = FormValidation.Plugin;

  var e = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.fieldValidHandler = _this.onFieldValid.bind(_assertThisInitialized(_this));
      _this.fieldInvalidHandler = _this.onFieldInvalid.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        var _this2 = this;

        var t = this.core.getFields();
        this.startDateFieldOptions = t[this.opts.startDate.field];
        this.endDateFieldOptions = t[this.opts.endDate.field];

        var _e = this.core.getFormElement();

        this.core.on("core.field.valid", this.fieldValidHandler).on("core.field.invalid", this.fieldInvalidHandler).addField(this.opts.startDate.field, {
          validators: {
            date: {
              format: this.opts.format,
              max: function max() {
                var t = _e.querySelector("[name=\"".concat(_this2.opts.endDate.field, "\"]"));

                return t.value;
              },
              message: this.opts.startDate.message
            }
          }
        }).addField(this.opts.endDate.field, {
          validators: {
            date: {
              format: this.opts.format,
              message: this.opts.endDate.message,
              min: function min() {
                var t = _e.querySelector("[name=\"".concat(_this2.opts.startDate.field, "\"]"));

                return t.value;
              }
            }
          }
        });
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.removeField(this.opts.startDate.field);

        if (this.startDateFieldOptions) {
          this.core.addField(this.opts.startDate.field, this.startDateFieldOptions);
        }

        this.core.removeField(this.opts.endDate.field);

        if (this.endDateFieldOptions) {
          this.core.addField(this.opts.endDate.field, this.endDateFieldOptions);
        }

        this.core.off("core.field.valid", this.fieldValidHandler).off("core.field.invalid", this.fieldInvalidHandler);
      }
    }, {
      key: "onFieldInvalid",
      value: function onFieldInvalid(t) {
        switch (t) {
          case this.opts.startDate.field:
            this.startDateValid = false;
            break;

          case this.opts.endDate.field:
            this.endDateValid = false;
            break;
        }
      }
    }, {
      key: "onFieldValid",
      value: function onFieldValid(t) {
        switch (t) {
          case this.opts.startDate.field:
            this.startDateValid = true;

            if (this.endDateValid === false) {
              this.core.revalidateField(this.opts.endDate.field);
            }

            break;

          case this.opts.endDate.field:
            this.endDateValid = true;

            if (this.startDateValid === false) {
              this.core.revalidateField(this.opts.startDate.field);
            }

            break;
        }
      }
    }]);

    return e;
  }(t);

  return e;

}));
