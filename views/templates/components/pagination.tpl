{**
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
 *}
<nav class="wishlist-pagination pagination">
  <template v-if="display">
    <div class="col-md-4">
      {l s='Showing %min% - %max% of %total% item(s)' sprintf=['%min%' => '((minShown))', '%max%' => '((maxShown))', '%total%' => '((total))'] d='Modules.Blockwishlist.Shop'}
    </div>

    <div class="col-md-6 offset-md-2 pr-0">
      <ul class="page-list clearfix text-sm-center">
        <li :class="{literal}{current: page.current}{/literal}" v-for="page of pages">
          <a class="js-search-link" @click="paginate(page)" key="page.page" :class="{literal}{disabled: page.current, next: page.type === 'next', previous: page.type === 'previous'}{/literal}">
            <span v-if="page.type === 'previous'">
              <i class="material-icons">keyboard_arrow_left</i> {l s='Previous' d='Modules.Blockwishlist.Shop'} 
            </span>

            <template v-if="page.type !== 'previous' && page.type !== 'next'">
              ((page.page))
            </template>

            <span v-if="page.type === 'next'">
              {l s='Next' d='Modules.Blockwishlist.Shop'} <i class="material-icons"></i>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </template>
</nav>
