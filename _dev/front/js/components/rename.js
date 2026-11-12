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
import {renameList} from '../api';
import EventBus, {notify} from '../lib/event-bus';
import createModal from '../lib/modal';

/**
 * @param {HTMLElement} root
 */
export default function initRename(root) {
  const modal = createModal(root);
  const form = root.querySelector('form');
  const input = form.querySelector('input[name="name"]');
  let listId = 0;

  EventBus.$on('showRenameWishlist', (event) => {
    ({listId} = event.detail);
    input.value = event.detail.title;
    modal.open();
    input.focus();
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const response = await renameList(root.dataset.url, listId, input.value);
    EventBus.$emit('refetchList');
    notify(response);
    modal.close();
  });
}
