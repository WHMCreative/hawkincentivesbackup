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

window.cardCatalog = new Vue({
  el: '#card-catalog',
  provide: apolloProvider.provide(),
  template: '<App/>',
  components: { App }
});
