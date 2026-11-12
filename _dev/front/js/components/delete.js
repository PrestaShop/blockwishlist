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
import {deleteList, removeFromList} from '../api';
import EventBus, {notify} from '../lib/event-bus';
import createModal from '../lib/modal';

/**
 * Confirm the deletion of a list, or the removal of a product from a list
 *
 * @param {HTMLElement} root
 */
export default function initDelete(root) {
  const modal = createModal(root);
  const form = root.querySelector('form');
  const message = root.querySelector('.modal-text');
  let target = {};

  EventBus.$on('showDeleteWishlist', (event) => {
    target = event.detail;
    const isProduct = Boolean(target.productId);

    root.querySelectorAll('.js-wishlist-delete-product').forEach((element) => {
      element.hidden = !isProduct;
    });
    root.querySelectorAll('.js-wishlist-delete-list').forEach((element) => {
      element.hidden = isProduct;
    });

    if (message) {
      message.dataset.text = message.dataset.text || message.textContent;
      message.textContent = message.dataset.text.replace('%nameofthewishlist%', target.listName || '');
    }

    modal.open();
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const response = target.productId
      ? await removeFromList(root.dataset.deleteProductUrl, {
        listId: target.listId,
        productId: parseInt(target.productId, 10),
        productAttributeId: parseInt(target.productAttributeId, 10),
      })
      : await deleteList(root.dataset.deleteListUrl, target.listId);

    EventBus.$emit('refetchList');
    notify(response);
    modal.close();
  });
}
