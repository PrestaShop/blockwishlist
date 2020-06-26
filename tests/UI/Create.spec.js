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
