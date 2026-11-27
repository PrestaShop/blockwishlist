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
 * Render a Smarty template of the module the simple way: the translations are replaced by their
 * source text and the other tags are dropped, then the markup is injected into the document
 *
 * @param {String} name The file path in views/templates, without extension
 * @param {Object} variables The values of the variables used by the template
 *
 * @returns {HTMLElement} The first element of the template
 */
export default function loadTemplate(name, variables = {}) {
  /* eslint-disable no-undef */
  const fs = __non_webpack_require__('fs');
  const path = __non_webpack_require__('path');
  /* eslint-enable no-undef */
  const source = fs.readFileSync(path.resolve(`views/templates/${name}.tpl`), 'utf8');
  const html = source
    .replace(/\{\*[\s\S]*?\*\}/g, '')
    .replace(/\{l s='([^']*)'[^}]*\}/g, '$1')
    .replace(/\{\$([\w.]+)\}/g, (match, variable) => variables[variable] || '')
    .replace(/\{[^}]*\}/g, '');

  const container = document.createElement('div');
  container.innerHTML = html;
  const element = container.firstElementChild;
  document.body.append(...container.childNodes);

  return element;
}
