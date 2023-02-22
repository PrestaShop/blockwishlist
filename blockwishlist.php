<?php
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

use PrestaShop\Module\BlockWishList\Database\Install;
use PrestaShop\Module\BlockWishList\Database\Uninstall;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class BlockWishList extends Module
{
    const HOOKS = [
        'actionAdminControllerSetMedia',
        'actionFrontControllerSetMedia',
        'actionAttributeDelete',
        'actionProductDelete',
        'actionProductAttributeDelete',
        'deleteProductAttribute',
        'displayProductActions',
        'displayCustomerAccount',
        'displayFooter',
        'displayAdminCustomers',
        'displayMyAccountBlock',
    ];

    const MODULE_ADMIN_CONTROLLERS = [
        [
            'class_name' => 'WishlistConfigurationAdminParentController',
            'visible' => false,
            'parent_class_name' => 'AdminParentModulesSf',
            'name' => 'Wishlist Module',
        ],
        [
            'class_name' => 'WishlistConfigurationAdminController',
            'visible' => true,
            'parent_class_name' => 'WishlistConfigurationAdminParentController',
            'name' => 'Configuration',
        ],
        [
            'class_name' => 'WishlistStatisticsAdminController',
            'visible' => true,
            'parent_class_name' => 'WishlistConfigurationAdminParentController',
            'name' => 'Statistics',
        ],
    ];

    public function __construct()
    {
        $this->name = 'blockwishlist';
        $this->tab = 'front_office_features';
        $this->version = '3.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans('Wishlist', [], 'Modules.Blockwishlist.Admin');
        $this->description = $this->trans('Allow customers to create wishlists to save their favorite products for later.', [], 'Modules.Blockwishlist.Admin');
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (false === (new Install($this->getTranslator()))->run()) {
            return false;
        }

        return parent::install()
            && $this->registerHook(static::HOOKS);
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return (new Uninstall())->run()
            && parent::uninstall();
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            SymfonyContainer::getInstance()->get('router')->generate('blockwishlist_configuration')
        );
    }

    /**
     * Add asset for Administration
     *
     * @param array $params
     */
    public function hookActionAdminControllerSetMedia(array $params)
    {
        $this->context->controller->addCss($this->getPathUri() . 'public/backoffice.css');

        $this->context->controller->addJs($this->getPathUri() . 'public/vendors.js');
    }

    /**
     * Add asset for Shop Front Office
     *
     * @see https://devdocs.prestashop.com/1.7/themes/getting-started/asset-management/#without-a-front-controller-module
     *
     * @param array $params
     */
    public function hookActionFrontControllerSetMedia(array $params)
    {
        $productsTagged = true === $this->context->customer->isLogged()
            ? WishList::getAllProductByCustomer($this->context->customer->id, $this->context->shop->id)
            : false;

        Media::addJsDef([
            'blockwishlistController' => $this->context->link->getModuleLink(
                $this->name,
                'action'
            ),
            'removeFromWishlistUrl' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
            'wishlistUrl' => $this->context->link->getModuleLink('blockwishlist', 'view'),
            'wishlistAddProductToCartUrl' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'addProductToCart']),
            'productsAlreadyTagged' => $productsTagged ?: [],
        ]);

        $this->context->controller->registerStylesheet(
            'blockwishlistController',
            'modules/' . $this->name . '/public/wishlist.css',
            [
              'media' => 'all',
              'priority' => 100,
            ]
        );

        $this->context->controller->registerJavascript(
            'blockwishlistController',
            'modules/' . $this->name . '/public/product.bundle.js',
            [
              'priority' => 100,
            ]
        );

        $this->context->controller->registerJavascript(
            'blockwishlistGraphql',
            'modules/' . $this->name . '/public/graphql.js',
            [
              'priority' => 190,
            ]
        );

        $this->context->controller->registerJavascript(
            'blockwishlistVendors',
            'modules/' . $this->name . '/public/vendors.js',
            [
              'priority' => 190,
            ]
        );
    }

    /**
     * This hook allow additional action button, near the add to cart button on the product page
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductActions(array $params)
    {
        $this->smarty->assign([
          'blockwishlist' => $this->displayName,
          'url' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/product/add-button.tpl');
    }

    public function hookActionProductDelete(array $params)
    {
        if (!isset($params['id_product'])) {
            return;
        }

        WishList::removeProductFromWishlist($params['id_product']);
        Statistics::removeProductFromStatistics($params['id_product']);
    }

    public function hookActionProductAttributeDelete(array $params)
    {
        if (!isset($params['id_product']) || !isset($params['id_product_attribute'])) {
            return;
        }

        // Remove all attributes from a product
        if (!empty($params['deleteAllAttributes'])) {
            $this->hookActionProductDelete($params);

            return;
        }

        WishList::removeProductFromWishlist($params['id_product'], $params['id_product_attribute']);
        Statistics::removeProductFromStatistics($params['id_product'], $params['id_product_attribute']);
    }

    public function hookActionAttributeDelete(array $params)
    {
        if (!isset($params['id_attribute'])) {
            return;
        }

        WishList::removeProductFromWishlist(null, $params['id_product_attribute']);
        Statistics::removeProductFromStatistics(null, $params['id_product_attribute']);
    }

    public function hookDeleteProductAttribute(array $params)
    {
        $this->hookActionProductAttributeDelete($params);
    }

    /**
     * This hook displays new elements on the customer account page
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayCustomerAccount(array $params)
    {
        $this->smarty->assign([
            'url' => $this->context->link->getModuleLink('blockwishlist', 'lists'),
            'wishlistsTitlePage' => Configuration::get('blockwishlist_WishlistPageName', $this->context->language->id),
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/displayCustomerAccount.tpl');
    }

    /**
     * This hook displays a new block on the admin customer page
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminCustomers(array $params)
    {
        $this->smarty->assign([
            'blockwishlist' => $this->displayName,
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/displayAdminCustomers.tpl');
    }

    /**
     * Display additional information inside the "my account" block
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayMyAccountBlock(array $params)
    {
        $this->smarty->assign([
            'blockwishlist' => $this->displayName,
            'url' => $this->context->link->getModuleLink('blockwishlist', 'lists'),
            'wishlistsTitlePage' => Configuration::get('blockwishlist_WishlistPageName', $this->context->language->id),
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/account/myaccount-block.tpl');
    }

    /**
     * This hook adds additional elements in the footer section of your pages
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayFooter(array $params)
    {
        $this->smarty->assign([
            'context' => $this->context->controller->php_self,
            'url' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'getAllWishlist']),
            'deleteListUrl' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteWishlist']),
            'createUrl' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'createNewWishlist']),
            'deleteProductUrl' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
            'addUrl' => $this->context->link->getModuleLink('blockwishlist', 'action', ['action' => 'addProductToWishlist']),
            'newWishlistCTA' => Configuration::get('blockwishlist_CreateButtonLabel', $this->context->language->id),
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/displayHeader.tpl');
    }
}
