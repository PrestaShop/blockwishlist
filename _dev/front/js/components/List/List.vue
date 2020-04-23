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
    <li class="wishlist-list-item" :key="list.id" v-for="list of items">
      <p>
        {{ list.title }} <span>({{ list.numbersProduct }})</span>
      </p>

      <div class="wishlist-list-item-right">
        <a @click="togglePopup(list.id)" class="wishlist-list-item-actions">
          <i class="material-icons">more_vert</i>
        </a>

        <div
          class="dropdown-menu show"
          v-if="activeDropdowns.includes(list.id)"
        >
          <a @click="toggleRename(list.id, list.title)">{{ renameText }}</a>
          <a @click="toggleShare(list.id)">{{ shareText }}</a>
        </div>

        <a @click="$emit('delete', list.id)">
          <i class="material-icons">delete</i>
        </a>
      </div>
    </li>
  </ul>
</template>

<script>
/**
 * Dumb component to display the list of Wishlist on a page
 */
export default {
  name: "List",
  data() {
    return {
      activeDropdowns: []
    };
  },
  props: {
    items: {
      type: Array,
      default: []
    },
    renameText: {
      type: String,
      default: "Rename"
    },
    shareText: {
      type: String,
      default: "Share"
    }
  },
  methods: {
    /**
     * Toggle a dropdown with some actions
     *
     * @param {Int} id The ID of the list which contain this dropdown
     */
    togglePopup(id) {
      if (this.activeDropdowns.includes(id)) {
        this.activeDropdowns = this.activeDropdowns.filter(e => e !== id);
      } else {
        this.activeDropdowns.push(id);
      }
    },
    /**
     * Toggle the popup to rename a list
     *
     * @param {Int} id The list ID so the rename popup know which list to rename
     * @param {String} The base title so the rename popup can autofill it
     */
    toggleRename(id, title) {
      const event = new CustomEvent("showRenameWishlist", {
        detail: { listId: id, title }
      });

      document.dispatchEvent(event);
    },
    /**
     * Toggle the popup to rename a list
     *
     * @param {Int} id The list ID so the rename popup know which list to rename
     * @param {String} The base title so the rename popup can autofill it
     */
    toggleShare(id, title) {
      const event = new CustomEvent("showShareModal", {
        detail: { listId: id, title }
      });

      document.dispatchEvent(event);
    }
  }
};
</script>

<style lang="scss" type="text/scss">
.wishlist {
  &-list {
    &-item {
      display: flex;
      justify-content: space-between;
      align-items: center;

      .dropdown-menu {
        right: 20px;
        left: inherit;
        display: flex;
        flex-direction: column;
      }

      &-right {
        position: relative;
      }

      &-actions {
      }

      a {
        cursor: pointer;
      }
    }
  }
}
</style>
