<!--**
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
 *-->
<template>
  <button
    class="wishlist-button-add"
    :class="{ 'wishlist-button-product': isProduct }"
    @click="addToWishlist"
  >
    <i
      class="material-icons"
      v-if="isChecked"
    >favorite</i>
    <i
      class="material-icons"
      v-else
    >favorite_border</i>
  </button>
</template>

<script>
  import removeFromList from '@graphqlFiles/mutations/removeFromList';
  import prestashop from 'prestashop';
  import EventBus from '@components/EventBus';

  export default {
    name: 'Button',
    props: {
      url: {
        type: String,
        required: true,
        default: '#',
      },
      productId: {
        type: Number,
        required: true,
        default: null,
      },
      productAttributeId: {
        type: Number,
        required: true,
        default: null,
      },
      checked: {
        type: Boolean,
        required: false,
        default: false,
      },
      isProduct: {
        type: Boolean,
        required: false,
        default: false,
      },
    },
    data() {
      return {
        isChecked: this.checked === 'true',
        idList: this.listId,
        idProductAttribute: this.productAttributeId,
      };
    },
    methods: {
      /**
       * Toggle the heart on this component, basically if the heart is filled,
       * then this product is inside a wishlist, else it's not in a wishlist
       */
      toggleCheck() {
        this.isChecked = !this.isChecked;
      },
      /**
       * If the product isn't in a wishlist, then open the "AddToWishlist" component modal,
       * if he's in a wishlist, then launch a removeFromList mutation to remote the product from a wishlist
       */
      async addToWishlist(event) {
        event.preventDefault();
        const quantity = document.querySelector(
          '.product-quantity input#quantity_wanted',
        );

        if (!prestashop.customer.is_logged) {
          EventBus.$emit('showLogin');

          return;
        }

        if (!this.isChecked) {
          EventBus.$emit('showAddToWishList', {
            detail: {
              productId: this.productId,
              productAttributeId: parseInt(this.productAttributeId, 10),
              forceOpen: true,
              quantity: quantity ? parseInt(quantity.value, 10) : 0,
            },
          });
        } else {
          const {data} = await this.$apollo.mutate({
            mutation: removeFromList,
            variables: {
              productId: this.productId,
              url: this.url,
              productAttributeId: this.productAttributeId,
              listId: this.idList ? this.idList : this.listId,
            },
          });

          const {removeFromList: response} = data;

          EventBus.$emit('showToast', {
            detail: {
              type: response.success ? 'success' : 'error',
              message: response.message,
            },
          });

          if (!response.error) {
            this.toggleCheck();
          }
        }
      },
    },
    mounted() {
      /**
       * Register to event addedToWishlist to toggle the heart if the product has been added correctly
       */
      EventBus.$on('addedToWishlist', (event) => {
        if (
          event.detail.productId === this.productId
          && parseInt(event.detail.productAttributeId, 10) === this.productAttributeId
        ) {
          this.isChecked = true;
          this.idList = event.detail.listId;
        }
      });

      // eslint-disable-next-line
      const items = productsAlreadyTagged.filter(
        (e) => parseInt(e.id_product, 10) === this.productId
          && parseInt(e.id_product_attribute, 10) === this.productAttributeId,
      );

      if (items.length > 0) {
        this.isChecked = true;
        this.idList = parseInt(items[0].id_wishlist, 10);
      }

      if (this.isProduct) {
        prestashop.on('updateProduct', ({eventType}) => {
          if (eventType === 'updatedProductQuantity') {
            this.isChecked = false;
          }
        });

        prestashop.on('updatedProduct', (args) => {
          const quantity = document.querySelector(
            '.product-quantity input#quantity_wanted',
          );

          this.idProductAttribute = parseInt(args.id_product_attribute, 10);

          // eslint-disable-next-line
          const itemsFiltered = productsAlreadyTagged.filter(
            (e) => parseInt(e.id_product, 10) === this.productId
              && e.quantity.toString() === quantity.value
              && parseInt(e.id_product_attribute, 10) === this.productAttributeId,
          );

          if (itemsFiltered.length > 0) {
            this.isChecked = true;
            this.idList = parseInt(itemsFiltered[0].id_wishlist, 10);
          } else {
            this.isChecked = false;
          }
        });
      }
    },
  };
</script>

<style lang="scss" type="text/scss">
  .wishlist {
    &-button {
      &-product {
        margin-left: 1.25rem;
      }

      &-add {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 2.5rem;
        width: 2.5rem;
        min-width: 2.5rem;
        padding-top: 0.1875rem;
        background-color: #ffffff;
        box-shadow: 0.125rem -0.125rem 0.25rem 0 rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        cursor: pointer;
        transition: 0.2s ease-out;
        border: none;

        &:hover {
          opacity: 0.7;
        }

        &:focus {
          outline: 0;
        }

        &:active {
          transform: scale(1.2);
        }

        i {
          color: #7a7a7a;
        }
      }
    }
  }
</style>
