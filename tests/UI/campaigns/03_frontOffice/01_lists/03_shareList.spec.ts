import {
  dataCustomers,
  dataModules,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalWishlistPage,
  foClassicMyAccountPage,
  foClassicMyWishlistsPage,
  foClassicMyWishlistsViewPage,
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

    await foClassicHomePage.goTo(page, global.FO.URL);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).toEqual(true);
  });

  test('should go to login page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToLoginFO', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicLoginPage.pageTitle);
  });

  test('should login', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'foLogin', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toEqual(true);
  });

  test('should go to "My Account" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount1', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyAccountPage.pageTitle);
  });

  test('should go to "My Wishlists" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists1', baseContext);

    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyWishlistsPage.pageTitle);
  });

  test('should click on the share icon and cancel the modal', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickShareAndCancel', baseContext);

    await foClassicMyWishlistsPage.clickShareWishlistButton(page, 1);

    const hasModalShare = await foClassicModalWishlistPage.hasModalShare(page);
    expect(hasModalShare).toEqual(true);

    const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalShare(page);
    expect(isModalVisible).toEqual(false);
  });

  test('should click on the share icon and copy the text', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickShareAndCopyText', baseContext);

    await foClassicMyWishlistsPage.clickShareWishlistButton(page, 1);

    const hasModalLogin = await foClassicModalWishlistPage.hasModalShare(page);
    expect(hasModalLogin).toEqual(true);

    const textToast = await foClassicModalWishlistPage.clickShareOnModalShare(page);
    expect(textToast).toEqual(foClassicModalWishlistPage.messageLinkSharedWishlist);
  });

  test('should click on the Create new list link and cancel', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'createNewListAndCancel', baseContext);

    await foClassicMyWishlistsPage.clickCreateWishlistButton(page);

    const hasModalCreate = await foClassicModalWishlistPage.hasModalCreate(page);
    expect(hasModalCreate).toEqual(true);

    const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalCreate(page);
    expect(isModalVisible).toEqual(false);
  });

  test('should click on the Create new list link and create it', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'createNewListAndCreate', baseContext);

    await foClassicMyWishlistsPage.clickCreateWishlistButton(page);

    const hasModalCreate = await foClassicModalWishlistPage.hasModalCreate(page);
    expect(hasModalCreate).toEqual(true);

    await foClassicModalWishlistPage.setNameOnModalCreate(page, wishlistName);

    const textToast = await foClassicModalWishlistPage.clickCreateOnModalCreate(page);
    expect(textToast).toEqual(foClassicModalWishlistPage.messageWishlistCreated);
  });

  test('should click on the share icon (in dropdown) and cancel the modal', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickDropdownShareAndCancel', baseContext);

    await foClassicMyWishlistsPage.clickShareWishlistButton(page, 2);

    const hasModalShare = await foClassicModalWishlistPage.hasModalShare(page);
    expect(hasModalShare).toEqual(true);

    const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalShare(page);
    expect(isModalVisible).toEqual(false);
  });

  test('should click on the share icon (in dropdown) and copy the text', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickDropdownShareAndCopyText', baseContext);

    await foClassicMyWishlistsPage.clickShareWishlistButton(page, 2);

    const hasModalLogin = await foClassicModalWishlistPage.hasModalShare(page);
    expect(hasModalLogin).toEqual(true);

    const textToast = await foClassicModalWishlistPage.clickShareOnModalShare(page);
    expect(textToast).toEqual(foClassicModalWishlistPage.messageLinkSharedWishlist);

    wishlistUrl = await foClassicMyWishlistsPage.getClipboardText(page);
    expect(wishlistUrl.length).toBeGreaterThan(0);
  });

  test('should go to the shared wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToSharedWishlistLogged', baseContext);

    await foClassicMyWishlistsPage.goTo(page, wishlistUrl);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test('should logout', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'logout', baseContext);

    await foClassicMyWishlistsViewPage.logout(page);
    await foClassicMyWishlistsViewPage.clickOnHeaderLink(page, 'Logo');

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toEqual(false);
  });

  test('should return to the shared wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToSharedWishlistUnlogged', baseContext);

    await foClassicLoginPage.goTo(page, wishlistUrl);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test('POST-Condition : Reset the module', async function () {
    await opsBOModules.resetModule(page, dataModules.blockwishlist, `${baseContext}_postTest_0`);
  });
});
