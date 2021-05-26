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
    <a
      class="wishlist-product-link"
      :href="product.canonical_url"
    >
      <div class="wishlist-product-image">
        <img
          v-if="product.default_image"
          :src="product.default_image.large.url"
          :alt="product.default_image.legend"
          :title="product.default_image.legend"
          :class="{
            'wishlist-product-unavailable': !product.add_to_cart_url
          }"
        >
        <img
          v-else-if="product.cover"
          :src="product.cover.large.url"
          :alt="product.cover.legend"
          :title="product.cover.legend"
          :class="{
            'wishlist-product-unavailable': !product.add_to_cart_url
          }"
        >
        <img
          v-else
          :src="prestashop.urls.no_picture_image.bySize.home_default.url"
        >

        <p
          class="wishlist-product-availability"
          v-if="product.show_availability"
        >
          <i
            class="material-icons"
            v-if="product.availability === 'unavailable'"
          >
            block
          </i>
          <i
            class="material-icons"
            v-if="product.availability === 'last_remaining_items'"
          >
            warning
          </i>
          {{ product.availability_message }}
        </p>
      </div>
      <div class="wishlist-product-right">
        <p class="wishlist-product-title">{{ product.name }}</p>

        <p class="wishlist-product-price">
          <span
            class="wishlist-product-price-promo"
            v-if="product.has_discount"
          >
            {{ product.regular_price }}
          </span>
          {{ product.price }}
        </p>

        <div class="wishlist-product-combinations">
          <p class="wishlist-product-combinations-text">
            <template v-for="(attribute, key, index) of product.attributes">
              {{ attribute.group }} : {{ attribute.name }}
              <span
                :key="key"
                v-if="index <= Object.keys(product.attributes).length - 1"
              >
                -
              </span>

              <span
                :key="key + 'end'"
                v-if="index == Object.keys(product.attributes).length - 1"
              >
                {{ quantityText }} : {{ product.wishlist_quantity }}
              </span>
            </template>

            <span v-if="Object.keys(product.attributes).length === 0">
              {{ quantityText }} : {{ product.wishlist_quantity }}
            </span>
          </p>

          <a
            :href="product.canonical_url"
            v-if="!isShare"
          >
            <i class="material-icons">create</i>
          </a>
        </div>
      </div>
    </a>

    <div class="wishlist-product-bottom">
      <button
        class="btn wishlist-product-addtocart"
        :class="{
          'btn-secondary': product.customizable === '1',
          'btn-primary': product.customizable === '0'
        }"
        :disabled="isDisabled"
        @click="
          product.add_to_cart_url || product.customizable === '1'
            ? addToCartAction()
            : null
        "
      >
        <i
          class="material-icons shopping-cart"
          v-if="product.customizable === '0'"
        >
          shopping_cart
        </i>
        {{ product.customizable === '1' ? customizeText : addToCart }}
      </button>

      <button
        class="wishlist-button-add"
        v-if="!isShare"
        @click="removeFromWishlist"
      >
        <i class="material-icons">delete</i>
      </button>
    </div>

    <p
      class="wishlist-product-availability wishlist-product-availability-responsive"
      v-if="product.show_availability"
    >
      <i
        class="material-icons"
        v-if="product.availability === 'unavailable'"
      >
        block
      </i>
      <i
        class="material-icons"
        v-if="product.availability === 'last_remaining_items'"
      >
        warning
      </i>
      {{ product.availability_message }}
    </p>
  </div>
</template>

<script>
  import EventBus from '@components/EventBus';
  import headers from '@constants/headers';
  import prestashop from 'prestashop';
  import wishlistAddProductToCartUrl from 'wishlistAddProductToCartUrl';

  export default {
    name: 'Product',
    props: {
      product: {
        type: Object,
        required: true,
        default: null,
      },
      listId: {
        type: Number,
        required: true,
        default: null,
      },
      listName: {
        type: String,
        required: true,
        default: '',
      },
      isShare: {
        type: Boolean,
        required: false,
        default: false,
      },
      customizeText: {
        type: String,
        required: true,
        default: 'Customize',
      },
      quantityText: {
        type: String,
        required: true,
        default: 'Quantity',
      },
      addToCart: {
        type: String,
        required: true,
      },
      status: {
        type: Number,
        required: false,
        default: 0,
      },
      hasControls: {
        type: Boolean,
        required: false,
        default: true,
      },
    },
    data() {
      return {
        prestashop,
      };
    },
    computed: {
      isDisabled() {
        if (this.product.customizable === '1') {
          return false;
        }

        return !this.product.add_to_cart_url;
      },
    },
    methods: {
      /**
       * Remove the product from the wishlist
       */
      async removeFromWishlist() {
        EventBus.$emit('showDeleteWishlist', {
          detail: {
            listId: this.listId,
            listName: this.listName,
            productId: this.product.id,
            productAttributeId: this.product.id_product_attribute,
          },
        });
      },
      async addToCartAction() {
        if (this.product.add_to_cart_url && this.product.customizable !== '1') {
          try {
            const datas = new FormData();
            datas.append('qty', this.product.wishlist_quantity);
            datas.append('id_product', this.product.id_product);
            datas.append('id_customization', this.product.id_customization);

            const response = await fetch(
              `${this.product.add_to_cart_url}&action=update`,
              {
                method: 'POST',
                headers: headers.addToCart,
                body: datas,
              },
            );

            const resp = await response.json();

            prestashop.emit('updateCart', {
              reason: {
                idProduct: this.product.id_product,
                idProductAttribute: this.product.id_product_attribute,
                idCustomization: this.product.id_customization,
                linkAction: 'add-to-cart',
              },
              resp,
            });

            /* eslint-disable */
            const statResponse = await fetch(
              `${wishlistAddProductToCartUrl}&params[idWishlist]=${this.listId}&params[id_product]=${this.product.id_product}&params[id_product_attribute]=${this.product.id_product_attribute}&params[quantity]=${this.product.wishlist_quantity}`,
              {
                headers: {
                  'Content-Type':
                    'application/x-www-form-urlencoded; charset=UTF-8',
                  Accept: 'application/json, text/javascript, */*; q=0.01'
                }
              }
            );
            /* eslint-enable */

            await statResponse.json();
          } catch (error) {
            prestashop.emit('handleError', {
              eventType: 'addProductToCart',
              resp: error,
            });
          }
        } else {
          window.location.href = this.product.canonical_url;
        }
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import '@scss/_variables';

  .wishlist {
    &-products-item {
      margin: 25px;
    }

    &-product {
      max-width: 250px;
      width: 100%;
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;

      &-unavailable {
        opacity: 0.5;
      }

      &-availability {
        display: flex;
        align-items: flex-start;
        margin-bottom: 0;
        color: #232323;
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 0;
        line-height: 17px;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 17px;
        z-index: 5;
        min-width: 80%;
        justify-content: center;

        i {
          color: #ff4c4c;
          margin-right: 5px;
          font-size: 18px;
        }

        &-responsive {
          display: none;
          position: inherit;
          transform: inherit;
          bottom: inherit;
          margin-top: 10px;
          left: inherit;
        }
      }

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

        &.btn-secondary {
          background-color: #dddddd;

          &:hover {
            background-color: #dddddd;
            opacity: 0.7;
          }
        }

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
        min-width: 40px;
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

  @media screen and (max-width: 768px) {
    .wishlist {
      &-button-add {
        position: inherit;
        margin-left: 10px;
      }

      &-products-item {
        width: 100%;
        margin: 0;
        margin-bottom: 30px;

        &:not(:last-child) {
          margin-bottom: 30px;
        }
      }

      &-product {
        margin: 0;
        width: 100%;
        max-width: 100%;

        &-link {
          &:hover {
            img {
              transform: inherit;
            }
          }
        }

        &-bottom {
          display: flex;
          align-items: center;
          justify-content: space-between;
        }

        &-right {
          flex: 1;
        }

        &-availability {
          display: none;

          &-responsive {
            display: block;
            min-width: 100%;
            justify-content: flex-start;
          }
        }

        &-image {
          width: 100px;
          height: 100px;
          margin-right: 20px;
          position: inherit;

          img {
            position: inherit;
            left: inherit;
            top: inherit;
            transform: inherit;
          }
        }

        &-link {
          display: flex;
          align-items: flex-start;
        }

        &-title {
          margin-top: 0;
        }
      }
    }
  }
</style>
