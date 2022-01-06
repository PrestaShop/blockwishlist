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
  import ChooseList from '../ChooseList/ChooseList';

  export default {
    name: 'AddToWishlist',
    components: {
      ChooseList,
    },
    props: {
      url: {
        type: String,
        required: true,
        default: '#',
      },
    },
    data() {
      return {
        value: '',
        isHidden: true,
        productAttributeId: 0,
        productId: 0,
        quantity: 0,
      };
    },
    methods: {
      /**
       * Open and close the modal
       */
      toggleModal(forceOpen) {
        if (forceOpen === true) {
          this.isHidden = false;
        } else {
          this.isHidden = !this.isHidden;
        }
      },
      /**
       * Dispatch an event to the Create component
       */
      openNewWishlistModal() {
        this.toggleModal();

        EventBus.$emit('showCreateWishlist');
      },
    },
    mounted() {
      /**
       * Register to the event showAddToWishList so others component can open the modal of the current component
       */
      EventBus.$on('showAddToWishList', (event) => {
        this.toggleModal(
          event.detail.forceOpen ? event.detail.forceOpen : null,
        );

        if (event.detail.productId) {
          this.productId = event.detail.productId;
        }

        if (typeof event.detail.productAttributeId === 'number') {
          this.productAttributeId = event.detail.productAttributeId;
        }

        if (event.detail.quantity) {
          this.quantity = event.detail.quantity;
        }
      });
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import '@scss/_variables';

  .wishlist {
    &-add-to-new {
      cursor: pointer;
      transition: 0.2s ease-out;
      font-size: 0.875rem;
      letter-spacing: 0;
      line-height: 1rem;

      &:hover {
        opacity: 0.7;
      }

      i {
        margin-right: 0.3125rem;
        vertical-align: middle;
        color: $blue;
        margin-top: -0.125rem;
        font-size: 1.25rem;
      }
    }

    &-add-to {
      .modal {
        &-body {
          padding: 0;
        }

        &-footer {
          text-align: left;
          padding: 0.75rem 1.25rem;
        }
      }
    }
  }
</style>
