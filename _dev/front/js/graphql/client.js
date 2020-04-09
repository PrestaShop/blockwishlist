import {ApolloClient} from 'apollo-client';
import {SchemaLink} from 'apollo-link-schema';
import {InMemoryCache} from 'apollo-cache-inmemory';
import link from './link';

/**
 * Enabling client side cache
 */
const cache = new InMemoryCache();

/**
 * Creating the ApolloClient managing cache and schemas
 */
export default new ApolloClient({
  link: new SchemaLink({schema: link}),
  cache
});
