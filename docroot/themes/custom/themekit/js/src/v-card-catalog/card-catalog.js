import Vue from 'vue'
import App from './App.vue'
import { ApolloClient } from 'apollo-client'
import { HttpLink } from 'apollo-link-http'
// import { InMemoryCache } from 'apollo-cache-inmemory'
import VueApollo from 'vue-apollo'
import { InMemoryCache, IntrospectionFragmentMatcher } from 'apollo-cache-inmemory';
import introspectionQueryResultData from '../fragmentTypes.json';


const fragmentMatcher = new IntrospectionFragmentMatcher({
  introspectionQueryResultData
});

const httpLinkOptions = new HttpLink({
  credentials: 'same-origin'
});

// Create the apollo client
const apolloClient = new ApolloClient({
  link: new HttpLink(httpLinkOptions),
  // cache: new InMemoryCache(),
  cache: new InMemoryCache({ fragmentMatcher }),
  connectToDevTools: true
})

// Install the vue plugin
Vue.use(VueApollo)

const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
})


// IE Object doesn't support property or method 'assign'
if (typeof Object.assign != 'function') {
  Object.assign = function(target) {
    'use strict';
    if (target == null) {
      throw new TypeError('Cannot convert undefined or null to object');
    }

    target = Object(target);
    for (var index = 1; index < arguments.length; index++) {
      var source = arguments[index];
      if (source != null) {
        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }
    }
    return target;
  };
}


// IE 11 Object doesn't support property or method 'entries'
if (!Object.entries)
  Object.entries = function( obj ){
    var ownProps = Object.keys( obj ),
      i = ownProps.length,
      resArray = new Array(i); // preallocate the Array
    while (i--)
      resArray[i] = [ownProps[i], obj[ownProps[i]]];

    return resArray;
  };
//
// if (!String.prototype.includes) {
//   String.prototype.includes = function(search, start) {
//     if (typeof start !== 'number') {
//       start = 0;
//     }
//
//     if (start + search.length > this.length) {
//       return false;
//     } else {
//       return this.indexOf(search, start) !== -1;
//     }
//   };
// }

window.cardCatalog = new Vue({
  el: '#card-catalog',
  provide: apolloProvider.provide(),
  template: '<App/>',
  components: { App }
});
