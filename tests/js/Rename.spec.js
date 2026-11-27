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
import initRename from '@js/components/rename';
import EventBus from '@js/lib/event-bus';
import loadTemplate from './utils/loadTemplate';
import mockFetch, {flushPromises} from './utils/fetch';

describe('Rename', () => {
  let root;

  before(() => {
    root = loadTemplate('front/modals/rename', {renameUrl: 'http://shop/action?action=renameWishlist'});
    initRename(root);
  });

  it('should rename the list it receives', async () => {
    const requests = mockFetch({success: true});
    EventBus.$emit('showRenameWishlist', {detail: {listId: 4, title: 'Gifts'}});

    const input = root.querySelector('input[name="name"]');
    expect(input.value).toBe('Gifts');
    expect(root.querySelector('.wishlist-modal').classList.contains('show')).toBe(true);

    input.value = 'Ideas';
    root.querySelector('form').dispatchEvent(new window.Event('submit', {cancelable: true}));
    await flushPromises();

    expect(requests[0].url).toBe('http://shop/action?action=renameWishlist&params[name]=Ideas&params[idWishList]=4');
    expect(root.querySelector('.wishlist-modal').classList.contains('show')).toBe(false);
  });
});
