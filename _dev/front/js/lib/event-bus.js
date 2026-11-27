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

let listeners = {};

/**
 * Lets the wishlist components, and the theme or other modules, communicate.
 * Exposed as window.WishlistEventBus, it behaves like the Vue 2 instance it replaces:
 * $on, $once, $off and $emit, with arrays of events and chaining.
 */
const EventBus = {
  $on(event, callback) {
    if (Array.isArray(event)) {
      event.forEach((name) => EventBus.$on(name, callback));
    } else {
      listeners[event] = [...(listeners[event] || []), callback];
    }

    return EventBus;
  },
  $once(event, callback) {
    const listener = (...args) => {
      EventBus.$off(event, listener);
      callback(...args);
    };
    // Lets $off remove it with the original callback
    listener.fn = callback;

    return EventBus.$on(event, listener);
  },
  $off(event, callback) {
    if (event === undefined) {
      listeners = {};
    } else if (Array.isArray(event)) {
      event.forEach((name) => EventBus.$off(name, callback));
    } else if (callback === undefined) {
      delete listeners[event];
    } else {
      listeners[event] = (listeners[event] || []).filter(
        (listener) => listener !== callback && listener.fn !== callback,
      );
    }

    return EventBus;
  },
  $emit(event, ...args) {
    // Copied, so a listener removed while emitting doesn't skip the next one
    [...(listeners[event] || [])].forEach((listener) => {
      try {
        listener(...args);
      } catch (error) {
        console.error(`Error in a "${event}" listener of WishlistEventBus`, error);
      }
    });

    return EventBus;
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
