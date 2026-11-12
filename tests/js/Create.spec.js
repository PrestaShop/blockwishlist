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
import initCreate from '@js/components/create';
import EventBus from '@js/lib/event-bus';
import loadTemplate from './utils/loadTemplate';
import mockFetch, {flushPromises} from './utils/fetch';

describe('Create', () => {
  let root;
  let input;
  const submit = () => root.querySelector('form').dispatchEvent(new window.Event('submit', {cancelable: true}));

  before(() => {
    root = loadTemplate('front/modals/create', {createUrl: 'http://shop/action?action=createNewWishlist'});
    input = root.querySelector('input[name="name"]');
    initCreate(root);
  });

  it('should open with an empty name when it receives the event showCreateWishlist', () => {
    input.value = 'Previous name';
    EventBus.$emit('showCreateWishlist');

    expect(root.querySelector('.wishlist-modal').classList.contains('show')).toBe(true);
    expect(input.value).toBe('');
  });

  it('should refuse an empty name', () => {
    const requests = mockFetch({});
    let toast = null;
    EventBus.$on('showToast', (event) => {
      toast = event.detail;
    });

    input.value = '   ';
    submit();

    expect(requests.length).toBe(0);
    expect(toast).toEqual({type: 'error', message: 'List title is too short'});
  });

  it('should create the list, then reopen the list choice it was opened from', async () => {
    const requests = mockFetch({success: true, message: 'The list has been properly created'});
    let reopened = false;
    EventBus.$on('showAddToWishList', () => {
      reopened = true;
    });

    EventBus.$emit('showCreateWishlist', {detail: {fromAddToWishlist: true}});
    input.value = 'Gifts & ideas';
    submit();
    await flushPromises();

    expect(requests[0].url).toBe('http://shop/action?action=createNewWishlist&params[name]=Gifts%20%26%20ideas');
    expect(root.querySelector('.wishlist-modal').classList.contains('show')).toBe(false);
    expect(reopened).toBe(true);
  });
});
