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
<script>
  import deleteList from '@graphqlFiles/mutations/deletelist';
  import removeFromList from '@graphqlFiles/mutations/removeFromList';
  import EventBus from '@components/EventBus';

  /**
   * This component display a modal where you can delete a wishlist
   */
  export default {
    name: 'Delete',
    props: {
      deleteProductUrl: {
        type: String,
        required: false,
        default: '#',
      },
      deleteListUrl: {
        type: String,
        required: false,
        default: '#',
      },
      title: {
        type: String,
        required: true,
        default: 'Delete',
      },
      titleList: {
        type: String,
        required: true,
        default: 'Delete',
      },
      placeholder: {
        type: String,
        required: true,
        default: 'This action is irreversible',
      },
      cancelText: {
        type: String,
        required: true,
        default: 'Cancel',
      },
      deleteText: {
        type: String,
        required: true,
        default: 'Delete',
      },
      deleteTextList: {
        type: String,
        required: true,
        default: 'Delete',
      },
    },
    data() {
      return {
        value: '',
        isHidden: true,
        listId: null,
        listName: '',
        productId: null,
        productAttributeId: null,
      };
    },
    computed: {
      confirmMessage() {
        return this.placeholder.replace('%nameofthewishlist%', this.listName);
      },
      modalTitle() {
        return this.productId ? this.title : this.titleList;
      },
      modalDeleteText() {
        return this.productId ? this.deleteText : this.deleteTextList;
      },
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
        const {data} = await this.$apollo.mutate({
          mutation: this.productId ? removeFromList : deleteList,
          variables: {
            listId: this.listId,
            productId: parseInt(this.productId, 10),
            productAttributeId: parseInt(this.productAttributeId, 10),
            url: this.productId ? this.deleteProductUrl : this.deleteListUrl,
          },
        });

        const response = data.deleteList
          ? data.deleteList
          : data.removeFromList;

        /**
         * As this is not a real SPA, we need to inform others VueJS apps that they need to refetch the list
         */
        EventBus.$emit('refetchList');

        EventBus.$emit('showToast', {
          detail: {
            type: response.success ? 'success' : 'error',
            message: response.message,
          },
        });

        /**
         * Finally hide the modal after deleting the list
         * and reopen the wishlist modal
         */
        this.toggleModal();
      },
    },
    mounted() {
      /**
       * Register to the event showCreateWishlist so others components can toggle this modal
       *
       * @param {String} 'showDeleteWishlist'
       */
      EventBus.$on('showDeleteWishlist', (event) => {
        this.value = '';
        this.listId = event.detail.listId;
        this.listName = event.detail.listName;
        this.productId = null;
        this.productAttributeId = null;

        if (event.detail.productId) {
          this.productId = event.detail.productId;
          this.productAttributeId = event.detail.productAttributeId;
        }

        this.toggleModal();
      });
    },
  };
</script>

<style lang="scss" type="text/scss">
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
