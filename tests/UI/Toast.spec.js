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
