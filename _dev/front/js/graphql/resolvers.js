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
     * Get product from a list
     */
    products: async (root, {url, listId}, context) => {
      let response = await fetch(`${url}&params[id_wishlist]=${listId}`);

      let datas = await response.json();

      EventBus.$emit('paginate', {
        detail: {
          total: 30,
          minShown: 1,
          maxShown: 20,
          pageNumber: 2,
          currentPage: 1
        }
      });

      return datas;
    },
    /**
     * Get every lists from User
     */
    lists: async (root, {url}, context) => {
      let response = await fetch(url);

      let datas = await response.json();
      console.log(datas);

      return datas.wishlists;
    }
  },
  Mutation: {
    /**
     * Create a list based on a name and an userId
     *
     * @param {String} name The name of the list
     * @param {Int} userId The ID of the user you want to create a list on
     */
    createList: async (root, {name, url}, context) => {
      let response = await fetch(url + `&params[name]=${name}`, {
        method: 'POST'
      });

      let datas = await response.json();

      return datas;
    },
    /**
     * Get a share url for a list
     *
     * @param {ID} listId ID of the list
     * @param {ID} userId ID of the user
     */
    shareList: (root, {listId, userId}, context) => {
      return {
        url: 'http://url.fr'
      };
    },
    /**
     * Rename a list
     *
     * @param {String} {name New name of the list
     * @param {Int} userId Id of the user
     * @param {Int} listId} ID of the list to be renamed
     */
    renameList: async (root, {name, listId, url}, context) => {
      let response = await fetch(url + `&params[name]=${name}&params[idWishlist]=${listId}`, {
        method: 'POST'
      });

      let datas = await response.json();

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
    addToList: async (root, {listId, url, productId, quantity, productAttributeId}, context) => {
      let response = await fetch(
        url +
          `&params[id_product]=${productId}&params[idWishlist]=${listId}&params[quantity]=${quantity}&params[id_product_attribute]=${productAttributeId}`,
        {
          method: 'POST'
        }
      );

      let datas = await response.json();

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
    removeFromList: async (root, {listId, productId, url, productAttributeId}, context) => {
      let response = await fetch(
        url +
          `&params[id_product]=${productId}&params[idWishlist]=${listId}&params[id_product_attribute]=${productAttributeId}`,
        {
          method: 'POST'
        }
      );

      let datas = await response.json();

      return datas;
    },
    /**
     * Remove a list
     *
     * @param {Int} {listId} The list id
     *
     * @returns {JSON} a JSON success or failed response
     */
    deleteList: async (root, {listId, url}, context) => {
      let response = await fetch(url + `&params[idWishlist]=${listId}`, {
        method: 'POST'
      });

      let datas = await response.json();

      return datas;
    }
  }
};
