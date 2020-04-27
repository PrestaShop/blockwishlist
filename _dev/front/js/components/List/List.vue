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
  <ul class="wishlist-list">
    <li class="wishlist-list-item" :key="list.id" v-for="list of items">
      <p class="wishlist-list-item-title">
        {{ list.title }}
        <span>({{ list.numbersProduct }})</span>
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

        <a @click="toggleDelete(list.id, list.title)">
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
    name: 'List',
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
        default: 'Rename'
      },
      shareText: {
        type: String,
        default: 'Share'
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
          this.activeDropdowns = [];
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
        const event = new CustomEvent('showRenameWishlist', {
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
        const event = new CustomEvent('showShareWishlist', {
          detail: { listId: id, userId: 1 }
        });

        document.dispatchEvent(event);
      },
      /**
       * Toggle the popup to rename a list
       *
       * @param {Int} id The list ID so the rename popup know which list to rename
       * @param {String} The base title so the rename popup can autofill it
       */
      toggleDelete(id, title) {
        const event = new CustomEvent('showDeleteWishlist', {
          detail: { listId: id, userId: 1 }
        });

        document.dispatchEvent(event);
      }
    }
  };
</script>

<style lang="scss" type="text/scss">
  .wishlist {
    &-list {
      margin-bottom: 0;

      &-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 20px;

        .dropdown-menu {
          right: 20px;
          left: inherit;
          display: flex;
          flex-direction: column;
        }

        &-right {
          position: relative;

          > a {
            transition: 0.25s ease-out;

            &:hover {
              opacity: 0.6;
            }

            i {
              color: #7a7a7a;
            }
          }

          .dropdown-menu {
            box-sizing: border-box;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            background-color: #ffffff;
            box-shadow: 2px 2px 10px 0 rgba(0, 0, 0, 0.2);
            padding: 0;
            overflow: hidden;

            > a {
              padding: 10px 20px;
              transition: 0.2s ease-out;

              &:hover {
                background-color: #f1f1f1;
              }
            }
          }
        }

        &-title {
          color: #232323;
          font-size: 16px;
          font-weight: bold;
          letter-spacing: 0;
          line-height: 22px;
          margin-bottom: 0;

          span {
            color: #7a7a7a;
            font-size: 16px;
            letter-spacing: 0;
            line-height: 22px;
            font-weight: normal;
            margin-left: 5px;
          }
        }

        a {
          cursor: pointer;
        }
      }
    }
  }
</style>
