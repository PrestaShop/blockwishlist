export default `
  type Product {
    id: Int
    name: String
    price: String
  }

  type List {
    id: Int
    title: String
    numbersProduct: Int
  }

  type ShareUrl {
    url: String
  }

  type Query {
    products: [Product]
    lists: [List]
  }

  type Mutation {
    createList(name: String!, userId: Int!): [List]
    shareList(listId: String!, userId: Int!): ShareUrl
    renameList(name: String!, userId: Int!, listId: Int!): [List]
    addToList(listId: Int!, productId: Int!, userId: Int!): List
    removeFromList(listId: Int!, productId: Int!): List
    deleteList(listId: Int!): [List]
  }
`
