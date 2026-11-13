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
import EventBus from '../lib/event-bus';

/**
 * Display the messages sent with the showToast event.
 * A message can be the name of a data attribute of the toast, holding the translated text.
 *
 * @param {HTMLElement} toast
 */
export default function initToast(toast) {
  const text = toast.querySelector('.wishlist-toast-text');
  let timeout = null;
  let type = 'basic';

  EventBus.$on('showToast', (event) => {
    const {message} = event.detail;

    if (message) {
      text.textContent = toast.dataset[message] || message;
    }

    toast.classList.remove(type);
    type = event.detail.type || 'basic';
    toast.classList.add('isActive', type);

    clearTimeout(timeout);
    timeout = setTimeout(() => {
      toast.classList.remove('isActive');
    }, 2500);
  });
}
