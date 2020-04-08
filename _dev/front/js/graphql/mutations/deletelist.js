import gql from 'graphql-tag';

export default gql`
  mutation deleteList($listId: Int!) {
    deleteList(listId: $listId) {
      id
      title
      numbersProduct
    }
  }
`;
