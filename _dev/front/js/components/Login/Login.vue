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
  import EventBus from '@components/EventBus';
  import prestashop from 'prestashop';

  /**
   * This component display a modal where you can redirect to login page
   */
  export default {
    name: 'Login',
    props: {
      cancelText: {
        type: String,
        required: true,
        default: 'Cancel',
      },
      loginText: {
        type: String,
        required: true,
        default: 'Login',
      },
    },
    data() {
      return {
        value: '',
        isHidden: true,
        listId: null,
        prestashop,
      };
    },
    methods: {
      /**
       * Toggle the modal
       */
      toggleModal() {
        this.isHidden = !this.isHidden;
      },
    },
    mounted() {
      /**
       * Register to the event showCreateWishlist so others components can toggle this modal
       *
       * @param {String} 'showDeleteWishlist'
       */
      EventBus.$on('showLogin', () => {
        this.toggleModal();
      });
    },
  };
</script>

<style lang="scss" type="text/scss" scoped>
  .wishlist {
    &-login {
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
