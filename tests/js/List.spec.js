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
import initListsPage from '@js/pages/lists';
import EventBus from '@js/lib/event-bus';

describe('Lists page', () => {
  let events;
  const action = (name) => document.querySelector(`[data-ps-action="${name}"]`);

  before(() => {
    document.body.insertAdjacentHTML('beforeend', `
      <div data-ps-component="wishlist-lists-page">
        <a href="#" data-ps-action="wishlist-create-list">New</a>
        <div data-ps-target="wishlist-lists">
          <ul>
            <li data-ps-ref="wishlist-list-item" data-id="2" data-name="Gifts" data-share-url="http://shop/share">
              <a href="#">
                <button type="button" data-ps-action="wishlist-toggle-menu"></button>
                <div data-ps-ref="wishlist-list-menu">
                  <button type="button" data-ps-action="wishlist-rename"></button>
                  <button type="button" data-ps-action="wishlist-share"></button>
                </div>
                <button type="button" data-ps-action="wishlist-delete"></button>
              </a>
            </li>
          </ul>
        </div>
      </div>
    `);
    initListsPage(document.querySelector('[data-ps-component="wishlist-lists-page"]'));

    events = [];
    ['showCreateWishlist', 'showRenameWishlist', 'showShareWishlist', 'showDeleteWishlist'].forEach(
      (name) => EventBus.$on(name, (event) => events.push({name, event})),
    );
  });

  it('should toggle the actions of a list, and close them when clicking elsewhere', () => {
    const menu = document.querySelector('[data-ps-ref="wishlist-list-menu"]');

    action('wishlist-toggle-menu').click();
    expect(menu.classList.contains('show')).toBe(true);

    document.body.click();
    expect(menu.classList.contains('show')).toBe(false);
  });

  it('should send the list to the modals', () => {
    action('wishlist-rename').click();
    action('wishlist-share').click();
    action('wishlist-delete').click();

    expect(events.map(({name, event}) => [name, event.detail])).toEqual([
      ['showRenameWishlist', {listId: 2, title: 'Gifts'}],
      ['showShareWishlist', {listId: 2, shareUrl: 'http://shop/share'}],
      ['showDeleteWishlist', {listId: 2, listName: 'Gifts'}],
    ]);
  });

  it('should open the creation of a list', () => {
    action('wishlist-create-list').click();

    expect(events.pop().name).toBe('showCreateWishlist');
  });
});
