/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
export default `
  scalar JSON
  scalar JSONObject

  type List {
    id_wishlist: Int
    name: String
    listUrl: String
    shareUrl: String
    default: Int
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

  type ProductListResponse {
    datas: JSONObject
  }

  type Response {
    success: Boolean!
    message: String!
  }

  type Query {
    products(listId: Int!, url: String!): ProductListResponse
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
