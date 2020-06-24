<!--**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
        if (event.detail.productId === this.productId) {
          this.isChecked = true;
          this.idList = event.detail.listId;
        }
      });

      // eslint-disable-next-line
      const items = productsAlreadyTagged.filter(
        (e) => e.id_product === this.productId.toString()
          && e.id_product_attribute === this.productAttributeId.toString(),
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

          this.productAttributeId = args.id_product_attribute;

          // eslint-disable-next-line
          const itemsFiltered = productsAlreadyTagged.filter(
            (e) => e.id_product === this.productId.toString()
              && e.quantity === quantity.value
              && e.id_product_attribute === this.productAttributeId.toString(),
          );

          if (itemsFiltered.length > 0) {
            this.isChecked = true;
            this.idList = parseInt(items[0].id_wishlist, 10);
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
        margin-left: 20px;
      }

      &-add {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        width: 40px;
        min-width: 40px;
        padding-top: 3px;
        background-color: #ffffff;
        box-shadow: 2px 2px 4px 0 rgba(0, 0, 0, 0.2);
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
