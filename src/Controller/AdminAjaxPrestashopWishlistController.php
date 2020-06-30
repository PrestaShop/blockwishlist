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
use PrestaShop\Module\BlockWishList\Calculator\StatisticsCalculator;
use PrestaShop\Module\BlockWishList\Type\ConfigurationType;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

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
        $datas = $this->getWishlistConfigurationDatas();
        $configurationForm = $this->createForm(ConfigurationType::class, $datas);
        $configurationForm->handleRequest($request);

        if ($configurationForm->isSubmitted() && $configurationForm->isValid()) {
            $resultHandleForm = $this->handleForm($configurationForm->getData());
        }

        return $this->render('@Modules/blockwishlist/views/templates/admin/home.html.twig', [
            'configurationForm' => $configurationForm->createView(),
            'resultHandleForm' => isset($resultHandleForm) ? $resultHandleForm : null,
        ]);
    }

    private function handleForm($datas)
    {
        $result = true;
        if (isset($datas['WishlistPageName'])) {
            foreach ($datas['WishlistPageName'] as $langID => $value) {
                $result &= \Configuration::updateValue('blockwishlist_WishlistPageName', [$langID => $value]);
            }
        }

        if (isset($datas['WishlistDefaultTitle'])) {
            foreach ($datas['WishlistDefaultTitle'] as $langID => $value) {
                $result &= \Configuration::updateValue('blockwishlist_WishlistDefaultTitle', [$langID => $value]);
            }
        }

        if (isset($datas['CreateButtonLabel'])) {
            foreach ($datas['CreateButtonLabel'] as $langID => $value) {
                $result &= \Configuration::updateValue('blockwishlist_CreateButtonLabel', [$langID => $value]);
            }
        }

        return $result;
    }

    private function getWishlistConfigurationDatas()
    {
        $languages = \Language::getLanguages(true);

        foreach ($languages as $lang) {
            $wishlistNames[$lang['id_lang']] = \Configuration::get('blockwishlist_WishlistPageName', $lang['id_lang']);
            $wishlistDefaultTitles[$lang['id_lang']] = \Configuration::get('blockwishlist_WishlistDefaultTitle', $lang['id_lang']);
            $wishlistCreateNewButtonsLabel[$lang['id_lang']] = \Configuration::get('blockwishlist_CreateButtonLabel', $lang['id_lang']);
        }

        $datas = [
            'WishlistPageName' => $wishlistNames,
            'WishlistDefaultTitle' => $wishlistDefaultTitles,
            'CreateButtonLabel' => $wishlistCreateNewButtonsLabel,
        ];

        return $datas;
    }

    public function getStatisticsAction(Request $request)
    {
        if ($this->cache->contains('blockwishlist.stats.allTime')) {
            $results = [
                'allTime' => $this->cache->fetch('blockwishlist.stats.allTime'),
                'currentYear' => $this->cache->fetch('blockwishlist.stats.currentYear'),
                'currentMonth' => $this->cache->fetch('blockwishlist.stats.currentMonth'),
                'currentDay' => $this->cache->fetch('blockwishlist.stats.currentDay'),
            ];
        } else {
            $results = (new StatisticsCalculator($this->context))->computeAllStats();
            $this->cache->save('blockwishlist.stats.allTime', $results['allTime'], self::CACHE_LIFETIME_SECONDS);
            $this->cache->save('blockwishlist.stats.currentYear', $results['currentYear'], self::CACHE_LIFETIME_SECONDS);
            $this->cache->save('blockwishlist.stats.currentMonth', $results['currentMonth'], self::CACHE_LIFETIME_SECONDS);
            $this->cache->save('blockwishlist.stats.currentDay', $results['currentDay'], self::CACHE_LIFETIME_SECONDS);
        }

        return $this->json([
            'success' => true,
            'stats' => $results,
        ]);
    }

    // this idea need some functional specification, it is not used ATM
    public function forceRefreshCacheStatsAction(Request $request)
    {
        $cacheName = $request->request->get('cacheName');

        $statsCalculator = new StatisticsCalculator($this->context);
        switch ($cacheName) {
            case 'year':
                $results = $statsCalculator->computeYearStats();
                break;
            case 'month':
                $results = $statsCalculator->computeMonthStats();
                break;
            case 'day':
                $results = $statsCalculator->computeDayStats();
            break;
            default:
                break;
        }

        return $this->json([
            'success' => true,
            'stats' => $results,
        ]);
    }
}
