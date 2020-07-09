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
  import renameList from '@graphqlFiles/mutations/renamelist';
  import EventBus from '@components/EventBus';

  /**
   * A modal used to rename a list
   */
  export default {
    name: 'Rename',
    props: {
      url: {
        type: String,
        required: true,
        default: '#',
      },
      title: {
        type: String,
        required: true,
        default: 'Rename wishlist',
      },
      label: {
        type: String,
        required: true,
        default: 'Wishlist name',
      },
      placeholder: {
        type: String,
        required: true,
        default: 'Rename text',
      },
      cancelText: {
        type: String,
        required: true,
        default: 'Cancel',
      },
      renameText: {
        type: String,
        required: true,
        default: 'Rename',
      },
    },
    data() {
      return {
        value: '',
        isHidden: true,
        listId: 0,
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
       * Launch a renameList mutation, then dispatch an event to everycomponent to refetch the list after renaming it
       *
       * @param {Int} listId Id of the list to be renamed
       */
      async renameWishlist() {
        const {data} = await this.$apollo.mutate({
          mutation: renameList,
          variables: {
            name: this.value,
            url: this.url,
            listId: this.listId,
          },
        });

        const {renameList: response} = data;

        EventBus.$emit('refetchList');

        EventBus.$emit('showToast', {
          detail: {
            type: response.success ? 'success' : 'error',
            message: response.message,
          },
        });

        this.toggleModal();
      },
    },
    mounted() {
      /**
       * Register to the showRenameWishlist event so everycomponents can display this modal
       */
      EventBus.$on('showRenameWishlist', (event) => {
        this.value = event.detail.title;
        this.listId = event.detail.listId;
        this.toggleModal();
      });
    },
  };
</script>

<style lang="scss" type="text/scss" scoped>
  .wishlist {
    &-rename {
      .wishlist-modal {
        display: block;
        opacity: 0;
        pointer-events: none;
        z-index: 0;

        &.show {
          opacity: 1;
          pointer-events: all;
          z-index: 1051;
        }
      }
    }
  }
</style>
