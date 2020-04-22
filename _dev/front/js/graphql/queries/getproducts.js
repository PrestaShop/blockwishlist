import gql from 'graphql-tag';

export default gql`
  query getProducts($listId: Int!) {
    products(listId: $listId) {
      id
      name
      price
    }
  }
`;
