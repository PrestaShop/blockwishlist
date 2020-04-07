import {ApolloClient} from 'apollo-client';
import {SchemaLink} from 'apollo-link-schema';
import {InMemoryCache} from 'apollo-cache-inmemory';
import link from './link';

const cache = new InMemoryCache();

export default new ApolloClient({
  link: new SchemaLink({schema: link}),
  cache,
});
