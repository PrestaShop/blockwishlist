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
  <div class="wishlist-container">
    <div class="wishlist-container-header">
      <h1>{{ title }}</h1>

      <a @click="openNewWishlistModal" class="wishlist-add-to-new">
        <i class="material-icons">add_circle_outline</i> New wishlist
      </a>
    </div>

    <list :items="lists" @delete="deleteList"></list>
  </div>
</template>

<script>
import List from '@components/List/List';
import getLists from '@graphqlFiles/queries/getlists';
import deleteList from '@graphqlFiles/mutations/deletelist';

export default {
  name: 'WishlistContainer',
  components: {
    List,
  },
  apollo: {
    lists: getLists,
  },
  props: {
    url: '',
    title: '',
    homeLink: '/',
    returnLink: '/profil',
  },
  data() {
    return {
      lists: [],
    };
  },
  methods: {
    openNewWishlistModal() {
      const event = new Event('showCreateWishlist');
      document.dispatchEvent(event);
    },
    async deleteList(id) {
      const list = await this.$apollo.mutate({
        mutation: deleteList,
        variables: {
          listId: id,
        },
        update: store => {
          let data = store.readQuery({query: getLists});

          const lists = data.lists.filter(e => {
            return e.id != id;
          });
          data.lists = lists;

          store.writeQuery({query: getLists, data});
        },
      });

      this.lists = list.data.deleteList;
    },
  },
  mounted() {
    document.addEventListener('refetchList', () => {
      this.$apollo.queries.lists.refetch();
    });
  },
};
</script>

<style lang="scss" type="text/scss">
.wishlist {
  &-container {
    &-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
  }
}
</style>
