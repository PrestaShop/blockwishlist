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
