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
<script>
import renameList from '@graphqlFiles/mutations/renamelist';

/**
 * A modal used to rename a list
 */
export default {
  name: 'Rename',
  props: {
    url: '',
    title: '',
    label: '',
    placeholder: '',
    cancelText: '',
    createText: '',
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
    async renameWishlist(listId) {
      await this.$apollo.mutate({
        mutation: renameList,
        variables: {
          name: this.value,
          userId: 1,
          listId: this.listId,
        },
      });

      const event = new Event('refetchList');

      document.dispatchEvent(event);
    },
  },
  mounted() {
    /**
     * Register to the showRenameWishlist event so everycomponents can display this modal
     */
    document.addEventListener('showRenameWishlist', event => {
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
