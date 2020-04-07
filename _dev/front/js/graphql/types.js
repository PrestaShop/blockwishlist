export default `
  type Product {
    id: Int
    name: String
    price: String
  }

  type List {
    id: Int
    title: String
  }

  type Query {
    products: [Product]
    lists: [List]
  }
`;
