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

  before(() => {
    document.body.insertAdjacentHTML('beforeend', `
      <div class="wishlist-container">
        <a href="#" class="wishlist-add-to-new">New</a>
        <div class="wishlist-list-container">
          <ul class="wishlist-list">
            <li class="wishlist-list-item" data-id="2" data-name="Gifts" data-share-url="http://shop/share">
              <a class="wishlist-list-item-link" href="#">
                <div class="wishlist-list-item-right">
                  <button type="button" class="wishlist-list-item-actions"></button>
                  <div class="dropdown-menu">
                    <button type="button" class="js-wishlist-rename"></button>
                    <button type="button" class="js-wishlist-share"></button>
                  </div>
                  <button type="button" class="js-wishlist-delete"></button>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    `);
    initListsPage(document.querySelector('.wishlist-container'));

    events = [];
    ['showCreateWishlist', 'showRenameWishlist', 'showShareWishlist', 'showDeleteWishlist'].forEach(
      (name) => EventBus.$on(name, (event) => events.push({name, event})),
    );
  });

  it('should toggle the actions of a list, and close them when clicking elsewhere', () => {
    const menu = document.querySelector('.wishlist-list-item .dropdown-menu');

    document.querySelector('.wishlist-list-item-actions').click();
    expect(menu.classList.contains('show')).toBe(true);

    document.body.click();
    expect(menu.classList.contains('show')).toBe(false);
  });

  it('should send the list to the modals', () => {
    document.querySelector('.js-wishlist-rename').click();
    document.querySelector('.js-wishlist-share').click();
    document.querySelector('.js-wishlist-delete').click();

    expect(events.map(({name, event}) => [name, event.detail])).toEqual([
      ['showRenameWishlist', {listId: 2, title: 'Gifts'}],
      ['showShareWishlist', {listId: 2, shareUrl: 'http://shop/share'}],
      ['showDeleteWishlist', {listId: 2, listName: 'Gifts'}],
    ]);
  });

  it('should open the creation of a list', () => {
    document.querySelector('.wishlist-add-to-new').click();

    expect(events.pop().name).toBe('showCreateWishlist');
  });
});
