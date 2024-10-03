import {
  // Import BO pages
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  // Import data
  dataCustomers,
  dataModules,
  // Import FO pages
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicLoginPage,
  // Import modules
  modBlockwishlistBoMain,
  modBlockwishlistBoStatistics,
  // Import utils
  utilsTest,
} from '@prestashop-core/ui-testing';

import { test, expect, Page, BrowserContext } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_configuration_statisticsTabSettings';

test.describe('Wishlist module - Statistics tab settings', () => {
  const numProducts: number = 3;

  let browserContext: BrowserContext;
  let page: Page;

  test.beforeAll(async ({ browser }) => {
    browserContext = await browser.newContext();
    page = await browserContext.newPage();
  });
  test.afterAll(async () => {
    await page.close();
  });

  test('should login in BO', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).toContain(boDashboardPage.pageTitle);
  });

  test('should go to \'Modules > Module Manager\' page', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).toContain(boModuleManagerPage.pageTitle);
  });


  test(`should search the module ${dataModules.blockwishlist.name}`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
    expect(isModuleVisible).toEqual(true);
  });

  test(`should go to the configuration page of the module '${dataModules.blockwishlist.name}'`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.blockwishlist.tag);

    const pageTitle = await modBlockwishlistBoMain.getPageTitle(page);
    expect(pageTitle).toEqual(modBlockwishlistBoMain.pageTitle);

    const isConfigurationTabActive = await modBlockwishlistBoMain.isTabActive(page, 'Configuration');
    expect(isConfigurationTabActive).toEqual(true);

    const isStatisticsTabActive = await modBlockwishlistBoMain.isTabActive(page, 'Statistics');
    expect(isStatisticsTabActive).toEqual(false);
  });
  
  test('should go on Statistics Tab', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToStatisticsTab', baseContext);

    await modBlockwishlistBoMain.goToStatisticsTab(page);

    const pageTitle = await modBlockwishlistBoStatistics.getPageTitle(page);
    expect(pageTitle).toEqual(modBlockwishlistBoStatistics.pageTitle);

    const noRecordsFoundText = await modBlockwishlistBoStatistics.getTextForEmptyTable(page);
    expect(noRecordsFoundText).toContain('warning No records found');
  });

  test('should go to the FO', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToFO', baseContext);

    page = await modBlockwishlistBoStatistics.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).toEqual(true);
  });

  test('should go to login page', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToLoginPage', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').toContain(foClassicLoginPage.pageTitle);
  });

  test('should sign in with default customer', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'sighInFo', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').toEqual(true);
  });

  test('should go to all products page', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToAllProducts', baseContext);

    await foClassicHomePage.goToAllProductsPage(page);

    const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible).toEqual(true);
  });

    for (let idxProduct: number = 1; idxProduct <= numProducts; idxProduct++) {
      // eslint-disable-next-line no-loop-func
      test(`should add product #${idxProduct} to wishlist`, async () => {
        await utilsTest.addContextItem(test.info(), 'testIdentifier', `addToFavorite${idxProduct}`, baseContext);

        const textResult = await foClassicCategoryPage.addToWishList(page, idxProduct);
        expect(textResult).toEqual(foClassicCategoryPage.messageAddedToWishlist);

        const isAddedToWishlist = await foClassicCategoryPage.isAddedToWishlist(page, idxProduct);
        expect(isAddedToWishlist).toEqual(true);
      });
    }

  test('should logout', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'foLogout', baseContext);

    await foClassicCategoryPage.logout(page);

    const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
    expect(isCustomerConnected).toEqual(false);
  });
  
  test('should go to BO', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToBoBack', baseContext);

    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const pageTitle = await modBlockwishlistBoStatistics.getPageTitle(page);
    expect(pageTitle).toContain(modBlockwishlistBoStatistics.pageTitle);
  });

  test('should click on the refresh button', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickOnRefreshButton', baseContext);

    await modBlockwishlistBoStatistics.refreshStatistics(page);

    const pageTitle = await modBlockwishlistBoStatistics.getPageTitle(page);
    expect(pageTitle).toContain(modBlockwishlistBoStatistics.pageTitle);

    const numProductsInTable = await modBlockwishlistBoStatistics.getNumProducts(page);
    expect(numProductsInTable).toEqual(numProducts);
  });
});
