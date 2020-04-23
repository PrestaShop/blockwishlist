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
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class BlockWishList extends Module implements WidgetInterface
{
    const HOOKS = [
        'actionAdminControllerSetMedia',
        'actionFrontControllerSetMedia',
        'displayProductActions',
        'displayCustomerAccount',
        'displayHeader',
        'displayAdminCustomers',
        'displayProductAdditionalInfo',
        'displayTop',
        'displayMyAccountBlock',
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

        $this->displayName = $this->l('Wishlist block');
        $this->description = $this->l('Adds a block containing the customer\'s wishlists.');
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->isPrestaShopVersionLessThan176 = (bool) version_compare(_PS_VERSION_, '1.7.6', '<');
    }

    /**
     * @return bool
     */
    public function install()
    {
        $isDatabaseInstalled = new Install($this);

        if (false === $isDatabaseInstalled->installTables()) {
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
        return parent::uninstall();
    }

    /**
     * Add asset for Administration
     *
     * @param array $params
     */
    public function hookActionAdminControllerSetMedia(array $params)
    {
        if ($this->context->controller->controller_name === 'AdminCustomers') {
            $this->context->controller->addJs($this->getPathUri() . 'views/js/admin/displayAdminCustomers.js?v=' . $this->version);
        }
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
        Media::addJsDef([
            'blockwishlistController' => $this->context->link->getModuleLink(
                $this->name,
                'action'
            ),
        ]);

        $this->context->controller->registerStylesheet(
            'blockwishlistController',
            'modules/'.$this->name.'/public/wishlist.css',
            [
              'media' => 'all',
              'priority' => 200,
            ]
        );

        $this->context->controller->registerJavascript(
            'blockwishlistController',
            'modules/'.$this->name.'/public/product.bundle.js',
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
          'product' => array(
            'id' => 1,
            'id_wishlist' => 1
          ),
          'url' => 'http://dumburl.com/'
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
     * This hook displays additional elements at the top of your pages
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayTop(array $params)
    {
        $this->smarty->assign([
            'blockwishlist' => $this->displayName,
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/displayTop.tpl');
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
            'context' => $this->context->controller->php_self
        ]);

        return $this->fetch('module:blockwishlist/views/templates/hook/displayHeader.tpl');
    }

    /**
     * This is used to render Widget introduced in PrestaShop 1.7
     *
     * @param string $hookName
     * @param array $configuration
     *
     * @return string
     */
    public function renderWidget($hookName, array $configuration)
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:blockwishlist/views/templates/widget/blockwishlist.tpl');
    }

    /**
     * This is used to get Smarty variables for Widget introduced in PrestaShop 1.7
     *
     * @see https://devdocs.prestashop.com/1.7/modules/concepts/widgets/
     *
     * @param string $hookName
     * @param array $configuration
     *
     * @return array
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        return [
            'blockwishlist' => $this->displayName,
        ];
    }
}
