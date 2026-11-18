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

/**
 * Append the action parameters to a module controller URL, which always carries a query string
 *
 * @param {String} url
 * @param {Object} params
 *
 * @returns {String}
 */
const withParams = (url, params) => Object.keys(params).reduce(
  (result, key) => `${result}&params[${key}]=${encodeURIComponent(params[key])}`,
  url,
);

const post = async (url, params) => {
  const response = await fetch(withParams(url, params), {
    method: 'POST',
  });

  return response.json();
};

/**
 * Get every list of the customer
 *
 * @param {String} url
 *
 * @returns {Array}
 */
export const getLists = async (url) => {
  const response = await fetch(url);
  const datas = await response.json();

  return datas.wishlists || [];
};

export const createList = (url, name) => post(url, {name});

export const renameList = (url, listId, name) => post(url, {name, idWishList: listId});

export const deleteList = (url, listId) => post(url, {idWishList: listId});

export const addToList = async (url, {
  listId, productId, quantity, productAttributeId,
}) => {
  const datas = await post(url, {
    id_product: productId,
    idWishList: listId,
    quantity,
    id_product_attribute: productAttributeId,
  });

  if (datas.success) {
    window.productsAlreadyTagged.push({
      id_product: productId.toString(),
      id_wishlist: listId.toString(),
      quantity: quantity.toString(),
      id_product_attribute: productAttributeId.toString(),
    });
  }

  return datas;
};

export const removeFromList = async (url, {listId, productId, productAttributeId}) => {
  const datas = await post(url, {
    id_product: productId,
    idWishList: listId,
    id_product_attribute: productAttributeId,
  });

  if (datas.success) {
    window.productsAlreadyTagged = window.productsAlreadyTagged.filter(
      (e) => e.id_product !== productId.toString()
        || (e.id_product_attribute !== productAttributeId.toString() && e.id_product === productId.toString())
        || e.id_wishlist !== listId.toString(),
    );
  }

  return datas;
};
