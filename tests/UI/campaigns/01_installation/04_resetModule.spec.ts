import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  dataCustomers,
  dataModules,
  dataProducts,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalWishlistPage,
  foClassicMyWishlistsViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  modBlockwishlistBoMain,
  utilsTest,
} from '@prestashop-core/ui-testing';

import { test, expect, Page, BrowserContext } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_installation_resetModule';

test.describe('Wishlist module - Reset module', () => {
  const labelButton: string = 'Test Label Button';

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
    expect(isModuleVisible).toBeTruthy();
  });

  test('should display the reset modal and cancel it', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'reset', true);
    expect(textResult).toEqual('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.blockwishlist);
    expect(isModuleVisible).toBeTruthy();

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.blockwishlist, 'reset');
    expect(isModalVisible).toBeFalsy();
  });

  test(`should go to the configuration page of the module '${dataModules.blockwishlist.name}'`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.blockwishlist.tag);

    const pageTitle = await modBlockwishlistBoMain.getPageTitle(page);
    expect(pageTitle).toEqual(modBlockwishlistBoMain.pageTitle);

    const isConfigurationTabActive = await modBlockwishlistBoMain.isTabActive(page, 'Configuration');
    expect(isConfigurationTabActive).toBeTruthy();

    const wishlistDefaultTitle = await modBlockwishlistBoMain.getInputValue(page, 'wishlistDefaultTitle');
    expect(wishlistDefaultTitle).toEqual(modBlockwishlistBoMain.defaultValueWishlistDefaultTitle);

    const createButtonLabel = await modBlockwishlistBoMain.getInputValue(page, 'createButtonLabel');
    expect(createButtonLabel).toEqual(modBlockwishlistBoMain.defaultValueCreateButtonLabel);

    const wishlistPageName = await modBlockwishlistBoMain.getInputValue(page, 'wishlistPageName');
    expect(wishlistPageName).toEqual(modBlockwishlistBoMain.defaultValueWishlistPageName);
  });

  test('should update the label', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'updateLabel', baseContext);

    const textResult = await modBlockwishlistBoMain.setFormWording(page, undefined, labelButton);
    expect(textResult).toContain(modBlockwishlistBoMain.successfulUpdateMessage);
  });

  test('should go to Front Office', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToFo', baseContext);

    page = await modBlockwishlistBoMain.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).toBeTruthy();
  });

  test('should go to login page', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToLoginPageFO', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicLoginPage.pageTitle);
  });

  test('should sign in with default customer', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'sighInFO', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toBeTruthy();
  });

  test(`should search the product ${dataProducts.demo_3.name}`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductDemo3', baseContext);

    await foClassicMyWishlistsViewPage.searchProduct(page, dataProducts.demo_3.name);
    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(dataProducts.demo_3.name);
  });

  test('should add to the wishlist and get the label', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist1', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.getModalAddToCreateWislistLabel(page);
    expect(textResult).toContain(labelButton);
  });

  test('should return to \'Modules > Module Manager\' page', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToModuleManagerPageForReset', baseContext);

    page = await foClassicModalWishlistPage.changePage(browserContext, 0);
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).toContain(boModuleManagerPage.pageTitle);
  });

  test(`should search again the module ${dataModules.blockwishlist.name}`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchModuleForReset', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
    expect(isModuleVisible).toBeTruthy();
  });

  test('should reset the module', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'reset');
    expect(successMessage).toEqual(boModuleManagerPage.resetModuleSuccessMessage(dataModules.blockwishlist.tag));
  });

  test('should add to the wishlist and select the first wishlist', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist2', baseContext);

    page = await boModuleManagerPage.changePage(browserContext, 1);
    await foClassicProductPage.reloadPage(page);
    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.getModalAddToCreateWislistLabel(page);
    expect(textResult).toContain(modBlockwishlistBoMain.defaultValueCreateButtonLabel);
  });
});
