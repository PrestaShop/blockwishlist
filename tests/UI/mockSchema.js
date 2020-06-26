import {makeExecutableSchema} from 'graphql-tools';
import schema from '@graphqlFiles/types';
import resolvers from './mockResolvers';

export default makeExecutableSchema({
  typeDefs: schema,
  resolvers
});
