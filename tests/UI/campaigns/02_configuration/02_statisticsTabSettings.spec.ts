
// Import utils
import {testContext, dashboardPage, loginPage} from '@prestashop-core/ui-testing/dist';

// Import commonTests
//import loginCommon from '@prestashop-core/ui-testing/dist/common/BO/loginBO';

// Import pages
// Import BO pages
//import dashboardPage from '@prestashop-core/ui-testing/dist/pages/BO/dashboard';
//import loginPage from '@prestashop-core/ui-testing/dist/pages/BO/login';
/*
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import blockwishlistPage from '@pages/BO/modules/blockwishlist';
import blockwishlistStatisticsPage from '@pages/BO/modules/blockwishlist/statistics';
// Import FO pages
import categoryPage from '@pages/FO/category';
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';

// Import data
import Customers from '@data/demo/customers';
import Modules from '@data/demo/modules';*/

import { test, expect } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_configuration_statisticsTabSettings';

test('Wishlist module - Statistics tab settings', async ({ page }) => {
  await test.step('should login in BO', async () => {
    await testContent.addContextItem(test.info(), 'testIdentifier', 'loginBO', baseContext);

    await loginPage.goTo(page, global.BO.URL);
    await loginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).toContain(dashboardPage.pageTitle);
  });

  await test.step('should go to \'Modules > Module Manager\' page', async () => {
    await testContext.addContextItem(test.info(), 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );
    /*
    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);*/
  });
/*
    test(`should search the module ${Modules.blockwishlist.name}`, async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.blockwishlist);
      expect(isModuleVisible).to.eq(true);
    });

    test(`should go to the configuration page of the module '${Modules.blockwishlist.name}'`, async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, Modules.blockwishlist.tag);

      const pageTitle = await blockwishlistPage.getPageTitle(page);
      expect(pageTitle).to.eq(blockwishlistPage.pageTitle);

      const isConfigurationTabActive = await blockwishlistPage.isTabActive(page, 'Configuration');
      expect(isConfigurationTabActive).to.eq(true);

      const isStatisticsTabActive = await blockwishlistPage.isTabActive(page, 'Statistics');
      expect(isStatisticsTabActive).to.eq(false);
    });

    test('should go on Statistics Tab', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStatisticsTab', baseContext);

      await blockwishlistPage.goToStatisticsTab(page);

      const pageTitle = await blockwishlistStatisticsPage.getPageTitle(page);
      expect(pageTitle).to.eq(blockwishlistStatisticsPage.pageTitle);

      const noRecordsFoundText = await blockwishlistStatisticsPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });
  });
  describe('Go to the FO and add to favorites some products', async () => {
    test('should go to the FO', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      page = await blockwishlistStatisticsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    test('should go to login page', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    test('should sign in with default customer', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFo', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    test('should go to all products page', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.eq(true);
    });

    for (let idxProduct: number = 1; idxProduct <= 3; idxProduct++) {
      // eslint-disable-next-line no-loop-func
      test(`should add product #${idxProduct} to wishlist`, async ({ page }) => {
        await testContext.addContextItem(this, 'testIdentifier', `addToFavorite${idxProduct}`, baseContext);

        const textResult = await categoryPage.addToWishList(page, idxProduct);
        expect(textResult).to.be.eq(categoryPage.messageAddedToWishlist);

        const isAddedToWishlist = await categoryPage.isAddedToWishlist(page, idxProduct);
        expect(isAddedToWishlist).to.eq(true);
      });
    }

    test('should logout', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogout', baseContext);

      await categoryPage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);
    });
  });
  describe('Return to BO and check statistics', async () => {
    test('should go to BO', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBoBack', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await blockwishlistStatisticsPage.getPageTitle(page);
      expect(pageTitle).to.contains(blockwishlistStatisticsPage.pageTitle);
    });
    // @todo : https://github.com/PrestaShop/PrestaShop/issues/33374
    test('should click on the refresh button', async ({ page }) => {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnRefreshButton', baseContext);

      test.skip();

      await blockwishlistStatisticsPage.refreshStatistics(page);

      // Check statistics
      const pageTitle = await blockwishlistStatisticsPage.getPageTitle(page);
      expect(pageTitle).to.contains(blockwishlistStatisticsPage.pageTitle);
    });
  });*/
});
