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
        <i class="material-icons">add_circle_outline</i>
        {{ addText }}
      </a>
    </div>

    <section id="content" class="page-content card card-block">
      <list
        :items="lists"
        @delete="deleteList"
        :renameText="renameText"
        :shareText="shareText"
      ></list>
    </section>
  </div>
</template>

<script>
  import List from '@components/List/List'
  import getLists from '@graphqlFiles/queries/getlists'
  import deleteList from '@graphqlFiles/mutations/deletelist'

  /**
   * This component act as a smart component wich will handle every actions of the list one
   */
  export default {
    name: 'WishlistContainer',
    components: {
      List
    },
    apollo: {
      lists: getLists
    },
    props: {
      url: '',
      title: '',
      homeLink: '',
      returnLink: '',
      addText: '',
      renameText: '',
      shareText: ''
    },
    data() {
      return {
        lists: []
      }
    },
    methods: {
      /**
       * Send an event to opoen the Create Wishlist Modal
       */
      openNewWishlistModal() {
        const event = new Event('showCreateWishlist')

        document.dispatchEvent(event)
      },
      /**
       * Delete a list by launching a mutation, updating cache and then on response replacing the lists state
       *
       * @param {Int} id The list id to be removed
       */
      async deleteList(id) {
        const list = await this.$apollo.mutate({
          mutation: deleteList,
          variables: {
            listId: id
          },
          /**
           * Remove the list from the cache
           */
          update: store => {
            let data = store.readQuery({ query: getLists })

            const lists = data.lists.filter(e => {
              return e.id != id
            })
            data.lists = lists

            store.writeQuery({ query: getLists, data })
          }
        })

        /**
         * Finally refetch the list from the response of the mutation
         */
        this.lists = list.data.deleteList
      }
    },
    mounted() {
      /**
       * Register to the event refetchList so if an other component update it, this one can update his list
       *
       * @param {String} 'refetchList' The event I decided to create to communicate between VueJS Apps
       */
      document.addEventListener('refetchList', () => {
        this.$apollo.queries.lists.refetch()
      })
    }
  }
</script>

<style lang="scss" type="text/scss" scoped>
  @import '@scss/_variables';

  .wishlist {
    &-container {
      &-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
      }

      @at-root #main & .card.page-content {
        padding: 0;
        margin-bottom: 12px;
      }
    }

    &-add-to-new {
      cursor: pointer;
      transition: 0.2s ease-out;
      font-size: 14px;
      letter-spacing: 0;
      line-height: 16px;

      &:not([href]):not([tabindex]) {
        color: $blue;
      }

      &:hover {
        opacity: 0.7;
      }

      i {
        margin-right: 5px;
        vertical-align: middle;
        color: $blue;
        margin-top: -2px;
        font-size: 20px;
      }
    }
  }
</style>
