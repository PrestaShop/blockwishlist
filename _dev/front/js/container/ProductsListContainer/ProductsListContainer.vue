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
  <div class="wishlist-products-container">
    <div class="wishlist-products-container-header">
      <h1 v-if="products.name">
        Test
        <span
          class="wishlist-products-count"
          v-if="products.datas && products.datas.products"
        >
          ({{ products.datas.products.length }})
        </span>
      </h1>

      <div class="sort-by-row">
        <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">Sort by:</span>
        <div class="col-sm-9 col-xs-8 col-md-9 products-sort-order dropdown">
          <button
            class="btn-unstyle select-title"
            rel="nofollow"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            {{ selectedSort }}
            <i class="material-icons float-xs-right">arrow_drop_down</i>
          </button>
          <div class="dropdown-menu">
            <a
              rel="nofollow"
              @click="changeSelectedSort(lastAdded)"
              class="select-list js-search-link"
            >
              {{ lastAdded }}
            </a>
            <a
              rel="nofollow"
              @click="changeSelectedSort(priceLowHigh)"
              class="select-list current js-search-link"
            >
              {{ priceLowHigh }}
            </a>
            <a
              rel="nofollow"
              @click="changeSelectedSort(priceHighLow)"
              class="select-list js-search-link"
            >
              {{ priceHighLow }}
            </a>
          </div>
        </div>

        <div class="col-sm-3 col-xs-4 hidden-md-up filter-button">
          <button
            id="search_filter_toggler"
            class="btn btn-secondary"
          >
            {{ filter }}
          </button>
        </div>
      </div>
    </div>

    <section
      id="content"
      class="page-content card card-block"
    >
      <ul
        class="wishlist-products-list"
        v-if="products.datas && products.datas.products.length > 0"
      >
        <li
          class="wishlist-products-item"
          v-for="product in products.datas.products"
          :key="product.id_product_attribute"
        >
          <Product
            :product="product"
            :add-to-cart="addToCart"
            :customize-text="customizeText"
            :quantity-text="quantityText"
            :list-id="listId ? listId : parseInt(currentWishlist.id_wishlist)"
            :is-share="wishlistProducts ? true : false"
          />
        </li>
      </ul>

      <ContentLoader
        v-if="!products.datas"
        class="wishlist-list-loader"
        height="105"
      >
        <rect
          x="0"
          y="12"
          rx="3"
          ry="0"
          width="100%"
          height="11"
        />
        <rect
          x="0"
          y="36"
          rx="3"
          ry="0"
          width="100%"
          height="11"
        />
        <rect
          x="0"
          y="60"
          rx="3"
          ry="0"
          width="100%"
          height="11"
        />
        <rect
          x="0"
          y="84"
          rx="3"
          ry="0"
          width="100%"
          height="11"
        />
      </ContentLoader>

      <p
        class="wishlist-list-empty"
        v-if="products.datas && products.success === false"
      >
        {{ products.message }}
      </p>
    </section>
  </div>
</template>

<script>
  import Product from '@components/Product/Product';
  import getProducts from '@graphqlFiles/queries/getproducts';
  import {ContentLoader} from 'vue-content-loader';
  import EventBus from '@components/EventBus';

  /**
   * This component act as a smart component wich will handle every actions of the list one
   */
  export default {
    name: 'ProductsListContainer',
    components: {
      Product,
      ContentLoader,
    },
    apollo: {
      products: {
        query: getProducts,
        variables() {
          return {
            listId: this.listId,
            url: this.url,
          };
        },
        skip() {
          return true;
        },
      },
    },
    props: {
      url: {
        type: String,
        required: false,
        default: '#',
      },
      title: {
        type: String,
        required: true,
        default: 'Product name',
      },
      defaultSort: {
        type: String,
        required: true,
        default: 'All',
      },
      listId: {
        type: Number,
        required: false,
        default: 0,
      },
      wishlistProducts: {
        type: Array,
        required: false,
      },
      wishlist: {
        type: String,
        required: false,
      },
      addToCart: {
        type: String,
        required: true,
        default: 'Add to cart',
      },
      customizeText: {
        type: String,
        required: true,
        default: 'Customize',
      },
      quantityText: {
        type: String,
        required: true,
        defalut: 'Quantity',
      },
      lastAdded: {
        type: String,
        required: true,
        default: 'Last added',
      },
      priceLowHigh: {
        type: String,
        required: true,
        default: 'Price low to high',
      },
      priceHighLow: {
        type: String,
        required: true,
        default: 'Price high to low',
      },
      filter: {
        type: String,
        required: true,
        default: 'Filter',
      },
    },
    data() {
      return {
        products: [],
        currentWishlist: {},
        selectedSort: '',
      };
    },
    methods: {
      /**
       * Sort by the select drop down
       * @param {String} value The value selected
       */
      async changeSelectedSort(value) {
        this.selectedSort = value;
      },
    },
    mounted() {
      if (this.listId) {
        this.$apollo.queries.products.skip = false;
      }
      this.selectedSort = this.defaultSort;

      if (this.wishlist) {
        this.currentWishlist = JSON.parse(this.wishlist);
        this.products.name = this.currentWishlist.name;
      }

      if (this.wishlistProducts) {
        const products = JSON.parse(this.wishlistProducts);

        if (products.length > 0) {
          this.products.datas = {products};
        }
      }

      /**
       * Register to the event refetchProducts so if an other component update it, this one can update his list
       *
       * @param {String} 'refetchProduct' The event I decided to create to communicate between VueJS Apps
       */
      EventBus.$on('refetchList', () => {
        this.$apollo.queries.products.refetch();
      });

      EventBus.$on('paginationNumbers', (payload) => payload);
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import '@scss/_variables';

  .wishlist {
    &-list-loader {
      padding: 0 20px;
      width: 100%;
    }

    &-products-container {
      .sort-by-row {
        min-width: 315px;
        display: flex;
        align-items: center;

        a {
          cursor: pointer;
        }

        .sort-by {
          padding: 0;
        }

        .products-sort-order {
          padding: 0;
        }
      }

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

    &-products {
      &-list {
        display: flex;
        flex-wrap: wrap;
        margin: -25px;
        padding: 20px 45px;
      }

      &-count {
        color: #7a7a7a;
        font-size: 22px;
        font-weight: normal;
        line-height: 30px;
      }
    }
  }

  #module-blockwishlist-productslist,
  #module-blockwishlist-view {
    #wrapper .container {
      width: 975px;
    }
  }

  @media screen and (max-width: 768px) {
    .wishlist {
      &-products-container {
        &-header {
          flex-wrap: wrap;

          .products-sort-order {
            flex: 1;
          }

          .filter-button {
            width: auto;
            padding-right: 0;
          }

          .sort-by-row {
            width: 100%;
          }
        }

        .page-content.card {
          box-shadow: 2px 2px 8px 0 rgba(0, 0, 0, 0.2);
          background-color: #fff;
          margin-top: 20px;
        }

        .wishlist-products-list {
          justify-content: center;
          margin: 0;
          padding: 15px;
        }
      }
    }
  }
</style>
