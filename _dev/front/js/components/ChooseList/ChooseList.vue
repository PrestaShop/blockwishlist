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
      {{ list.title }}
    </li>
  </ul>
</template>

<script>
import getLists from '@graphqlFiles/queries/getlists';
import addtolist from '@graphqlFiles/mutations/addtolist';

export default {
  name: 'ChooseList',
  apollo: {
    lists: getLists,
  },
  methods: {
    async select(listId) {
      const list = await this.$apollo.mutate({
        mutation: addtolist,
        variables: {
          listId,
          userId: 1,
          productId: 1,
        },
      });

      this.$emit('hide');
    },
  },
  mounted() {},
};
</script>

<style lang="scss" type="text/scss">
.wishlist {
  &-list {
    &-item {
    }
  }
}
</style>
