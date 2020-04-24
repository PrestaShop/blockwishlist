/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Resolvers linked to schemas definitions
 */
export default {
  Query: {
    /**
     * Get product from a list
     */
    products: (root, args, context) => [
      {
        id: 1,
        name: 'Gnark produit',
        price: '1.50'
      }
    ],
    /**
     * Get every lists from User
     */
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
    /**
     * Create a list based on a name and an userId
     *
     * @param {String} name The name of the list
     * @param {Int} userId The ID of the user you want to create a list on
     */
    createList: (root, {name, userId}, context) => [
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
    ],
    /**
     * Get a share url for a list
     *
     * @param {ID} listId ID of the list
     * @param {ID} userId ID of the user
     */
    shareList: (root, {listId, userId}, context) => {
      return {
        url: 'http://url.fr'
      }
    },
    /**
     * Rename a list
     *
     * @param {String} {name New name of the list
     * @param {Int} userId Id of the user
     * @param {Int} listId} ID of the list to be renamed
     */
    renameList: (root, {name, userId, listId}, context) => [
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
    ],
    /**
     * Add a product to a list
     *
     * @param {Int} listId The list id
     * @param {Int} userId The user id
     * @param {Int} productId The product id to be added to the list id
     *
     * @returns {JSON} A success or failed response
     */
    addToList: (root, {listId, userId, productId}, context) => {
      console.log(listId, userId, productId)
      return {
        id: 1,
        title: 'Titre de liste 1'
      }
    },
    /**
     * Remove a product from a list
     *
     * @param {Int} listId The list id
     * @param {Int} userId The user id
     * @param {Int} productId The product id to be removed from the list id
     *
     * @returns {JSON} A success or failed response
     */
    removeFromList: (root, {listId, productId, userId}, context) => ({
      id: 1,
      title: 'Titre de liste 1',
      numbersProduct: 2
    }),
    /**
     * Remove a list
     *
     * @param {Int} {listId} The list id
     *
     * @returns {JSON} a JSON success or failed response
     */
    deleteList: (root, {listId}, context) => [
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
    ]
  }
}
