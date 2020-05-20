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
export default `
  type Product {
    id: Int
    name: String
    price: String
  }

  type List {
    id_wishlist: Int
    name: String
    nbProducts: Int
  }

  type ShareUrl {
    url: String
  }

  type CreateResponse {
    datas: List
    success: Boolean!
    message: String!
  }

  type Response {
    success: Boolean!
    message: String!
  }

  type Query {
    products(listId: Int!, url: String!): [CreateResponse]
    lists(url: String!): [List]
  }

  type Mutation {
    createList(name: String!, url: String!): CreateResponse
    shareList(listId: String!, userId: Int!): ShareUrl
    renameList(name: String!, url: String!, listId: Int!): Response
    addToList(listId: Int!, productId: Int!, quantity: Int!, productAttributeId: Int!, url: String!): Response
    removeFromList(listId: Int!, productId: Int!, productAttributeId: Int!, url: String!): Response
    deleteList(listId: Int!, url: String!): Response
  }
`;
