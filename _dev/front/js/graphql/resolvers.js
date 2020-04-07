export default {
  Query: {
    products: (root, args, context) => [
      {
        id: 1,
        name: 'Gnark produit',
        price: '1.50'
      }
    ],
    lists: () => [
      {
        id: 1,
        title: 'Titre de liste 1'
      },
      {
        id: 2,
        title: 'Titre de liste 2'
      },
      {
        id: 3,
        title: 'Titre de liste 3'
      },
      {
        id: 4,
        title: 'Titre de liste 4'
      }
    ]
  }
};
