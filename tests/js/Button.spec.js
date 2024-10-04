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
import {shallowMount} from '@vue/test-utils';
import Button from '@components/Button/Button.vue';
import EventBus from '@components/EventBus';

describe('Button.vue', () => {
  it('should stay unchecked if user is not logged in', async () => {
    const wrapper = shallowMount(Button);

    expect(wrapper.vm.isChecked).toBe(false);

    wrapper.find('button').trigger('click');
    await Vue.nextTick();
    expect(wrapper.vm.isChecked).toBe(false);
  });

  it('should be checked if it receive the event addedToWishlist', async () => {
    const wrapper = shallowMount(Button, {
      propsData: {
        productId: 1
      }
    });

    EventBus.$emit('addedToWishlist', {
      detail: {productId: 1, listId: 1}
    });

    expect(wrapper.vm.isChecked).toBe(true);
  });

  it('should be checked if we passe checked as a prop', async () => {
    const wrapper = shallowMount(Button, {
      propsData: {
        productId: 1,
        checked: 'true'
      }
    });

    expect(wrapper.vm.isChecked).toBe(true);
  });
});
