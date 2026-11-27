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
import initAddToWishlist from './components/add-to-wishlist';
import initHearts from './components/button';
import initCreate from './components/create';
import initDelete from './components/delete';
import initLogin from './components/login';
import initRename from './components/rename';
import initShare from './components/share';
import initToast from './components/toast';
import initListsPage from './pages/lists';
import initProductsListPage from './pages/products-list';
import selectors from './lib/selectors';

const components = {
  [selectors.components.addToWishlist]: initAddToWishlist,
  [selectors.components.create]: initCreate,
  [selectors.components.delete]: initDelete,
  [selectors.components.login]: initLogin,
  [selectors.components.rename]: initRename,
  [selectors.components.share]: initShare,
  [selectors.components.toast]: initToast,
  [selectors.components.lists]: initListsPage,
};

Object.entries(components).forEach(([selector, init]) => {
  const element = document.querySelector(selector);

  if (element) {
    init(element);
  }
});

if (document.querySelector(selectors.components.products)) {
  initProductsListPage();
}

initHearts();
