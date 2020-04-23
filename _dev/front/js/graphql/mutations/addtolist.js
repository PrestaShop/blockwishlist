import gql from 'graphql-tag';

export default gql`
  mutation addToList($listId: Int!, $productId: Int!, $userId: Int!) {
    addToList(listId: $listId, productId: $productId, userId: $userId) {
      id
      title
    }
  }
`;
