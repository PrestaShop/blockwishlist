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
import Toast from '@components/Toast/Toast.vue';
import EventBus from '@components/EventBus';

describe('Toast.vue', () => {
  it(`should'nt be hidden if it receive the event showToast`, async () => {
    const wrapper = shallowMount(Toast);

    EventBus.$emit('showToast', {
      detail: {
        message: 'Test of message'
      }
    });

    expect(wrapper.vm.active).toBe(true);
  });

  it(`should have a custom message`, async () => {
    const wrapper = shallowMount(Toast);

    EventBus.$emit('showToast', {
      detail: {
        message: 'custom'
      }
    });

    expect(wrapper.vm.text).toBe('custom');
  });

  it(`should be hidden after 2.5s`, function(done) {
    const wrapper = shallowMount(Toast);
    this.timeout(4000);

    EventBus.$emit('showToast', {
      detail: {
        message: 'Test of message'
      }
    });

    expect(wrapper.vm.active).toBe(true);

    setTimeout(() => {
      expect(wrapper.vm.active).toBe(false);
      done();
    }, 3000);
  });
});
