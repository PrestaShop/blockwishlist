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
<script>
  import deleteList from '@graphqlFiles/mutations/deleteList';
  import getLists from '@graphqlFiles/queries/getlists';
  import removeFromList from '@graphqlFiles/mutations/removeFromList';
  import EventBus from '@components/EventBus';

  /**
   * This component display a modal where you can delete a wishlist
   */
  export default {
    name: 'Delete',
    props: {
      url: {
        type: String,
        required: true,
        default: '#'
      },
      title: {
        type: String,
        required: true,
        default: 'Delete'
      },
      placeholder: {
        type: String,
        required: true,
        default: 'This action is irreversible'
      },
      cancelText: {
        type: String,
        required: true,
        default: 'Cancel'
      },
      deleteText: {
        type: String,
        required: true,
        default: 'Delete'
      }
    },
    data() {
      return {
        value: '',
        isHidden: true,
        listId: null,
        productId: null
      };
    },
    methods: {
      /**
       * Toggle the modal
       */
      toggleModal() {
        this.isHidden = !this.isHidden;
      },
      /**
       * Launch a deleteList mutation to delete a Wishlist
       */
      async deleteWishlist() {
        const list = await this.$apollo.mutate({
          mutation: this.productId ? removeFromList : deleteList,
          variables: {
            listId: this.listId,
            productId: this.productId,
            userId: 1
          }
        });

        /**
         * As this is not a real SPA, we need to inform others VueJS apps that they need to refetch the list
         */
        EventBus.$emit('refetchList');

        EventBus.$emit('showToast', {
          detail: {
            type: 'success',
            message: this.productId ? 'deleteProductText' : 'deleteWishlistText'
          }
        });

        /**
         * Finally hide the modal after deleting the list
         * and reopen the wishlist modal
         */
        this.toggleModal();
      }
    },
    mounted() {
      /**
       * Register to the event showCreateWishlist so others components can toggle this modal
       *
       * @param {String} 'showDeleteWishlist'
       */
      EventBus.$on('showDeleteWishlist', event => {
        this.value = '';
        this.listId = event.detail.listId;

        if (event.detail.productId) {
          this.productId = event.detail.productId;
        } else {
          this.productId = null;
        }

        this.toggleModal();
      });
    }
  };
</script>

<style lang="scss" type="text/scss" scoped>
  .wishlist {
    &-delete {
      .wishlist-modal {
        display: block;
        opacity: 0;
        pointer-events: none;
        z-index: 0;

        &.show {
          opacity: 1;
          pointer-events: all;
          z-index: 1053;
        }
      }
    }
  }
</style>
