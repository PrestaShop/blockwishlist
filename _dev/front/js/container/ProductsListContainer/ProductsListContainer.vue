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
      <h1>
        {{ title }}

        <span
          class="wishlist-products-count"
          v-if="products.datas && products.datas.products"
        >
          ({{ products.datas.pagination.total_items }})
        </span>
      </h1>

      <div
        class="sort-by-row"
        v-if="products.datas"
      >
        <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{{ filter }}</span>
        <div class="col-sm-9 col-xs-8 col-md-9 products-sort-order dropdown">
          <button
            class="btn-unstyle select-title"
            rel="nofollow"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            {{ currentSort }}
            <i class="material-icons float-xs-right">arrow_drop_down</i>
          </button>
          <div class="dropdown-menu">
            <a
              rel="nofollow"
              @click="changeSelectedSort(sort)"
              class="select-list"
              :key="key"
              v-for="(sort, key) in productList"
            >
              {{ sort.label }}
            </a>
          </div>
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
          v-for="(product, key) in products.datas.products"
          :key="key"
        >
          <Product
            :product="product"
            :add-to-cart="addToCart"
            :customize-text="customizeText"
            :quantity-text="quantityText"
            :list-name="title"
            :list-id="
              listId ? listId : parseInt(currentWishlist.id_wishlist, 10)
            "
            :is-share="share"
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
        v-if="products.datas && products.datas.products.length <= 0"
      >
        {{ noProductsMessage }}
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
            url: this.apiUrl,
          };
        },
        skip() {
          return true;
        },
        fetchPolicy: 'network-only',
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
      },
      filter: {
        type: String,
        required: true,
      },
      noProductsMessage: {
        type: String,
        required: true,
      },
      listId: {
        type: Number,
        required: false,
        default: 0,
      },
      addToCart: {
        type: String,
        required: true,
      },
      share: {
        type: Boolean,
        required: true,
      },
      customizeText: {
        type: String,
        required: true,
      },
      quantityText: {
        type: String,
        required: true,
      },
    },
    data() {
      return {
        products: [],
        currentWishlist: {},
        apiUrl: window.location.href,
        selectedSort: '',
      };
    },
    methods: {
      /**
       * Sort by the select drop down
       * @param {String} value The value selected
       */
      async changeSelectedSort(value) {
        this.selectedSort = value.label;
        this.apiUrl = value.url;
      },
    },
    computed: {
      productList() {
        const productList = this.products.datas.sort_orders.filter(
          (sort) => sort.label !== this.products.datas.sort_selected,
        );

        return productList;
      },
      currentSort() {
        return this.selectedSort !== ''
          ? this.selectedSort
          : this.products.datas.sort_selected;
      },
    },
    mounted() {
      if (this.listId) {
        this.$apollo.queries.products.skip = false;
      }

      /**
       * Register to the event refetchProducts so if an other component update it, this one can update his list
       *
       * @param {String} 'refetchProduct' The event I decided to create to communicate between VueJS Apps
       */
      EventBus.$on('refetchList', () => {
        this.$apollo.queries.products.refetch();
      });

      EventBus.$on('updatePagination', (payload) => {
        this.products = false;
        this.apiUrl = payload.page.url;
      });
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import '@scss/_variables';

  .wishlist {
    &-list-loader {
      padding: 0 1.25rem;
      width: 100%;
    }

    &-list-empty {
      font-size: 30;
      text-align: center;
      padding: 1.875rem;
      padding-bottom: 1.25rem;
      font-weight: bold;
      color: #000;
    }

    &-products-container {
      .sort-by-row {
        min-width: 19.6875rem;
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
        margin-bottom: 1.25rem;
      }

      @at-root #main & .card.page-content {
        padding: 0;
        margin-bottom: 0.75rem;
      }
    }

    &-products {
      &-list {
        display: flex;
        flex-wrap: wrap;
        margin: -1.5625rem;
        padding: 1.25rem 2.8125rem;
        margin-top: 0;
      }

      &-count {
        color: #7a7a7a;
        font-size: 1.375rem;
        font-weight: normal;
        line-height: 1.875rem;
      }
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
          box-shadow: 0.125rem 0.125rem 0.5rem 0 rgba(0, 0, 0, 0.2);
          background-color: #fff;
          margin-top: 1.25rem;
        }

        .wishlist-products-list {
          justify-content: center;
          margin: 0;
          padding: 0.9375rem;
        }
      }
    }
  }
</style>
