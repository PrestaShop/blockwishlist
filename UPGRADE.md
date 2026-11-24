# Upgrade guide

## From 4.x to 5.0

blockwishlist 5.0 removes Vue.js and GraphQL from the front office. The wishlist is now rendered by plain Smarty templates, which a theme can override like any other module template, and driven by a single vanilla script (`public/wishlist.bundle.js`).

The module itself needs no upgrade step: the hooks, the database and the configuration are unchanged. Only themes and modules that customized or extended the front office are concerned.

### Templates

The former templates mixed Smarty and Vue syntax (`@click`, `v-model`, `((title))`...), and the lists, products and button were only rendered by Vue components, so they couldn't be overridden. They are replaced by the templates of `views/templates/front/`.

An override of a former template is no longer used: the module template is rendered instead. Move it to the new path and remove the Vue syntax.

| 4.x template | 5.0 template |
|---|---|
| `components/modals/add-to-wishlist.tpl` | `front/modals/add-to-wishlist.tpl` |
| `components/modals/create.tpl` | `front/modals/create.tpl` |
| `components/modals/delete.tpl` | `front/modals/delete.tpl` |
| `components/modals/login.tpl` | `front/modals/login.tpl` |
| `components/modals/rename.tpl` | `front/modals/rename.tpl` |
| `components/modals/share.tpl` | `front/modals/share.tpl` |
| `components/toast.tpl` | `front/toast.tpl` |
| `components/pagination.tpl` | `front/products/pagination.tpl` |
| `pages/lists.tpl` | `front/pages/lists.tpl`, a list being rendered by `front/lists/list.tpl` |
| `pages/products-list.tpl` | `front/pages/products-list.tpl`, a product being rendered by `front/products/product.tpl` |
| `hook/displayHeader.tpl` (rendered by the `displayFooter` hook) | `hook/displayFooter.tpl` |
| The button of `Button.vue` | `front/button.tpl` |

The other templates (`hook/product/add-button.tpl`, `hook/displayCustomerAccount.tpl`, `hook/account/myaccount-block.tpl`, `hook/displayAdminCustomers.tpl`, `errors/forbidden.tpl`) keep their path.

The script relies on the `js-*` classes and `data-*` attributes of these templates: keep them in an override.

### JavaScript assets

The following assets are no longer registered, and their files are removed from `public/`:

- `blockwishlistGraphql` (`public/graphql.js`) and `blockwishlistVendors` (`public/vendors.js`), the GraphQL client and the Vue chunk of the module;
- the per-component bundles: `addtowishlist`, `button`, `create`, `product`, `productslist`, `rename` and `wishlistcontainer`.

They were internal to the module: a theme or module that loaded them has to ship its own copy.

### WishlistEventBus

`window.WishlistEventBus` is no longer a Vue instance, but keeps the same API: `$on`, `$once`, `$off` and `$emit`, with arrays of events and chaining. The `wishlistEventBusInit` event of `prestashop` is still emitted once it is available.

The events emitted by the module are unchanged, except `hide`, `paginate` and `updatePagination`, which were internal to the Vue components and are no longer emitted.
