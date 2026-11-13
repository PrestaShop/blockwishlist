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
import initToast from '@js/components/toast';
import EventBus from '@js/lib/event-bus';
import loadTemplate from './utils/loadTemplate';

describe('Toast', () => {
  let toast;

  before(() => {
    toast = loadTemplate('front/toast');
    initToast(toast);
  });

  it('should display the message it receives', () => {
    EventBus.$emit('showToast', {detail: {type: 'error', message: 'Something went wrong'}});

    expect(toast.classList.contains('isActive')).toBe(true);
    expect(toast.classList.contains('error')).toBe(true);
    expect(toast.querySelector('.wishlist-toast-text').textContent).toBe('Something went wrong');
  });

  it('should translate the message named after one of its data attributes', () => {
    EventBus.$emit('showToast', {detail: {type: 'success', message: 'copyText'}});

    expect(toast.classList.contains('error')).toBe(false);
    expect(toast.classList.contains('success')).toBe(true);
    expect(toast.querySelector('.wishlist-toast-text').textContent).toBe('Share link copied!');
  });
});
