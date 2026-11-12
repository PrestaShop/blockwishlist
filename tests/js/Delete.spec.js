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
import initDelete from '@js/components/delete';
import EventBus from '@js/lib/event-bus';
import loadTemplate from './utils/loadTemplate';
import mockFetch, {flushPromises} from './utils/fetch';

describe('Delete', () => {
  let root;

  before(() => {
    root = loadTemplate('front/modals/delete', {
      deleteListUrl: 'http://shop/action?action=deleteWishlist',
      deleteProductUrl: 'http://shop/action?action=deleteProductFromWishlist',
    });
    initDelete(root);
  });

  it('should confirm the removal of a product, with the name of its list', () => {
    EventBus.$emit('showDeleteWishlist', {
      detail: {
        listId: 3, listName: 'Gifts', productId: 1, productAttributeId: 2,
      },
    });

    expect(root.querySelector('.modal-title .js-wishlist-delete-product').hidden).toBe(false);
    expect(root.querySelector('.modal-title .js-wishlist-delete-list').hidden).toBe(true);
    expect(root.querySelector('.modal-text').textContent).toBe('The product will be removed from "Gifts".');
  });

  it('should confirm the deletion of a list', async () => {
    const requests = mockFetch({success: true});
    EventBus.$emit('showDeleteWishlist', {detail: {listId: 3, listName: 'Gifts'}});

    expect(root.querySelector('.modal-body').hidden).toBe(true);
    expect(root.querySelector('.modal-title .js-wishlist-delete-list').hidden).toBe(false);

    root.querySelector('form').dispatchEvent(new window.Event('submit', {cancelable: true}));
    await flushPromises();

    expect(requests[0].url).toBe('http://shop/action?action=deleteWishlist&params[idWishList]=3');
    expect(root.querySelector('.wishlist-modal').classList.contains('show')).toBe(false);
  });
});
