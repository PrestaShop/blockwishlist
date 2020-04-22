import gql from 'graphql-tag';

export default gql`
  mutation renameList($name: String!, $userId: Int!, $listId: Int!) {
    renameList(name: $name, userId: $userId, listId: $listId) {
      id
      title
    }
  }
`;
