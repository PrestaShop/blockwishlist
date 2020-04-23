import gql from 'graphql-tag';

export default gql`
  mutation removeFromList($listId: Int!, $productId: Int!, $userId: Int!) {
    removeFromList(listId: $listId, productId: $productId, userId: $userId) {
      id
      title
      numbersProduct
    }
  }
`;
