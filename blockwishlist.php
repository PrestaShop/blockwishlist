<?php
/*
* 2007-2020 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class BlockWishList extends Module
{
    public function __construct()
    {
        $this->name = 'blockwishlist';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Wishlist block');
        $this->description = $this->l('Adds a block containing the customer\'s wishlists.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        // $this->js_path = $this->_path . 'views/js/';
    }

    public function install()
    {
        // $database =  new PrestaShop\Module\BlockWishList\Database\Install($this);

        // if (!parent::install() ||
        //     !$this->registerHook('displayProductActions') ||
        //     !$this->registerHook('displayCustomerAccount') ||
        //     // !$this->registerHook('displayHeader') ||
        //     !$this->registerHook('displayAdminCustomers') ||
        //     !$this->registerHook('displayProductListFunctionalButtons')// ||
        //     // !$this->registerHook('displayTop')
        // )
        // {
        //     return false;
        // }

        /* This hook is optional */
        // $this->registerHook('displayMyAccountBlock');

        return parent::install();
    }

    public function uninstall()
    {
        //todo
        return parent::uninstall();
    }

    // public function hookDisplayProductActions()
    // {
    //     // Used to show the add to wishlist button near the add to cart button
    //     Media::addJsDef([
    //         'WishlistControllerURL' => $this->context->link->getLink('AdminAjaxPrestashopWishlist')
    //     ]);

    //     $this->context->controller->addJS([
    //         $this->js_path . 'hook/displayProductActions.js',
    //     ]);
    // }

    // public function hookDisplayCustomerAccount()
    // {
    //     // Used to show the wishlist in the customer account
    //     Media::addJsDef([
    //         'WishlistControllerURL' => $this->context->link->getLink('BlockWishlistFrontControllerModule')
    //     ]);

    //     $this->context->controller->addJS([
    //         $this->js_path . 'hook/displayCustomerAccount.js',
    //     ]);
    // }

    // public function hookDisplayAdminCustomers()
    // {
    //     Media::addJsDef([
    //         'AdminControllerURL' => $this->context->link->getAdminLink('AdminAjaxPrestashopWishlist')
    //     ]);

    //     $this->context->controller->addJS([
    //         $this->js_path . 'hook/adminCustomers.js',
    //     ]);

    // }

    // public function hookDisplayTop()
    // {
    // }

    // public function hookDisplayMyAccountBlock()
    // {
    // }

    // public function hookDisplayProductListFunctionalButtons()
    // {
    //     Media::addJsDef([
    //         'AdminControllerURL' => $this->context->link->getAdminLink('BlockWishlistFrontControllerModule')
    //     ]);

    //     $this->context->controller->addJS([
    //         $this->js_path . 'hook/displayProductListFunctionalButtons.js',
    //     ]);
    // }

    // public function hookDisplayHeader()
    // {
    // }

    public function getContent()
    {
        // Media::addJsDef([
        //     'AdminControllerURL' => $this->context->link->getAdminLink('AdminAjaxPrestashopWishlist')
        // ]);

        // $this->context->controller->addJS([
        //     $this->js_path . 'back.js',
        // ]);

        return $this->display(__FILE__, '/views/templates/admin/config.tpl');
    }
}
