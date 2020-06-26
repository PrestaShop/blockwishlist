import Vue from 'vue';
import {shallowMount, mount} from '@vue/test-utils';
import Login from '@components/Login/Login.vue';
import EventBus from '@components/EventBus';

describe('Login.vue', () => {
  it(`should'nt be hidden if it receive the event showLogin`, async () => {
    const wrapper = shallowMount(Login);

    EventBus.$emit('showLogin');

    expect(wrapper.vm.isHidden).toBe(false);
  });
});
