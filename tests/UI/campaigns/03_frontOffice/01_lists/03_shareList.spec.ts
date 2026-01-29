import {
  dataCustomers,
  dataModules,
  foHomePage,
  foLoginPage,
  foModalWishlistPage,
  foMyAccountPage,
  foMyWishlistsPage,
  foMyWishlistsViewPage,
  opsBOModules,
  utilsTest,
} from '@prestashop-core/ui-testing';

import { test, expect, Page, BrowserContext } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_frontOffice_lists_shareList';

test.describe('Wishlist module - Share a list', async () => {
  const wishlistName: string = 'Ma liste de souhaits';

  let browserContext: BrowserContext;
  let page: Page;
  let wishlistUrl: string;

  test.beforeAll(async ({ browser }) => {
    browserContext = await browser.newContext({
      permissions: ['clipboard-read'],
    });
    page = await browserContext.newPage();
  });
  test.afterAll(async () => {
    await page.close();
  });

  test('should open the shop page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToShopFO', baseContext);

    await foHomePage.goTo(page, global.FO.URL);

    const isHomePage = await foHomePage.isHomePage(page);
    expect(isHomePage).toEqual(true);
  });

  test('should go to login page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToLoginFO', baseContext);

    await foHomePage.goToLoginPage(page);

    const pageTitle = await foLoginPage.getPageTitle(page);
    expect(pageTitle).toContain(foLoginPage.pageTitle);
  });

  test('should login', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'foLogin', baseContext);

    await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toEqual(true);
  });

  test('should go to "My Account" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount1', baseContext);

    await foHomePage.goToMyAccountPage(page);

    const pageTitle = await foMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyAccountPage.pageTitle);
  });

  test('should go to "My Wishlists" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists1', baseContext);

    await foMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyWishlistsPage.pageTitle);
  });

  test('should click on the share icon and cancel the modal', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickShareAndCancel', baseContext);

    await foMyWishlistsPage.clickShareWishlistButton(page, 1);

    const hasModalShare = await foModalWishlistPage.hasModalShare(page);
    expect(hasModalShare).toEqual(true);

    const isModalVisible = await foModalWishlistPage.clickCancelOnModalShare(page);
    expect(isModalVisible).toEqual(false);
  });

  test('should click on the share icon and copy the text', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickShareAndCopyText', baseContext);

    await foMyWishlistsPage.clickShareWishlistButton(page, 1);

    const hasModalLogin = await foModalWishlistPage.hasModalShare(page);
    expect(hasModalLogin).toEqual(true);

    const textToast = await foModalWishlistPage.clickShareOnModalShare(page);
    expect(textToast).toEqual(foModalWishlistPage.messageLinkSharedWishlist);
  });

  test('should click on the Create new list link and cancel', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'createNewListAndCancel', baseContext);

    await foMyWishlistsPage.clickCreateWishlistButton(page);

    const hasModalCreate = await foModalWishlistPage.hasModalCreate(page);
    expect(hasModalCreate).toEqual(true);

    const isModalVisible = await foModalWishlistPage.clickCancelOnModalCreate(page);
    expect(isModalVisible).toEqual(false);
  });

  test('should click on the Create new list link and create it', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'createNewListAndCreate', baseContext);

    await foMyWishlistsPage.clickCreateWishlistButton(page);

    const hasModalCreate = await foModalWishlistPage.hasModalCreate(page);
    expect(hasModalCreate).toEqual(true);

    await foModalWishlistPage.setNameOnModalCreate(page, wishlistName);

    const textToast = await foModalWishlistPage.clickCreateOnModalCreate(page);
    expect(textToast).toEqual(foModalWishlistPage.messageWishlistCreated);
  });

  test('should click on the share icon (in dropdown) and cancel the modal', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickDropdownShareAndCancel', baseContext);

    await foMyWishlistsPage.clickShareWishlistButton(page, 2);

    const hasModalShare = await foModalWishlistPage.hasModalShare(page);
    expect(hasModalShare).toEqual(true);

    const isModalVisible = await foModalWishlistPage.clickCancelOnModalShare(page);
    expect(isModalVisible).toEqual(false);
  });

  test('should click on the share icon (in dropdown) and copy the text', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickDropdownShareAndCopyText', baseContext);

    await foMyWishlistsPage.clickShareWishlistButton(page, 2);

    const hasModalLogin = await foModalWishlistPage.hasModalShare(page);
    expect(hasModalLogin).toEqual(true);

    const textToast = await foModalWishlistPage.clickShareOnModalShare(page);
    expect(textToast).toEqual(foModalWishlistPage.messageLinkSharedWishlist);

    wishlistUrl = await foMyWishlistsPage.getClipboardText(page);
    expect(wishlistUrl.length).toBeGreaterThan(0);
  });

  test('should go to the shared wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToSharedWishlistLogged', baseContext);

    await foMyWishlistsPage.goTo(page, wishlistUrl);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test('should logout', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'logout', baseContext);

    await foMyWishlistsViewPage.logout(page);
    await foMyWishlistsViewPage.clickOnHeaderLink(page, 'Logo');

    const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toEqual(false);
  });

  test('should return to the shared wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToSharedWishlistUnlogged', baseContext);

    await foLoginPage.goTo(page, wishlistUrl);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test('POST-Condition : Reset the module', async function () {
    await opsBOModules.resetModule(page, dataModules.blockwishlist, `${baseContext}_postTest_0`);
  });
});
