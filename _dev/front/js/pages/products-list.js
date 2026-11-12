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
import prestashop from 'prestashop';
import wishlistAddProductToCartUrl from 'wishlistAddProductToCartUrl';
import EventBus from '../lib/event-bus';
import refresh from '../lib/refresh';

const getList = (product) => {
  const {listId, listName} = product.closest('.wishlist-products-container').dataset;

  return {listId: parseInt(listId, 10), listName};
};

const addToCart = async (product, button) => {
  const {
    addToCartUrl, idProduct, idProductAttribute, idCustomization, quantity, url,
  } = product.dataset;

  if (!addToCartUrl) {
    window.location.href = url;

    return;
  }

  button.disabled = true;

  try {
    const datas = new FormData();
    datas.append('qty', quantity);
    datas.append('id_product', idProduct);
    datas.append('id_customization', idCustomization);

    const response = await fetch(`${addToCartUrl}&action=update`, {
      method: 'POST',
      headers: {Accept: 'application/json, text/javascript'},
      body: datas,
    });
    const resp = await response.json();

    EventBus.$emit('refetchList');
    prestashop.emit('updateCart', {
      reason: {
        idProduct,
        idProductAttribute,
        idCustomization,
        linkAction: 'add-to-cart',
      },
      resp,
    });

    const {listId} = getList(product);
    await fetch(
      `${wishlistAddProductToCartUrl}&params[idWishlist]=${listId}&params[id_product]=${idProduct}`
        + `&params[id_product_attribute]=${idProductAttribute}&params[quantity]=${quantity}`,
      {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          Accept: 'application/json, text/javascript, */*; q=0.01',
        },
      },
    );
  } catch (error) {
    prestashop.emit('handleError', {
      eventType: 'addProductToCart',
      resp: error,
    });
  }
};

/**
 * The page of the products of a list, rendered by the server and reloaded after each change
 */
export default function initProductsListPage() {
  document.addEventListener('click', (event) => {
    const product = event.target.closest('.wishlist-products-container .wishlist-product');

    if (!product) {
      return;
    }

    const addToCartButton = event.target.closest('.wishlist-product-addtocart');

    if (addToCartButton) {
      addToCart(product, addToCartButton);

      return;
    }

    if (event.target.closest('.wishlist-product-bottom .wishlist-button-add')) {
      EventBus.$emit('showDeleteWishlist', {
        detail: {
          ...getList(product),
          productId: product.dataset.idProduct,
          productAttributeId: product.dataset.idProductAttribute,
        },
      });
    }
  });

  EventBus.$on('refetchList', () => refresh('.wishlist-products-container'));
}
