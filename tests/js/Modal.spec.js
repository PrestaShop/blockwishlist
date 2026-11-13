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
import createModal from '@js/lib/modal';

describe('Modal', () => {
  let root;
  let modal;
  let clicks;

  const press = (element, key) => {
    const event = new window.KeyboardEvent('keydown', {key, bubbles: true, cancelable: true});
    element.dispatchEvent(event);

    return event;
  };

  before(() => {
    document.body.insertAdjacentHTML('beforeend', `
      <div class="js-modal-test">
        <div class="wishlist-modal modal fade">
          <div role="button" class="wishlist-add-to-new">New list</div>
          <button type="button" data-dismiss="modal">Close</button>
        </div>
        <div class="modal-backdrop fade"></div>
      </div>
    `);
    root = document.querySelector('.js-modal-test');
    modal = createModal(root);
    root.querySelector('[role="button"]').addEventListener('click', () => {
      clicks += 1;
    });
  });

  beforeEach(() => {
    clicks = 0;
  });

  it('should activate a [role="button"] element with Enter and Space', () => {
    const button = root.querySelector('[role="button"]');

    expect(press(button, 'Enter').defaultPrevented).toBe(true);
    expect(press(button, ' ').defaultPrevented).toBe(true);
    expect(clicks).toBe(2);
  });

  it('should ignore the other keys', () => {
    press(root.querySelector('[role="button"]'), 'a');

    expect(clicks).toBe(0);
  });

  it('should close the opened modals with Escape', () => {
    modal.open();
    press(document.body, 'Escape');

    expect(modal.isOpen()).toBe(false);
  });
});
