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
import {shallowMount, mount} from '@vue/test-utils';
import Create from '@components/Create/Create.vue';
import EventBus from '@components/EventBus';

describe('Create.vue', () => {
  it(`should'nt be hidden if it receive the event showCreateWishlist`, async () => {
    const wrapper = shallowMount(Create);

    EventBus.$emit('showCreateWishlist');

    expect(wrapper.vm.isHidden).toBe(false);
  });

  it('value should be empty if it receive the event addedToWishlist', async () => {
    const wrapper = shallowMount(Create);

    EventBus.$emit('showCreateWishlist');

    expect(wrapper.vm.value).toBe('');
  });
});
