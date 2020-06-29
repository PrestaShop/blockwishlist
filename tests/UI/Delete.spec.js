import {shallowMount} from '@vue/test-utils';
import Delete from '@components/Delete/Delete.vue';
import EventBus from '@components/EventBus';

describe('Delete.vue', () => {
  it(`should'nt be hidden if it receive the event showDelete`, async () => {
    const wrapper = shallowMount(Delete);

    EventBus.$emit('showDeleteWishlist', {
      detail: {
        listId: 1,
        productId: 1,
        productAttributeId: 1
      }
    });

    expect(wrapper.vm.isHidden).toBe(false);
  });
});
