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
  import ChooseList from '../ChooseList/ChooseList'

  export default {
    name: 'AddToWishlist',
    components: {
      ChooseList
    },
    props: {
      url: '',
      title: '',
      label: '',
      placeholder: '',
      cancelText: '',
      createText: ''
    },
    data() {
      return {
        value: '',
        isHidden: true,
        productId: 0
      }
    },
    methods: {
      /**
       * Open and close the modal
       */
      toggleModal(forceOpen) {
        if (forceOpen === true) {
          this.isHidden = false
        } else {
          this.isHidden = !this.isHidden
        }
      },
      /**
       * Dispatch an event to the Create component
       */
      openNewWishlistModal() {
        this.toggleModal()

        const event = new Event('showCreateWishlist')
        document.dispatchEvent(event)
      }
    },
    mounted() {
      /**
       * Register to the event showAddToWishList so others component can open the modal of the current component
       */
      document.addEventListener('showAddToWishList', event => {
        this.toggleModal(event.detail.forceOpen ? event.detail.forceOpen : null)
        this.productId = event.detail.productId
      })
    }
  }
</script>

<style lang="scss" type="text/scss" scoped>
  @import '@scss/_variables';

  .wishlist {
    &-add-to-new {
      cursor: pointer;
      transition: 0.2s ease-out;
      height: 16px;
      width: 79px;
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

    &-add-to {
      .modal {
        &-body {
          padding: 0;
        }

        &-footer {
          text-align: left;
          padding: 12px 20px;
        }
      }
    }
  }
</style>
