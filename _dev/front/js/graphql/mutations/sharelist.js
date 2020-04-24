import gql from 'graphql-tag'

export default gql`
  mutation shareList($listId: Int!, $userId: Int!) {
    shareList(listId: $listId, userId: $userId) {
      url
    }
  }
`
