import {makeExecutableSchema} from 'graphql-tools';
import resolvers from './resolvers';
import typeDefs from './types';

/**
 * Generate SchemaLink that ApolloClient needs to understand schemas
 * and link resolvers to schemas
 */
export default makeExecutableSchema({
  typeDefs,
  resolvers
});
