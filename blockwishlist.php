<?php
/**
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
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php';

/**
 * Class BlockWishlist
 */
class BlockWishlist extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    private $html = '';

    /**
     * BlockWishlist constructor.
     *
     * @throws PrestaShopDatabaseException
     */
    public function __construct()
    {
        $this->name = 'blockwishlist';
        $this->tab = 'front_office_features';
        $this->version = '1.3.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->controllers = ['buywishlistproduct', 'cart', 'managewishlist', 'mywishlist', 'sendwishlist', 'view'];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Wishlist block', [], 'Modules.BlockWishlist.Admin');
        $this->description = $this->trans('Adds a block containing the customer\'s wishlists.', [], 'Modules.BlockWishlist.Admin');
        $this->default_wishlist_name = $this->trans('My wishlist', [], 'Modules.BlockWishlist.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
        $this->html = '';
    }

    /**
     * @param bool $deleteParams
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install($deleteParams = true)
    {
        if ($deleteParams) {
            if (!file_exists(dirname(__FILE__).'/'.static::INSTALL_SQL_FILE)) {
                return (false);
            } else {
                if (!$sql = file_get_contents(dirname(__FILE__).'/'.static::INSTALL_SQL_FILE)) {
                    return (false);
                }
            }
            $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);
            $sql = preg_split("/;\s*[\r\n]+/", $sql);
            foreach ($sql as $query) {
                if ($query) {
                    if (!Db::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }

        if (!parent::install() ||
            !$this->registerHook('rightColumn') ||
            !$this->registerHook('productActions') ||
            !$this->registerHook('cart') ||
            !$this->registerHook('customerAccount') ||
            !$this->registerHook('header') ||
            !$this->registerHook('adminCustomers') ||
            !$this->registerHook('displayProductListFunctionalButtons') ||
            !$this->registerHook('top')) {
            return false;
        }
        /* This hook is optional */
        $this->registerHook('displayMyAccountBlock');

        return true;
    }

    /**
     * Uninstall this module
     *
     * @param bool $deleteParams
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @since 1.0.0
     */
    public function uninstall($deleteParams = true)
    {
        if (($deleteParams && !$this->deleteTables()) || !parent::uninstall()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @since 1.0.0
     */
    protected function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'wishlist`,
			`'._DB_PREFIX_.'wishlist_email`,
			`'._DB_PREFIX_.'wishlist_product`,
			`'._DB_PREFIX_.'wishlist_product_cart`'
        );
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @since 1.0.0
     */
    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    /**
     * Configuration page
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @since 1.0.0
     */
    public function getContent()
    {
        if (Tools::isSubmit('viewblockwishlist') && $id = Tools::getValue('id_product')) {
            Tools::redirect($this->context->link->getProductLink($id));
        } elseif (Tools::isSubmit('submitSettings')) {
            $activated = Tools::getValue('activated');
            if ($activated != 0 && $activated != 1) {
                $this->html .= '<div class="alert error alert-danger">'.$this->trans('Activate module : Invalid choice.', [], 'Modules.BlockWishlist.Admin').'</div>';
            }
            $this->html .= '<div class="conf confirm alert alert-success">'.$this->trans('Settings updated', [], 'Modules.BlockWishlist.Admin').'</div>';
        }

        $this->html .= $this->renderJS();
        $this->html .= $this->renderForm();
        if (Tools::getValue('id_customer') && Tools::getValue('id_wishlist')) {
            $this->html .= $this->renderList((int) Tools::getValue('id_wishlist'));
        }

        return $this->html;
    }

    /**
     * @param array $params
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     *
     * @since 1.0.0
     */
    public function hookDisplayProductListFunctionalButtons($params)
    {
        //TODO : Add cache
        if ($this->context->customer->isLogged()) {
            $this->smarty->assign('wishlists', Wishlist::getByIdCustomer($this->context->customer->id));
        }

        $this->smarty->assign('product', $params['product']);

        return $this->display(__FILE__, 'blockwishlist_button.tpl');
    }

    /**
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     *
     * @since 1.0.0
     */
    public function hookDisplayTop()
    {
        if ($this->context->customer->isLogged()) {
            $wishlists = Wishlist::getByIdCustomer($this->context->customer->id);
            if (empty($this->context->cookie->id_wishlist) === true ||
                Wishlist::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
                if (!count($wishlists)) {
                    $idWishlist = false;
                } else {
                    $idWishlist = (int) $wishlists[0]['id_wishlist'];
                    $this->context->cookie->id_wishlist = (int) $idWishlist;
                }
            } else {
                $idWishlist = $this->context->cookie->id_wishlist;
            }

            $this->smarty->assign(
                [
                    'id_wishlist'       => $idWishlist,
                    'isLogged'          => true,
                    'wishlist_products' => ($idWishlist == false ? false : Wishlist::getProductByIdCustomer($idWishlist,
                        $this->context->customer->id, $this->context->language->id, null, true)),
                    'wishlists'         => $wishlists,
                    'ptoken'            => Tools::getToken(false),
                ]
            );
        } else {
            $this->smarty->assign(['wishlist_products' => false, 'wishlists' => false]);
        }

        return $this->display(__FILE__, 'blockwishlist_top.tpl');
    }

    /**
     * @throws PrestaShopException
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/ajax-wishlist.js');

        $this->smarty->assign(['wishlist_link' => $this->context->link->getModuleLink('blockwishlist', 'mywishlist')]);
    }

    /**
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function hookRightColumn()
    {
        if ($this->context->customer->isLogged()) {
            $wishlists = Wishlist::getByIdCustomer($this->context->customer->id);
            if (empty($this->context->cookie->id_wishlist) === true ||
                Wishlist::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
                if (!count($wishlists)) {
                    $id_wishlist = false;
                } else {
                    $id_wishlist = (int) $wishlists[0]['id_wishlist'];
                    $this->context->cookie->id_wishlist = (int) $id_wishlist;
                }
            } else {
                $id_wishlist = $this->context->cookie->id_wishlist;
            }
            $this->smarty->assign(
                [
                    'id_wishlist'       => $id_wishlist,
                    'isLogged'          => true,
                    'wishlist_products' => ($id_wishlist == false ? false : Wishlist::getProductByIdCustomer($id_wishlist,
                        $this->context->customer->id, $this->context->language->id, null, true)),
                    'wishlists'         => $wishlists,
                    'ptoken'            => Tools::getToken(false),
                ]
            );
        } else {
            $this->smarty->assign(['wishlist_products' => false, 'wishlists' => false]);
        }

        return ($this->display(__FILE__, 'blockwishlist.tpl'));
    }

    /**
     * @param $params
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function hookLeftColumn($params)
    {
        return $this->hookRightColumn($params);
    }

    /**
     * @param $params
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function hookProductActions($params)
    {
        $cookie = $params['cookie'];

        $this->smarty->assign([
            'id_product' => (int) Tools::getValue('id_product'),
        ]);

        if (isset($cookie->id_customer)) {
            $this->smarty->assign([
                'wishlists' => Wishlist::getByIdCustomer($cookie->id_customer),
            ]);
        }

        return ($this->display(__FILE__, 'blockwishlist-extra.tpl'));
    }

    /**
     * @param $params
     *
     * @return string
     * @throws SmartyException
     */
    public function hookCustomerAccount()
    {
        return $this->display(__FILE__, 'my-account.tpl');
    }

    /**
     * @param $params
     *
     * @return string
     * @throws SmartyException
     */
    public function hookDisplayMyAccountBlock($params)
    {
        return $this->hookCustomerAccount($params);
    }

    /**
     * @param int $idWishlist
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @since 1.0.0
     */
    protected function _displayProducts($idWishlist)
    {
        $wishlist = new Wishlist($idWishlist);
        $products = Wishlist::getProductByIdCustomer($idWishlist, $wishlist->id_customer, $this->context->language->id);
        $nbProducts = count($products);
        for ($i = 0; $i < $nbProducts; ++$i) {
            $obj = new Product((int) $products[$i]['id_product'], false, $this->context->language->id);
            if (!Validate::isLoadedObject($obj)) {
                continue;
            } else {
                $images = $obj->getImages($this->context->language->id);
                foreach ($images as $image) {
                    if ($image['cover']) {
                        $products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
                        break;
                    }
                }
                if (!isset($products[$i]['cover'])) {
                    $products[$i]['cover'] = $this->context->language->iso_code.'-default';
                }
            }
        }
        $this->html .= '
		<table class="table">
			<thead>
				<tr>
					<th class="first_item" style="width:600px;">'.$this->trans('Product', [], 'Modules.BlockWishlist.Admin').'</th>
					<th class="item" style="text-align:center;width:150px;">'.$this->trans('Quantity', [], 'Modules.BlockWishlist.Admin').'</th>
					<th class="item" style="text-align:center;width:150px;">'.$this->trans('Priority', [], 'Modules.BlockWishlist.Admin').'</th>
				</tr>
			</thead>
			<tbody>';
        $priority = [$this->trans('High', [], 'Modules.BlockWishlist.Admin'), $this->trans('Medium', [], 'Modules.BlockWishlist.Admin'), $this->trans('Low', [], 'Modules.BlockWishlist.Admin')];
        foreach ($products as $product) {
            $this->html .= '
				<tr>
					<td class="first_item">
						<img src="'.$this->context->link->getImageLink($product['link_rewrite'], $product['cover'],
                    ImageType::getFormatedName('small')).'" alt="'.htmlentities($product['name'], ENT_COMPAT, 'UTF-8').'" style="float:left;" />
						'.$product['name'];
            if (isset($product['attributes_small'])) {
                $this->html .= '<br /><i>'.htmlentities($product['attributes_small'], ENT_COMPAT, 'UTF-8').'</i>';
            }
            $this->html .= '
					</td>
					<td class="item" style="text-align:center;">'.(int) $product['quantity'].'</td>
					<td class="item" style="text-align:center;">'.$priority[(int) $product['priority'] % 3].'</td>
				</tr>';
        }
        $this->html .= '</tbody></table>';
    }

    /**
     * @param array $params
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @since 1.0.0
     */
    public function hookAdminCustomers($params)
    {
        $customer = new Customer((int) $params['id_customer']);
        if (!Validate::isLoadedObject($customer)) {
            die (Tools::displayError());
        }

        $this->html = '<h2>'.$this->trans('Wishlists', [], 'Modules.BlockWishlist.Admin').'</h2>';

        $wishlists = Wishlist::getByIdCustomer((int) $customer->id);
        if (!count($wishlists)) {
            $this->html .= $customer->lastname.' '.$customer->firstname.' '.$this->trans('No wishlist.', [], 'Modules.BlockWishlist.Admin');
        } else {
            $this->html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="listing">';

            $idWishlist = (int) Tools::getValue('id_wishlist');
            if (!$idWishlist) {
                $idWishlist = $wishlists[0]['id_wishlist'];
            }

            $this->html .= '<span>'.$this->trans('Wishlist', [], 'Modules.BlockWishlist.Admin').': </span> <select name="id_wishlist" onchange="$(\'#listing\').submit();">';

            if (is_array($wishlists)) {
                foreach ($wishlists as $wishlist) {
                    $this->html .= '<option value="'.(int) $wishlist['id_wishlist'].'"';
                    if ($wishlist['id_wishlist'] == $idWishlist) {
                        $this->html .= ' selected="selected"';
                        $counter = $wishlist['counter'];
                    }
                    $this->html .= '>'.htmlentities($wishlist['name'], ENT_COMPAT, 'UTF-8').'</option>';
                }
            }
            $this->html .= '</select>';

            $this->_displayProducts((int) $idWishlist);

            $this->html .= '</form><br />';

            return $this->html;
        }

    }

    /**
     * Display Error from controler
     */
    public function errorLogged()
    {
        return $this->trans('You must be logged in to manage your wishlists.', [], 'Modules.BlockWishlist.Admin');
    }

    /**
     * @return string
     */
    public function renderJS()
    {
        return "<script>
			$(document).ready(function () { $('#id_customer, #id_wishlist').change( function () { $('#module_form').submit();}); });
		</script>";
    }

    /**
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function renderForm()
    {
        $customers = [];
        foreach (Wishlist::getCustomers() as $c) {
            $customers[$c['id_customer']]['id_customer'] = $c['id_customer'];
            $customers[$c['id_customer']]['name'] = $c['firstname'].' '.$c['lastname'];
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Listing', [], 'Modules.BlockWishlist.Admin'),
                    'icon'  => 'icon-cogs',
                ],
                'input'  => [
                    [
                        'type'    => 'select',
                        'label'   => $this->trans('Customers :', [], 'Modules.BlockWishlist.Admin'),
                        'name'    => 'id_customer',
                        'options' => [
                            'default' => ['value' => 0, 'label' => $this->trans('Choose customer', [], 'Modules.BlockWishlist.Admin')],
                            'query'   => $customers,
                            'id'      => 'id_customer',
                            'name'    => 'name',
                        ],
                    ],
                ],
            ],
        ];

        if ($id_customer = Tools::getValue('id_customer')) {
            $wishlists = Wishlist::getByIdCustomer($id_customer);
            $fields_form['form']['input'][] = [
                'type'    => 'select',
                'label'   => $this->trans('Wishlist :', [], 'Modules.BlockWishlist.Admin'),
                'name'    => 'id_wishlist',
                'options' => [
                    'default' => ['value' => 0, 'label' => $this->trans('Choose wishlist', [], 'Modules.BlockWishlist.Admin')],
                    'query'   => $wishlists,
                    'id'      => 'id_wishlist',
                    'name'    => 'name',
                ],
            ];
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name
            .'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'id_customer' => Tools::getValue('id_customer'),
            'id_wishlist' => Tools::getValue('id_wishlist'),
        ];
    }

    public function renderList($id_wishlist)
    {
        $wishlist = new Wishlist($id_wishlist);
        $products = Wishlist::getProductByIdCustomer($id_wishlist, $wishlist->id_customer, $this->context->language->id);

        foreach ($products as $key => $val) {
            $image = Image::getCover($val['id_product']);
            $products[$key]['image'] = $this->context->link->getImageLink($val['link_rewrite'], $image['id_image'], ImageType::getFormatedName('small'));
        }

        $fieldsList = [
            'image'            => [
                'title' => $this->trans('Image', [], 'Modules.BlockWishlist.Admin'),
                'type'  => 'image',
            ],
            'name'             => [
                'title' => $this->trans('Product', [], 'Modules.BlockWishlist.Admin'),
                'type'  => 'text',
            ],
            'attributes_small' => [
                'title' => $this->trans('Combination', [], 'Modules.BlockWishlist.Admin'),
                'type'  => 'text',
            ],
            'quantity'         => [
                'title' => $this->trans('Quantity', [], 'Modules.BlockWishlist.Admin'),
                'type'  => 'text',
            ],
            'priority'         => [
                'title'  => $this->trans('Priority', [], 'Modules.BlockWishlist.Admin'),
                'type'   => 'priority',
                'values' => [
                    $this->trans('High', [], 'Modules.BlockWishlist.Admin'),
                    $this->trans('Medium', [], 'Modules.BlockWishlist.Admin'),
                    $this->trans('Low', [], 'Modules.BlockWishlist.Admin'),
                ],
            ],
        ];

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->no_link = true;
        $helper->actions = ['view'];
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->identifier = 'id_product';
        $helper->title = $this->trans('Product list', [], 'Modules.BlockWishlist.Admin');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->tpl_vars = [
            'priority' => [
                $this->trans('High', [], 'Modules.BlockWishlist.Admin'),
                $this->trans('Medium', [], 'Modules.BlockWishlist.Admin'),
                $this->trans('Low', [], 'Modules.BlockWishlist.Admin'),
            ]];

        return $helper->generateList($products, $fieldsList);
    }
}
