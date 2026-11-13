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
const openedModals = new Set();

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    openedModals.forEach((modal) => modal.close());

    return;
  }

  // Themes may render the actions of a modal as [role="button"] elements, which browsers don't activate
  const button = event.target.closest('.wishlist-modal [role="button"]');

  if (button && (event.key === 'Enter' || event.key === ' ')) {
    event.preventDefault();
    button.click();
  }
});

/**
 * Handle the opening of a wishlist modal: the theme only has to keep the .wishlist-modal
 * and .modal-backdrop elements, and the data-dismiss="modal" attribute on the closing buttons
 *
 * @param {HTMLElement} root The element containing the modal
 *
 * @returns {Object}
 */
export default function createModal(root) {
  const element = root.querySelector('.wishlist-modal');
  const backdrop = root.querySelector('.modal-backdrop');

  const modal = {
    isOpen: () => element.classList.contains('show'),
    toggle(open = !modal.isOpen()) {
      element.classList.toggle('show', open);
      if (backdrop) {
        // "in" for Bootstrap 4 themes, "show" for Bootstrap 5 ones
        backdrop.classList.toggle('in', open);
        backdrop.classList.toggle('show', open);
      }

      if (open) {
        openedModals.add(modal);
      } else {
        openedModals.delete(modal);
      }
    },
    open: () => modal.toggle(true),
    close: () => modal.toggle(false),
  };

  root.addEventListener('click', (event) => {
    if (event.target.closest('[data-dismiss="modal"]')) {
      event.preventDefault();
      modal.close();
    }
  });

  return modal;
}
