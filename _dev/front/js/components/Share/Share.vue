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
  import shareList from '@graphqlFiles/mutations/sharelist'

  /**
   * This component display a modal where you can create a wishlist
   */
  export default {
    name: 'Share',
    props: {
      url: '',
      title: '',
      label: '',
      placeholder: '',
      cancelText: '',
      copyText: '',
      copiedText: ''
    },
    data() {
      return {
        value: '',
        isHidden: true,
        actionText: ''
      }
    },
    methods: {
      /**
       * Toggle the modal
       */
      toggleModal() {
        this.isHidden = !this.isHidden
      },
      /**
       * Copy the link in the input value
       */
      copyLink() {
        console.log('copy')
        const shareInput = document.querySelector(
          '.wishlist-share .form-control'
        )

        shareInput.select()
        shareInput.setSelectionRange(0, 99999)

        document.execCommand('copy')

        this.actionText = this.copiedText
      }
    },
    mounted() {
      this.actionText = this.copyText

      /**
       * Register to the event showCreateWishlist so others components can toggle this modal
       *
       * @param {String} 'showCreateWishlist'
       */
      document.addEventListener('showShareWishlist', async event => {
        const { data } = await this.$apollo.mutate({
          mutation: shareList,
          variables: {
            listId: event.detail.listId,
            userId: event.detail.userId
          }
        })
        const result = data.shareList
        this.value = result.url
        this.toggleModal()
      })
    }
  }
</script>

<style lang="scss" type="text/scss" scoped>
  .wishlist {
    &-create {
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
