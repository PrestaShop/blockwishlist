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
 * Replace an element by its new version, rendered by the current page
 *
 * @param {String} selector
 */
export default async function refresh(selector) {
  const element = document.querySelector(selector);

  if (!element) {
    return;
  }

  const response = await fetch(window.location.href, {
    headers: {Accept: 'text/html'},
  });
  const page = new DOMParser().parseFromString(await response.text(), 'text/html');
  const newElement = page.querySelector(selector);

  if (newElement) {
    element.replaceWith(newElement);
  }
}
