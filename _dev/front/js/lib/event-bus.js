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

const listeners = {};

/**
 * Lets the wishlist components, and the theme or other modules, communicate.
 * Exposed as window.WishlistEventBus, with the same methods as the previous Vue instance.
 */
const EventBus = {
  $on(event, callback) {
    listeners[event] = [...(listeners[event] || []), callback];
  },
  $off(event, callback) {
    listeners[event] = (listeners[event] || []).filter((listener) => listener !== callback);
  },
  $emit(event, ...args) {
    (listeners[event] || []).forEach((listener) => listener(...args));
  },
};

window.WishlistEventBus = EventBus;
prestashop.emit('wishlistEventBusInit');

export default EventBus;

/**
 * Display the response of an action in the toast
 *
 * @param {Object} response
 */
export const notify = (response) => EventBus.$emit('showToast', {
  detail: {
    type: response.success ? 'success' : 'error',
    message: response.message,
  },
});
