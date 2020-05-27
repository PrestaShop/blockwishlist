/**
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
 */
import Vue from 'vue';
import VueApollo from 'vue-apollo';
import apolloClient from '@graphqlFiles/client';

/**
 * Init a VueJS application to keep monolith features such as hooks or event the use of twig/smarty
 *
 * @param {Vue} component The component to be init
 * @param {String} componentSelector A selector for querySelectorAll
 * @param {Array[Object]} props An array containing Object{name, type} to parse int
 */
export default function initApp(component, componentSelector, props) {
  Vue.use(VueApollo);

  const apolloProvider = new VueApollo({
    defaultClient: apolloClient
  });

  const componentElements = document.querySelectorAll(componentSelector);
  const ComponentRoot = Vue.extend(component);

  let propsData = {};

  componentElements.forEach(e => {
    for (const prop of props) {
      if (e.dataset[prop.name]) {
        propsData[prop.name] =
          prop.type === Number
            ? parseInt(e.dataset[prop.name])
            : prop.type === Boolean
            ? e.dataset[prop.name] === 'true'
            : e.dataset[prop.name];
      }
    }

    new ComponentRoot({
      el: e,
      delimiters: ['((', '))'],
      apolloProvider,
      propsData
    });
  });
}
