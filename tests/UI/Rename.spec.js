import Vue from 'vue';
import {shallowMount, mount} from '@vue/test-utils';
import Rename from '@components/Rename/Rename.vue';
import EventBus from '@components/EventBus';

describe('Rename.vue', () => {
  it(`should'nt be hidden if it receive the event showRenameWishlist`, async () => {
    const wrapper = shallowMount(Rename);

    EventBus.$emit('showRenameWishlist', {
      detail: {
        listId: 1,
        title: 'Test of title'
      }
    });

    expect(wrapper.vm.isHidden).toBe(false);
  });
});
