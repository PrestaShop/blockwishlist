import Vue from 'vue';
import {shallowMount, mount, createLocalVue} from '@vue/test-utils';
import Delete from '@components/Delete/Delete.vue';
import EventBus from '@components/EventBus';
import {addMockFunctionsToSchema} from 'graphql-tools';
import schema from './mockSchema';
import {graphql} from 'graphql';

const removeFromList = `
  mutation {
    removeFromList(listId: 1, productId: 1, productAttributeId: 1, url: "#") {
      success
      message
    }
  }
`;

describe('Delete.vue', () => {
  let localVue;
  beforeEach(() => {
    localVue = createLocalVue();

    addMockFunctionsToSchema({
      schema,
      preserveResolvers: true
    });
  });

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

  it(`should be hidden after accepting`, async () => {
    const wrapper = shallowMount(Delete);

    const result = await graphql(schema, removeFromList);

    expect(result.data.removeFromList.success).toBe(true);
  });
});
