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
  <ul class="wishlist-list">
    <li
      class="wishlist-list-item"
      v-for="list of lists"
      @click="select(list.id)"
    >
      <p>
        {{ list.title }}
      </p>
    </li>
  </ul>
</template>

<script>
import getLists from "@graphqlFiles/queries/getlists";
import addtolist from "@graphqlFiles/mutations/addtolist";

/**
 * The role of this component is to render a list
 * and make the possibility to choose one for the selected product
 */
export default {
  name: "ChooseList",
  apollo: {
    lists: getLists
  },
  props: {
    productId: {
      type: Number,
      default: 0
    }
  },
  methods: {
    /**
     * Select a list and add the product to it
     *
     * @param {Int} listId The id of the list selected
     * @param {Int} userId The id of the user
     * @param {Int} productId The id of the product
     */
    async select(listId) {
      const list = await this.$apollo.mutate({
        mutation: addtolist,
        variables: {
          listId,
          userId: 1,
          productId: 1
        }
      });

      /**
       * Hide the modal inside the parent
       */
      this.$emit("hide");

      /**
       * Send an event to the Heart the user previously clicked on
       */
      const event = new CustomEvent("addedToWishlist", {
        detail: { productId: this.productId, listId }
      });

      document.dispatchEvent(event);
    }
  },
  mounted() {}
};
</script>

<style lang="scss" type="text/scss">
@import "@scss/_variables";

.wishlist {
  &-list {
    max-height: 200px;
    overflow-y: scroll;
    border-top: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    margin: 0;

    &-item {
      padding: 14px 0;
      transition: 0.25s ease-out;
      cursor: pointer;

      &:hover {
        background: lighten($blue, 45%);
      }

      p {
        font-size: 14px;
        letter-spacing: 0;
        color: #232323;
        margin-bottom: 0;
        line-height: 16px;
        padding: 0 40px;
      }
    }
  }
}
</style>
