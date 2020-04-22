<!--**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <button class="wishlist-button-add" @click="addToWishlist">
    <i class="material-icons" v-if="isChecked">favorite</i>
    <i class="material-icons" v-else>favorite_border</i>
  </button>
</template>

<script>
import removeFromList from '@graphqlFiles/mutations/removeFromList';

export default {
  name: 'Button',
  props: {
    url: '',
    productId: null,
    listId: null,
    checked: false,
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
    async addToWishlist() {
      if (!this.isChecked) {
        const event = new CustomEvent('showAddToWishList', {
          detail: {productId: this.productId},
        });

        document.dispatchEvent(event);
      } else {
        let response = await this.$apollo.mutate({
          mutation: removeFromList,
          variables: {
            productId: this.productId,
            listId: this.listId ? this.listId : this.idList,
            userId: 1,
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
    document.addEventListener('addedToWishlist', event => {
      if (event.detail.productId === this.productId) {
        this.isChecked = true;
        this.idList = event.detail.listId;
      }
    });
  },
};
</script>

<style lang="scss" type="text/scss">
.wishlist {
  &-button {
    &-add {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 40px;
      width: 40px;
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
