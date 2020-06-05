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

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminAjaxPrestashopWishlistController extends FrameworkBundleAdminController
{
    const CACHE_LIFETIME_SECONDS = 86400;
    const YEAR_CACHE_LIFETIME_SECONDS = 86400;
    const MONTH_CACHE_LIFETIME_SECONDS = 86400;
    const DAY_CACHE_LIFETIME_SECONDS = 86400;

    /* @var CacheProvider $cache */
    private $cache;

    /* @var LegacyContext $cache */
    private $context;

    public function __construct(CacheProvider $cache, LegacyContext $context)
    {
        $this->cache = $cache;
        $this->context = $context->getContext();
    }

    public function homeAction(Request $request)
    {
        return $this->render('@Modules/blockwishlist/views/templates/admin/home.html.twig');
    }

    public function setWishlistConfigurationAction(Request $request)
    {
        // $key must be ID of lang so json for wishlistPageName should look like:
        // {"wishlistPageName": {"1":"wishlistPageNameFR", "1":"wishlistPageNameFR"} }

        if (isset($params['wishlistPageName'])) {
            $wishlistNames = json_decode($params['wishlistPageName'], true);
            foreach ($wishlistNames as $langID => $value) {
                Configuration::udpateValue('blockwishlist_wishlistPageName',[$langID => $value]);
            }
        }

        if (isset($params['wishlistDefaultTitle'])) {
            $wishlistDefaultTitle = json_decode($params['wishlistDefaultTitle'], true);
            foreach ($wishlistDefaultTitle as $langID => $value) {
                Configuration::udpateValue('blockwishlist_wishlistDefaultTitle',[$langID => $value]);
            }
        }

        if (isset($params['createNewButtonLabel'])) {
            $createNewButtons = json_decode($params['createNewButton'], true);
            foreach ($createNewButtons as $langID => $value) {
                Configuration::udpateValue('blockwishlist_createNewButtonLabel',[$langID => $value]);
            }
        }
    }

    public function getWishlistConfigurationAction(Request $request)
    {
        $languages = Language::getLanguages(true);

        foreach ($languages as $lang) {
            $wishlistNames[$lang['id_lang']] = Configuration::get('blockwishlist_wishlistPageName', $lang['id_lang']);
            $wishlistDefaultTitles[$lang['id_lang']] = Configuration::get('blockwishlist_wishlistDefaultTitle', $lang['id_lang']);
            $wishlistCreateNewButtonsLabel[$lang['id_lang']] = Configuration::get('blockwishlist_createNewButtonLabel', $lang['id_lang']);
        }

        $datas = [
            'wishlistNames' => $wishlistNames,
            'wishlistDefaultTitles' => $wishlistDefaultTitles,
            'wishlistCreateNewButtonsLabel' => $wishlistCreateNewButtonsLabel
        ];

        return json_encode($datas);
    }

    public function getStatisticsAction(Request $request)
    {
        if ($this->cache->contains('blockwishlist.cachetest')) {
            $results = $this->cache->fetch('blockwishlist.cachetest');
        } else {
            $results = (new StatisticsCalculator($this->context))->computeAllStats();
            $this->cache->save('blockwishlist.stats.allTime', $results['allTime'], self::CACHE_LIFETIME_SECONDS);
            $this->cache->save('blockwishlist.stats.currentYear', $results['currentYear'], self::CACHE_LIFETIME_SECONDS);
            $this->cache->save('blockwishlist.stats.currentMonth', $results['currentMonth'], self::CACHE_LIFETIME_SECONDS);
            $this->cache->save('blockwishlist.stats.currentDay', $results['currentDay'], self::CACHE_LIFETIME_SECONDS);
        }

        return $this->json([
            'success' => true,
            'stats' => $results
        ]);
    }

    public function forceRefreshCacheStatsAction(Request $request)
    {
        $cacheName = $request->request->get('cacheName');

        $statsCalculator = new StatisticsCalculator($this->context);
        switch ($cacheName) {
            case 'year':
                $result = $statsCalculator->computeYearStats();
                break;
            case 'month':
                $result = $statsCalculator->computeMonthStats();
                break;
            case 'day':
                $result = $statsCalculator->computeDayStats();
            break;
            default:
                break;
        }

        return $this->json([
            'success' => true,
            'stats' => $results
        ]);
    }

}
