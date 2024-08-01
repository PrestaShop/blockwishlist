import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  dataModules,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  utilsFile,
  utilsTest,
} from '@prestashop-core/ui-testing';

import { test, expect, Page, BrowserContext } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_installation_uninstallAndDeleteModule';

test.describe('Wishlist module - Uninstall and delete module', () => {
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
    expect(isModuleVisible).toBeTruthy()
  });

  test('should display the uninstall modal and cancel it', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'uninstall', true);
    expect(textResult).toEqual('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.blockwishlist);
    expect(isModuleVisible).toBeTruthy();

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.blockwishlist, 'uninstall');
    expect(isModalVisible).toBeFalsy();

    test.skip(true, 'Can\'t be tested as it is in Docker');

    const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.blockwishlist.tag}/`);
    expect(dirExists).toBeTruthy();
  });

  test('should uninstall the module', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'resetModule', baseContext);

    test.skip(true, 'Can\'t be uninstalled as it is a volume in Docker');

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'uninstall', false, true);
    expect(successMessage).toEqual(boModuleManagerPage.uninstallModuleSuccessMessage(dataModules.blockwishlist.tag));

    // Check the directory `modules/dataModules.blockwishlist.tag`
    const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.blockwishlist.tag}/`);
    expect(dirExists).toBeFalsy();
  });

  test('should view my shop', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'viewMyShop', baseContext);

    page = await boModuleManagerPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).toBeTruthy();
  });

  test('should go the product page', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 1);

    const productInformations = await foClassicProductPage.getProductInformation(page);
    expect(productInformations.name).toEqual(dataProducts.demo_1.name);
  });

  test('should check if the button "Add to wishlist" is present', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkButtonAddToWoshlist', baseContext);

    test.skip(true, 'Can\'t be uninstalled as it is a volume in Docker');

    const hasAddToWishlistButton = await foClassicProductPage.hasAddToWishlistButton(page);
    expect(hasAddToWishlistButton).toBeFalsy();
  });

  test('should go back to Back Office', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'returnToModulesManager', baseContext);

    page = await foClassicProductPage.closePage(browserContext, page, 0);

    test.skip(true, 'Can\'t be uninstalled as it is a volume in Docker');
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).toContain(boModuleManagerPage.pageTitle);
  });

  test(`should download the zip of the module '${dataModules.blockwishlist.name}'`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'downloadModule', baseContext);

    test.skip(true, 'Can\'t be uninstalled as it is a volume in Docker');

    const found = await utilsFile.doesFileExist('module_blockwishlist.zip');
    expect(found).toBeTruthy();
  });

  test(`should upload the module '${dataModules.blockwishlist.name}'`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'uploadModule', baseContext);

    test.skip(true, 'Can\'t be uninstalled as it is already installed');

    const successMessage = await boModuleManagerPage.uploadModule(page, 'module_blockwishlist.zip');
    expect(successMessage).toEqual(boModuleManagerPage.uploadModuleSuccessMessage);
  });

  test('should close upload module modal', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'closeModal', baseContext);

    test.skip(true, 'Can\'t be uninstalled as it is already installed');

    const isModalNotVisible = await boModuleManagerPage.closeUploadModuleModal(page);
    expect(isModalNotVisible).toBeTruthy();
  });

  test(`should search the module '${dataModules.blockwishlist.name}'`, async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkModulePresent', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
    expect(isModuleVisible, 'Module is not visible!').toBeTruthy();
  });
});
