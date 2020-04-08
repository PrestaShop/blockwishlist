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
        title: 'Titre de liste 1',
        numbersProduct: 8
      },
      {
        id: 2,
        title: 'Titre de liste 2',
        numbersProduct: 5
      },
      {
        id: 3,
        title: 'Titre de liste 3',
        numbersProduct: 1
      },
      {
        id: 4,
        title: 'Titre de liste 4',
        numbersProduct: 4
      }
    ]
  },
  Mutation: {
    createList: (root, {name, userId}, context) => {
      return [
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
      ];
    },
    renameList: (root, {name, userId, listId}, context) => {
      return [
        {
          id: 1,
          title: 'Renamed'
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
      ];
    },
    addToList: (root, {listId, userId, productId}, context) => {
      console.log(listId, userId, productId);
      return {
        id: 1,
        title: 'Titre de liste 1'
      };
    },
    deleteList: (root, {listId}, context) => {
      return [
        {
          id: 1,
          title: 'Titre de liste 1',
          numbersProduct: 4
        },
        {
          id: 2,
          title: 'Titre de liste 2',
          numbersProduct: 4
        },
        {
          id: 3,
          title: 'Titre de liste 3',
          numbersProduct: 4
        }
      ];
    }
  }
};
