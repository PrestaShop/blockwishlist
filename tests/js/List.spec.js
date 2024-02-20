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
import Vue from 'vue';
import { shallowMount, mount } from '@vue/test-utils';
import List from '@components/List/List.vue';
import EventBus from '@components/EventBus';

const items = [
  {
    id_wishlist: 1,
    nbProducts: 2,
    name: 'Test of list number one',
    listUrl: '#'
  },
  {
    id_wishlist: 2,
    nbProducts: 2,
    name: 'Test of list 2',
    listUrl: '#'
  }
];

describe('List.vue', () => {
  it('should be loading if no items are provided', async () => {
    const wrapper = shallowMount(List);

    expect(wrapper.vm.loading).toBe(true);
  });

  it('should render a list of items if they are provided', async () => {
    const wrapper = shallowMount(List, {
      propsData: {
        loading: false,
        items
      }
    });

    expect(wrapper.vm.loading).toBe(false);

    expect(wrapper.find('ul li').exists()).toBe(true);
    expect(wrapper.findAll('li')).toHaveLength(2);
  });

  it('should open links popup', async () => {
    const wrapper = mount(List, {
      propsData: {
        loading: false,
        items
      }
    });

    expect(wrapper.vm.loading).toBe(false);

    const lists = wrapper.findAll('li');

    lists.wrappers.forEach(list => {
      list.find('.wishlist-list-item-actions').trigger('click');
    });

    expect(wrapper.vm.activeDropdowns.length).toBe(1);
  });

  it('should click on share and open share modal', async () => {
    EventBus.$on('showShareWishlist', e => {
      expect(e.detail.listId).toBe(1);
    });

    const wrapper = mount(List, {
      propsData: {
        loading: false,
        items,
        listId: 1
      }
    });

    expect(wrapper.vm.loading).toBe(false);

    const list = wrapper.find('li:first-child');

    list.find('.wishlist-list-item-actions').trigger('click');
    await Vue.nextTick();

    list.find('.dropdown-menu button:last-child').trigger('click');
    await Vue.nextTick();

    expect(wrapper.vm.activeDropdowns.length).toBe(1);
  });

  it('should click on rename and open rename modal', async () => {
    EventBus.$on('showRenameWishlist', e => {
      expect(e.detail.listId).toBe(1);
    });

    const wrapper = mount(List, {
      propsData: {
        loading: false,
        items,
        listId: 1
      }
    });

    expect(wrapper.vm.loading).toBe(false);

    const list = wrapper.find('li:first-child');

    list.find('.wishlist-list-item-actions').trigger('click');
    await Vue.nextTick();

    list.find('.dropdown-menu button:first-child').trigger('click');
    await Vue.nextTick();

    expect(wrapper.vm.activeDropdowns.length).toBe(1);
  });
});
