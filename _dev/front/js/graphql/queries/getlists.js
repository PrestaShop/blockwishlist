import gql from 'graphql-tag';

export default gql`
  {
    lists {
      id
      title
      numbersProduct
    }
  }
`;
