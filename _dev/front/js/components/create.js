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
import {createList} from '../api';
import EventBus, {notify} from '../lib/event-bus';
import createModal from '../lib/modal';

/**
 * @param {HTMLElement} root
 */
export default function initCreate(root) {
  const modal = createModal(root);
  const form = root.querySelector('form');
  const input = form.querySelector('input[name="name"]');
  let fromAddToWishlist = false;

  EventBus.$on('showCreateWishlist', (event) => {
    fromAddToWishlist = Boolean(event && event.detail && event.detail.fromAddToWishlist);
    input.value = '';
    modal.open();
    input.focus();
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (input.value.trim() === '') {
      EventBus.$emit('showToast', {
        detail: {
          type: 'error',
          message: root.dataset.lengthText,
        },
      });

      return;
    }

    const response = await createList(root.dataset.url, input.value);
    notify(response);
    EventBus.$emit('refetchList');
    modal.close();

    if (fromAddToWishlist) {
      EventBus.$emit('showAddToWishList', {
        detail: {
          forceOpen: true,
        },
      });
    }
  });
}
