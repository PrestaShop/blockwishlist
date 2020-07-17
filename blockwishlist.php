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

require_once __DIR__ . '/vendor/autoload.php';

class BlockWishList extends Module
{
    const HOOKS = [
        'actionAdminControllerSetMedia',
        'actionFrontControllerSetMedia',
        'displayProductActions',
        'displayCustomerAccount',
        'displayHeader',
        'displayAdminCustomers',
        'displayProductAdditionalInfo',
        'displayMyAccountBlock',
    ];

    const MODULE_ADMIN_CONTROLLERS = [
        [
            'class_name' => 'WishlistConfigurationAdminParentController',
            'visible' => false,
            'parent_class_name' => 'AdminModules',
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

    /**
     * @var bool
     */
    public $isPrestaShopVersionLessThan176;

    public function __construct()
    {
        $this->name = 'blockwishlist';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Wishlist');
        $this->description = $this->l('Adds a block containing the customer\'s wishlists.');
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];
        $this->isPrestaShopVersionLessThan176 = (bool) version_compare(_PS_VERSION_, '1.7.6', '<');
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
        $productsTagged = false;

        if (true === $this->context->customer->isLogged()) {
            $productsTagged = WishList::getAllProductByCustomer($this->context->customer->id);
        }

        Media::addJsDef([
            'blockwishlistController' => $this->context->link->getModuleLink(
                $this->name,
                'action'
            ),
            'removeFromWishlistUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
            'wishlistUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'view'),
            'wishlistAddProductToCartUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'addProductToCart']),
            'productsAlreadyTagged' => $productsTagged ? $productsTagged : [],
        ]);

        $this->context->controller->registerStylesheet(
            'blockwishlistController',
            'modules/' . $this->name . '/public/wishlist.css',
            [
              'media' => 'all',
              'priority' => 200,
            ]
        );

        $this->context->controller->registerJavascript(
            'blockwishlistController',
            'modules/' . $this->name . '/public/product.bundle.js',
            [
              'priority' => 200,
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
          'url' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/product/add-button.tpl');
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
            'url' => Context::getContext()->link->getModuleLink('blockwishlist', 'lists'),
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
            'isPrestaShopVersionLessThan176' => $this->isPrestaShopVersionLessThan176,
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
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/account/myaccount-block.tpl');
    }

    /**
     * This hook adds additional information on the product page
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductAdditionalInfo(array $params)
    {
        $this->smarty->assign([
            'blockwishlist' => $this->displayName,
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/product/additional-infos.tpl');
    }

    /**
     * This hook adds additional elements in the head section of your pages (head section of html)
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayHeader(array $params)
    {
        $this->smarty->assign([
            'context' => $this->context->controller->php_self,
            'url' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'getAllWishlist']),
            'createUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'createNewWishlist']),
            'deleteProductUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'deleteProductFromWishlist']),
            'addUrl' => Context::getContext()->link->getModuleLink('blockwishlist', 'action', ['action' => 'addProductToWishlist']),
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/displayHeader.tpl');
    }
}
