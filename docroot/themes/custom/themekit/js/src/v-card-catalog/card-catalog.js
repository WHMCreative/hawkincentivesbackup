import Vue from 'vue'
import App from './App.vue'
import { ApolloClient } from 'apollo-client'
import { HttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory'
import VueApollo from 'vue-apollo'
import { InMemoryCache, IntrospectionFragmentMatcher } from 'apollo-cache-inmemory';
import introspectionQueryResultData from '../fragmentTypes.json';


const fragmentMatcher = new IntrospectionFragmentMatcher({
  introspectionQueryResultData
});

const httpLinkOptions = new HttpLink({
  credentials: 'same-origin'
});

export default new ApolloClient({
  // By default, this client will send queries to the
  //  `/graphql` endpoint on the same host
  // Pass the configuration option { uri: YOUR_GRAPHQL_API_URL } to the `HttpLink` to connect
  // to a different host
  link: new HttpLink(httpLinkOptions),
  cache: new InMemoryCache({ fragmentMatcher }),
  connectToDevTools: true,
});

// Create the apollo client
const apolloClient = new ApolloClient({
  link: httpLinkOptions,
  cache: new InMemoryCache(),
  connectToDevTools: true,
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
