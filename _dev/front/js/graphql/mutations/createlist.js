import gql from 'graphql-tag';

export default gql`
  mutation createList($name: String!, $userId: Int!) {
    createList(name: $name, userId: $userId) {
      id
      title
    }
  }
`;
