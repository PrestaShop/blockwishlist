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

{extends file='page.tpl'}

{block name="page_content"}

<div id="mywishlist">
  {*{capture name=path}*}
    {*<a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account' mod='blockwishlist'}</a>*}
    {*<span class="navigation-pipe">{$navigationPipe}</span>*}
    {*<a href="{$link->getModuleLink('blockwishlist', 'mywishlist')|escape:'html'}">{l s='My wishlists' mod='blockwishlist'}</a>*}
    {*{if isset($current_wishlist)}*}
      {*<span class="navigation-pipe">{$navigationPipe}</span>*}
      {*{$current_wishlist.name}*}
    {*{/if}*}
  {*{/capture}*}

  <h2>{l s='My wishlists' mod='blockwishlist'}</h2>

  {*{include file="$tpl_dir./errors.tpl"}*}

  {if $id_customer|intval neq 0}
    <form method="post" class="std" id="form_wishlist">
      <input type="hidden" name="token" value="{$token}"/>
      <div>
        <h3>{l s='New wishlist' d='Modules.BlockWistList.MyWistList'}</h3>
        <div class="form-group">
          <label for="name">{l s='Name' d='Modules.BlockWistList.MyWistList'}</label>
          <input type="text"
                 class="form-control"
                 id="name"
                 name="name"
                 value="{if isset($smarty.post.name) and $errors|@count > 0}{$smarty.post.name}{/if}"
          >
        </div>
        <div class="form-group">
          <input
                  type="submit"
                  name="submitWishlist"
                  id="submitWishlist"
                  value="{l s='Save' d='Modules.BlockWistList.MyWistList'}"
                  class="btn btn-primary"
          >
        </div>
      </div>
    </form>
    {if $wishlists}
      <div id="block-history" class="block-center">
        <table class="table">
          <thead>
            <tr>
              <th class="first_item">{l s='Name' d='Modules.BlockWistList.MyWistList'}</th>
              <th class="item mywishlist_first">{l s='Qty' d='Modules.BlockWistList.MyWistList'}</th>
              <th class="item mywishlist_first">{l s='Viewed' d='Modules.BlockWistList.MyWistList'}</th>
              <th class="item mywishlist_second">{l s='Created' d='Modules.BlockWistList.MyWistList'}</th>
              <th class="item mywishlist_second">{l s='Direct Link' d='Modules.BlockWistList.MyWistList'}</th>
              <th class="item mywishlist_second">{l s='Default' d='Modules.BlockWistList.MyWistList'}</th>
              <th class="last_item mywishlist_first">{l s='Delete' d='Modules.BlockWistList.MyWistList'}</th>
            </tr>
          </thead>
          <tbody>
            {section name=i loop=$wishlists}
              <tr id="wishlist_{$wishlists[i].id_wishlist|intval}">
                <td style="width:200px;">
                  <a href="javascript:;"
                     onclick="javascript:WishlistManage('block-order-detail', '{$wishlists[i].id_wishlist|intval}');">{$wishlists[i].name|truncate:30:'...'|escape:'html':'UTF-8'}</a>
                </td>
                <td class="bold align_center">
                  {assign var=n value=0}
                  {foreach from=$nbProducts item=nb name=i}
                    {if $nb.id_wishlist eq $wishlists[i].id_wishlist}
                      {assign var=n value=$nb.nbProducts|intval}
                    {/if}
                  {/foreach}
                  {if $n}
                    {$n|intval}
                  {else}
                    0
                  {/if}
                </td>
                <td>{$wishlists[i].counter|intval}</td>
                <td>{$wishlists[i].date_add|date_format:"%Y-%m-%d"}</td>
                <td><a href="javascript:;" onclick="javascript:WishlistManage('block-order-detail', '{$wishlists[i].id_wishlist|intval}');">{l s='View' d='Modules.BlockWistList.MyWistList'}</a></td>
                <td class="wishlist_default">
                  {if isset($wishlists[i].default) && $wishlists[i].default == 1}
                    <p class="is_wishlist_default">
                      <i class="icon icon-check-square"></i>
                    </p>
                  {else}
                    <a href="#" onclick="javascript:event.preventDefault();(WishlistDefault('wishlist_{$wishlists[i].id_wishlist|intval}', '{$wishlists[i].id_wishlist|intval}'));">
                      <i class="icon icon-square"></i>
                    </a>
                  {/if}
                </td>
                <td class="wishlist_delete">
                  <a href="javascript:;"
                     onclick="return (WishlistDelete('wishlist_{$wishlists[i].id_wishlist|intval}', '{$wishlists[i].id_wishlist|intval}', '{l s='Do you really want to delete this wishlist ?' d='Modules.BlockWistList.MyWistList' js=1}'));"
                     class="btn btn-danger"
                  >
                    <i class="material-icons">delete</i>{l s='Delete' d='Modules.BlockWistList.MyWistList'}
                  </a>
                </td>
              </tr>
            {/section}
          </tbody>
        </table>
      </div>
      <div id="block-order-detail">&nbsp;</div>
    {/if}
  {/if}

  {*<ul class="footer_links">*}
    {*<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon"/></a><a*}
              {*href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='Back to Your Account' mod='blockwishlist'}</a></li>*}
    {*<li class="f_right"><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon"/></a><a href="{$base_dir}">{l s='Home' mod='blockwishlist'}</a></li>*}
  {*</ul>*}
</div>
{/block}
