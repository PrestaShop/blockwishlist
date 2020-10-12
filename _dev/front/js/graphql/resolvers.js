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
import headers from '@constants/headers';
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
    products: async (root, {url}) => {
      const response = await fetch(`${url}&from-xhr`, {
        headers: headers.products,
      });

      const datas = await response.json();

      EventBus.$emit('paginate', {
        detail: {
          total: datas.pagination.total_items,
          minShown: datas.pagination.items_shown_from,
          maxShown: datas.pagination.items_shown_to,
          pageNumber: datas.pagination.pages_count,
          pages: datas.pagination.pages,
          display: datas.pagination.should_be_displayed,
          currentPage: datas.pagination.current_page,
        },
      });

      window.history.pushState(datas, document.title, datas.current_url);
      window.scrollTo(0, 0);

      return {
        datas: {
          products: datas.products,
          pagination: datas.pagination,
          current_url: datas.current_url,
          sort_orders: datas.sort_orders,
          sort_selected: datas.sort_selected,
        },
      };
    },
    /**
     * Get every lists from User
     */
    lists: async (root, {url}) => {
      const response = await fetch(url);

      const datas = await response.json();

      return datas.wishlists;
    },
  },
  Mutation: {
    /**
     * Create a list based on a name and an userId
     *
     * @param {String} name The name of the list
     * @param {Int} userId The ID of the user you want to create a list on
     */
    createList: async (root, {name, url}) => {
      const nameEncoded = encodeURIComponent(name);

      const response = await fetch(`${url}&params[name]=${nameEncoded}`, {
        method: 'POST',
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
        method: 'POST',
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
    addToList: async (root, {
      listId, url, productId, quantity, productAttributeId,
    }) => {
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
          id_product_attribute: productAttributeId.toString(),
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
    removeFromList: async (root, {
      listId, productId, url, productAttributeId,
    }) => {
      /* eslint-disable */
      const response = await fetch(
        `${url}&params[id_product]=${productId}&params[idWishList]=${listId}&params[id_product_attribute]=${productAttributeId}`,
        {
          method: 'POST'
        }
      );
      /* eslint-enable */

      const datas = await response.json();

      if (datas.success) {
        // eslint-disable-next-line
        productsAlreadyTagged = productsAlreadyTagged.filter(
          (e) => e.id_product !== productId.toString()
            || (e.id_product_attribute !== productAttributeId.toString() && e.id_product === productId.toString())
            || e.id_wishlist !== listId.toString(),
        );
      }

      return datas;
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
        method: 'POST',
      });

      const datas = await response.json();

      return datas;
    },
  },
};
