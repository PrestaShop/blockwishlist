{*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
  window.BlockWishlistModule = window.BlockWishlistModule || {ldelim}{rdelim};
  window.BlockWishlistModule.config = {
    idProducts: {$wishlist_products|json_encode},
    isLogged: {if Context::getContext()->customer->isLogged()}true{else}false{/if},
    token: '{Tools::getToken()}',
    urls: {
      cart: '{url entity='module' name='blockwishlist' controller='cart'}',
      managewishlist: '{url entity='module' name='blockwishlist' controller='managewishlist'}',
      sendwishlist: '{url entity='module' name='blockwishlist' controller='sendwishlist'}',
      view: '{url entity='module' name='blockwishlist' controller='view'}'
    }
  };
  window.BlockWishlistModule.translations = {
    loginRequired: '{l s='You must be logged in to manage your wishlist.' d='Modules.BlockWishlist.Top' js=1}',
    addedToWishlist: '{l s='The product was successfully added to your wishlist.' d='Modules.BlockWishlist.Top' js=1}',
  };
</script>
