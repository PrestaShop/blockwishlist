import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  modBlockwishlistBoMain,
  dataModules,
  utilsTest,
} from '@prestashop-core/ui-testing';

import { test, expect, Page, BrowserContext } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_configuration_helpHeaderButtons';

test.describe('Wishlist module - Help header buttons', () => {
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

  test('should open the help side bar and check the document language', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await modBlockwishlistBoMain.openHelpSideBar(page);
    expect(isHelpSidebarVisible).toEqual(true);

    const documentURL = await modBlockwishlistBoMain.getHelpDocumentURL(page);
    expect(documentURL).toContain('country=en');
  });

  test('should close the help side bar', async () => {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await modBlockwishlistBoMain.closeHelpSideBar(page);
    expect(isHelpSidebarClosed).toEqual(true);
  });
});
