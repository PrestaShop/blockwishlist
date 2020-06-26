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

import EventBus from '@components/EventBus';
import GraphQLJSON, {GraphQLJSONObject} from 'graphql-type-json';

/**
 * Resolvers linked to schemas definitions
 */
export default {
  JSON: GraphQLJSON,
  JSONObject: GraphQLJSONObject,
  Query: {
    /**
     * Get every lists from User
     */
    lists: async (root, {url}) => {
      return [
        {id_wishlist: 1, name: 'wishlist 1', nbProducts: 5, listUrl: '#'},
        {id_wishlist: 2, name: 'wishlist 2', nbProducts: 3, listUrl: '#'}
      ];
    }
  },
  Mutation: {
    /**
     * Create a list based on a name and an userId
     *
     * @param {String} name The name of the list
     * @param {Int} userId The ID of the user you want to create a list on
     */
    createList: async (root, {name, url}) => {
      const response = await fetch(`${url}&params[name]=${name}`, {
        method: 'POST'
      });

      const datas = await response.json();

      return datas;
    },
    /**
     * Rename a list
     *
     * @param {String} {name New name of the list
     * @param {Int} userId Id of the user
     * @param {Int} listId} ID of the list to be renamed
     */
    renameList: async (root, {name, listId, url}) => {
      const response = await fetch(`${url}&params[name]=${name}&params[idWishList]=${listId}`, {
        method: 'POST'
      });

      const datas = await response.json();

      return datas;
    },
    /**
     * Add a product to a list
     *
     * @param {Int} listId The list id
     * @param {Int} userId The user id
     * @param {Int} productId The product id to be added to the list id
     *
     * @returns {JSON} A success or failed response
     */
    addToList: async (root, {listId, url, productId, quantity, productAttributeId}) => {
      /* eslint-disable */
      const response = await fetch(
        `${url}&params[id_product]=${productId}&params[idWishList]=${listId}&params[quantity]=${quantity}&params[id_product_attribute]=${productAttributeId}`,
        {
          method: 'POST'
        }
      );
      /* eslint-enable */

      const datas = await response.json();

      if (datas.success) {
        // eslint-disable-next-line
        productsAlreadyTagged.push({
          id_product: productId.toString(),
          id_wishlist: listId.toString(),
          quantity: quantity.toString(),
          id_product_attribute: productAttributeId.toString()
        });
      }

      return datas;
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
    removeFromList: async (root, {listId, productId, url, productAttributeId}) => {
      return {
        message: 'Success',
        success: true
      };
    },
    /**
     * Remove a list
     *
     * @param {Int} {listId} The list id
     *
     * @returns {JSON} a JSON success or failed response
     */
    deleteList: async (root, {listId, url}) => {
      const response = await fetch(`${url}&params[idWishList]=${listId}`, {
        method: 'POST'
      });

      const datas = await response.json();

      return datas;
    }
  }
};
