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

namespace PrestaShop\Module\BlockWishList\Controller;

use Configuration;
use Doctrine\Common\Cache\CacheProvider;
use Language;
use PrestaShop\Module\BlockWishList\Grid\Data\BaseGridDataFactory;
use PrestaShop\Module\BlockWishList\Type\ConfigurationType;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WishlistConfigurationAdminController extends FrameworkBundleAdminController
{
    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var int|null
     */
    private $shopId;

    public function __construct(CacheProvider $cache, $shopId)
    {
        $this->cache = $cache;
        $this->shopId = $shopId;
    }

    public function configurationAction(Request $request)
    {
        $datas = $this->getWishlistConfigurationDatas();
        $configurationForm = $this->createForm(ConfigurationType::class, $datas);
        $configurationForm->handleRequest($request);
        $resultHandleForm = null;

        if ($configurationForm->isSubmitted() && $configurationForm->isValid()) {
            $resultHandleForm = $this->handleForm($configurationForm->getData());
        }

        return $this->render('@Modules/blockwishlist/views/templates/admin/home.html.twig', [
            'configurationForm' => $configurationForm->createView(),
            'resultHandleForm' => $resultHandleForm,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('WishlistConfigurationAdminController'),
        ]);
    }

    public function statisticsAction()
    {
        $searchCriteria = new SearchCriteria();
        $allTimeStatsGridFactory = $this->get('prestashop.module.blockwishlist.grid.all_time_stastistics_grid_factory');
        $currentYearGridFactory = $this->get('prestashop.module.blockwishlist.grid.current_year_stastistics_grid_factory');
        $currentMonthGridFactory = $this->get('prestashop.module.blockwishlist.grid.current_month_stastistics_grid_factory');
        $currentDayGridFactory = $this->get('prestashop.module.blockwishlist.grid.current_day_stastistics_grid_factory');
        $allTimeStatisticsGrid = $allTimeStatsGridFactory->getGrid($searchCriteria);
        $currentYearGrid = $currentYearGridFactory->getGrid($searchCriteria);
        $currentMonthGrid = $currentMonthGridFactory->getGrid($searchCriteria);
        $currentDayGrid = $currentDayGridFactory->getGrid($searchCriteria);

        return $this->render('@Modules/blockwishlist/views/templates/admin/statistics.html.twig', [
            'allTimeStatisticsGrid' => $this->presentGrid($allTimeStatisticsGrid),
            'currentYearStatisticsGrid' => $this->presentGrid($currentYearGrid),
            'currentMonthStatisticsGrid' => $this->presentGrid($currentMonthGrid),
            'currentDayStatisticsGrid' => $this->presentGrid($currentDayGrid),
            'shopId' => $this->shopId,
        ]);
    }

    public function resetStatisticsCacheAction()
    {
        $result = $this->cache->delete(BaseGridDataFactory::CACHE_KEY_STATS_ALL_TIME . $this->shopId)
            && $this->cache->delete(BaseGridDataFactory::CACHE_KEY_STATS_CURRENT_DAY . $this->shopId)
            && $this->cache->delete(BaseGridDataFactory::CACHE_KEY_STATS_CURRENT_MONTH . $this->shopId)
            && $this->cache->delete(BaseGridDataFactory::CACHE_KEY_STATS_CURRENT_YEAR . $this->shopId);

        return new JsonResponse(['success' => $result]);
    }

    /**
     * handleForm
     *
     * @param array $datas
     *
     * @return bool
     */
    private function handleForm($datas)
    {
        $result = true;
        if (isset($datas['WishlistPageName'])) {
            foreach ($datas['WishlistPageName'] as $langID => $value) {
                $result &= Configuration::updateValue('blockwishlist_WishlistPageName', [$langID => $value]);
            }
        }

        if (isset($datas['WishlistDefaultTitle'])) {
            foreach ($datas['WishlistDefaultTitle'] as $langID => $value) {
                $result &= Configuration::updateValue('blockwishlist_WishlistDefaultTitle', [$langID => $value]);
            }
        }

        if (isset($datas['CreateButtonLabel'])) {
            foreach ($datas['CreateButtonLabel'] as $langID => $value) {
                $result &= Configuration::updateValue('blockwishlist_CreateButtonLabel', [$langID => $value]);
            }
        }

        return $result;
    }

    /**
     * getWishlistConfigurationDatas
     *
     * @return array
     */
    private function getWishlistConfigurationDatas()
    {
        $languages = Language::getLanguages(true);
        $wishlistNames = $wishlistDefaultTitles = $wishlistCreateNewButtonsLabel = [];

        foreach ($languages as $lang) {
            $wishlistNames[$lang['id_lang']] = Configuration::get('blockwishlist_WishlistPageName', $lang['id_lang']);
            $wishlistDefaultTitles[$lang['id_lang']] = Configuration::get('blockwishlist_WishlistDefaultTitle', $lang['id_lang']);
            $wishlistCreateNewButtonsLabel[$lang['id_lang']] = Configuration::get('blockwishlist_CreateButtonLabel', $lang['id_lang']);
        }

        $datas = [
            'WishlistPageName' => $wishlistNames,
            'WishlistDefaultTitle' => $wishlistDefaultTitles,
            'CreateButtonLabel' => $wishlistCreateNewButtonsLabel,
        ];

        return $datas;
    }
}
