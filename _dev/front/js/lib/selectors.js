/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

/**
 * The hooks of the script in the templates, following the data-ps-* conventions of the themes:
 * data-ps-component on the root of a component, data-ps-ref on the elements it reads,
 * data-ps-action on the elements triggering an action, data-ps-target on the areas it reloads.
 * The classes only style the templates, so an override can change them freely.
 */
const component = (name) => `[data-ps-component="${name}"]`;
const ref = (name) => `[data-ps-ref="${name}"]`;
const action = (name) => `[data-ps-action="${name}"]`;
const target = (name) => `[data-ps-target="${name}"]`;

export default {
  components: {
    addToWishlist: component('wishlist-add-to-wishlist'),
    button: component('wishlist-button'),
    create: component('wishlist-create'),
    delete: component('wishlist-delete'),
    lists: component('wishlist-lists-page'),
    login: component('wishlist-login'),
    products: component('wishlist-products-page'),
    rename: component('wishlist-rename'),
    share: component('wishlist-share'),
    toast: component('wishlist-toast'),
  },
  modal: {
    dialog: ref('wishlist-modal'),
    backdrop: ref('wishlist-modal-backdrop'),
    close: action('wishlist-close-modal'),
  },
  addToWishlist: {
    lists: ref('wishlist-lists'),
    loader: ref('wishlist-lists-loader'),
    empty: ref('wishlist-lists-empty'),
    itemTemplate: ref('wishlist-list-item-template'),
    item: ref('wishlist-list-item'),
    itemName: ref('wishlist-list-item-name'),
  },
  button: {
    template: ref('wishlist-button-template'),
    toggle: action('wishlist-toggle-product'),
    icon: ref('wishlist-button-icon'),
  },
  delete: {
    message: ref('wishlist-delete-message'),
    product: ref('wishlist-delete-product'),
    list: ref('wishlist-delete-list'),
  },
  lists: {
    item: ref('wishlist-list-item'),
    menu: ref('wishlist-list-menu'),
    action: '[data-ps-action]',
    area: target('wishlist-lists'),
  },
  products: {
    product: ref('wishlist-product'),
    addToCart: action('wishlist-add-to-cart'),
    remove: action('wishlist-remove-product'),
    area: target('wishlist-products'),
  },
  toast: {
    text: ref('wishlist-toast-text'),
  },
  createList: action('wishlist-create-list'),
  // Rendered by the theme
  theme: {
    miniature: '.js-product-miniature',
    miniatureThumbnail: '.thumbnail-container',
    miniatureLists: '#products, .featured-products',
    quantity: '.product-quantity input#quantity_wanted',
  },
};
