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
<template>
  <div class="wishlist-product">
    <a class="wishlist-product-link" href="#">
      <div class="wishlist-product-image">
        <img src="/prestashop/img/p/2/1/21.jpg" />
      </div>
      <p class="wishlist-product-title">{{ product.name }}</p>

      <p class="wishlist-product-price">
        <span class="wishlist-product-price-promo">€28.68</span>
        €{{ product.price }}
      </p>

      <div class="wishlist-product-combinations">
        <p class="wishlist-product-combinations-text">
          Size: S - Colour: White - Quantity: 1
        </p>

        <a href="#">
          <i class="material-icons">create</i>
        </a>
      </div>
    </a>

    <button class="btn btn-primary wishlist-product-addtocart">
      <i class="material-icons shopping-cart">shopping_cart</i>
      Add to cart
    </button>

    <button class="wishlist-button-add" @click="removeFromWishlist">
      <i class="material-icons">delete</i>
    </button>
  </div>
</template>

<script>
  import removeFromList from '@graphqlFiles/mutations/removeFromList';
  import prestashop from 'prestashop';

  export default {
    name: 'Product',
    props: {
      product: null,
      listId: null,
      productId: null,
      status: null,
      hasControls: true
    },
    methods: {
      /**
       * Remove the product from the wishlist
       */
      async removeFromWishlist() {
        const event = new CustomEvent('showDeleteWishlist', {
          detail: { listId: this.listId, productId: this.productId, userId: 1 }
        });

        document.dispatchEvent(event);
        event.preventDefault();
      }
    },
    mounted() {}
  };
</script>

<style lang="scss" type="text/scss">
  @import '@scss/_variables';

  .wishlist {
    &-product {
      max-width: 250px;
      width: 100%;
      margin: 25px;
      position: relative;

      &-link {
        &:focus {
          text-decoration: none;
        }

        &:hover {
          img {
            transform: translate(-50%, -50%) scale(1.1);
          }
        }
      }

      &-title {
        margin-top: 10px;
        margin-bottom: 5px;
        color: #737373;
        font-size: 14px;
        letter-spacing: 0;
        line-height: 19px;
      }

      &-image {
        width: 250px;
        height: 250px;
        position: relative;
        overflow: hidden;

        img {
          position: absolute;
          max-width: 100%;
          max-height: 100%;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          transition: 0.25s ease-out;
        }
      }

      &-price {
        color: #232323;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 0;
        line-height: 22px;

        &-promo {
          text-decoration: line-through;
          color: #737373;
          font-size: 14px;
          font-weight: bold;
          letter-spacing: 0;
          line-height: 19px;
          margin-right: 5px;
          vertical-align: middle;
          display: inline-block;
          margin-top: -3px;
        }
      }

      &-combinations {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        a {
          display: block;
          color: #7a7a7a;

          &:hover {
            color: $blue;
          }
        }

        &-text {
          color: #7a7a7a;
          font-size: 13px;
          letter-spacing: 0;
          line-height: 20px;
          min-height: 50px;
          margin: 0;
        }
      }

      &-addtocart {
        width: 100%;
        text-transform: inherit;
        padding-left: 10px;

        i {
          margin-top: -3px;
        }
      }
    }

    &-button {
      &-add {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        width: 40px;
        padding-top: 3px;
        background-color: #ffffff;
        box-shadow: 2px 2px 4px 0 rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        cursor: pointer;
        transition: 0.2s ease-out;
        border: none;

        &:hover {
          opacity: 0.7;
        }

        &:focus {
          outline: 0;
        }

        &:active {
          transform: scale(1.2);
        }

        i {
          color: #7a7a7a;
          margin-top: -2px;
        }
      }
    }
  }
</style>
