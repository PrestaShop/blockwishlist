import {
  dataCustomers,
  dataProducts,
  FakerProduct,
  foHomePage,
  foLoginPage,
  foModalWishlistPage,
  foMyAccountPage,
  foMyWishlistsPage,
  foMyWishlistsViewPage,
  foProductPage,
  foSearchResultsPage,
  opsBOProducts,
  utilsTest,
} from '@prestashop-core/ui-testing';
import semver from 'semver';

import { test, expect, Page, BrowserContext } from '@playwright/test';

const baseContext: string = 'modules_blockwishlist_frontOffice_products_addProductToList';

test.describe('Wishlist module - Add a product to a list', async () => {
  const productOutOfStockNotAllowed: FakerProduct = new FakerProduct({
    name: 'Product Out of stock not allowed',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 0,
    behaviourOutOfStock: 'Deny orders',
  });
  const productLowStock: FakerProduct = new FakerProduct({
    name: 'Product Low Stock',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 2,
  });

  let browserContext: BrowserContext;
  let page: Page;
  let wishlistName: string;

  test.beforeAll(async ({ browser }) => {
    browserContext = await browser.newContext();
    page = await browserContext.newPage();
  });
  test.afterAll(async () => {
    await page.close();
  });

  test('PRE-Condition : Create product out of stock not allowed', async () => {
    await opsBOProducts.createProduct(page, productOutOfStockNotAllowed, `${baseContext}_preTest_0`, 1);
  });

  test('PRE-Condition : Create product with a low stock', async () => {
    await opsBOProducts.createProduct(page, productLowStock, `${baseContext}_preTest_1`, 2);
  });

  test('should open the shop page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToShopFO', baseContext);

    await foHomePage.goTo(page, global.FO.URL);

    const isHomePage = await foHomePage.isHomePage(page);
    expect(isHomePage).toBeTruthy();
  });

  test('should go the product page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToProductPage', baseContext);

    await foHomePage.goToProductPage(page, 1);

    const productInformations = await foProductPage.getProductInformation(page);
    expect(productInformations.name).toEqual(dataProducts.demo_1.name);
  });

  test('should click on the button "Add to wishlist" and cancel', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickAddToWishlistAndCancel', baseContext);

    await foProductPage.clickAddToWishlistButton(page);

    const hasModalLogin = await foModalWishlistPage.hasModalLogin(page);
    expect(hasModalLogin).toBeTruthy();

    const isModalVisible = await foModalWishlistPage.clickCancelOnModalLogin(page);
    expect(isModalVisible).toBeFalsy();
  });

  test('should click on the button "Add to wishlist" and login', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickAddToWishlistAndLogin', baseContext);

    await foProductPage.clickAddToWishlistButton(page);

    const hasModalLogin = await foModalWishlistPage.hasModalLogin(page);
    expect(hasModalLogin).toBeTruthy();

    await foModalWishlistPage.clickLoginOnModalLogin(page);

    const pageTitle = await foLoginPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').toContain(foLoginPage.pageTitle);
  });

  test('should login', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'foLogin', baseContext);

    await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toBeTruthy();
  });

  test('should go to "My Account" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount1', baseContext);

    await foHomePage.goToMyAccountPage(page);

    const pageTitle = await foMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists1', baseContext);

    await foMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyWishlistsPage.pageTitle);

    wishlistName = await foMyWishlistsPage.getWishlistName(page, 1);
  });

  test('should click on the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist1', baseContext);

    await foMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist1', baseContext);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test(`should search the product ${dataProducts.demo_3.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductDemo3', baseContext);

    await foMyWishlistsViewPage.searchProduct(page, dataProducts.demo_3.name);
    await foSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(dataProducts.demo_3.name);

    await foProductPage.setQuantityByArrowUpDown(
      page,
      5,
      semver.gte(utilsTest.getPSVersion(), '9.2.0') ? 'increment' : 'up',
    );
  });

  test('should add to the wishlist and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist1', baseContext);

    await foProductPage.clickAddToWishlistButton(page);

    const textResult = await foModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foModalWishlistPage.messageAddedToWishlist);
  });

  test('should retun to "My Account" page after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount2', baseContext);

    await foHomePage.goToMyAccountPage(page);

    const pageTitle = await foMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists2', baseContext);

    await foMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist page after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist2', baseContext);

    await foMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist2', baseContext);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(1);

    const nameProduct = await foMyWishlistsViewPage.getProductName(page, 1);
    expect(nameProduct).toEqual(dataProducts.demo_3.name);

    // @todo : https://github.com/PrestaShop/hummingbird/issues/908
    if (semver.lt(utilsTest.getPSVersion(), '9.2.0')) {
      const qtyProduct = await foMyWishlistsViewPage.getProductQuantity(page, 1);
      expect(qtyProduct).toEqual(5);
    }

    const sizeProduct = await foMyWishlistsViewPage.getProductAttribute(page, 1, 'Size');
    expect(sizeProduct).toEqual('S');
  });

  test(`should search the product ${productOutOfStockNotAllowed.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

    await foMyWishlistsViewPage.searchProduct(page, productOutOfStockNotAllowed.name);
    await foSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(productOutOfStockNotAllowed.name);
  });

  test('should add to the wishlist page a product out-of-stock and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist2', baseContext);

    await foProductPage.clickAddToWishlistButton(page);

    const textResult = await foModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foModalWishlistPage.messageAddedToWishlist);
  });

  test('should go to "My Account" page after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount3', baseContext);

    await foHomePage.goToMyAccountPage(page);

    const pageTitle = await foMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists3', baseContext);

    await foMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist3', baseContext);

    await foMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist3', baseContext);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(2);

    const nameProduct = await foMyWishlistsViewPage.getProductName(page, 2);
    expect(nameProduct).toEqual(productOutOfStockNotAllowed.name);

    const qtyProduct = await foMyWishlistsViewPage.getProductQuantity(page, 2);
    expect(qtyProduct).toEqual(1);

    const isProductOutOfStock = await foMyWishlistsViewPage.isProductOutOfStock(page, 2);
    expect(isProductOutOfStock).toBeTruthy();

    const hasButtonAddToCartDisabled = await foMyWishlistsViewPage.hasButtonAddToCartDisabled(page, 2);
    expect(hasButtonAddToCartDisabled).toBeTruthy();
  });

  test(`should search the product ${productLowStock.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductLowStock', baseContext);

    await foMyWishlistsViewPage.searchProduct(page, productLowStock.name);
    await foSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(productLowStock.name);
  });

  test('should add to the wishlist a low-stock product and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist3', baseContext);

    await foProductPage.clickAddToWishlistButton(page);

    const textResult = await foModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foModalWishlistPage.messageAddedToWishlist);
  });

  test('should go to "My Account" page after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount4', baseContext);

    await foHomePage.goToMyAccountPage(page);

    const pageTitle = await foMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists4', baseContext);

    await foMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist4', baseContext);

    await foMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist4', baseContext);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(3);

    const nameProduct = await foMyWishlistsViewPage.getProductName(page, 3);
    expect(nameProduct).toEqual(productLowStock.name);

    const qtyProduct = await foMyWishlistsViewPage.getProductQuantity(page, 2);
    expect(qtyProduct).toEqual(1);

    const isProductLastItemsInStock = await foMyWishlistsViewPage.isProductLastItemsInStock(page, 3);
    expect(isProductLastItemsInStock).toBeTruthy();
  });

  test(`should search the product ${dataProducts.demo_1.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductDemo1', baseContext);

    await foMyWishlistsViewPage.searchProduct(page, dataProducts.demo_1.name);
    await foSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(dataProducts.demo_1.name);
  });

  test('should select the size "M" / color "Black" and check it', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'selectSizeColor', baseContext);

    await foProductPage.selectAttributes(page, 'select', [{name: 'size', value: 'M'}]);
    await foProductPage.selectAttributes(page, 'radio', [{name: 'Color', value: 'Black'}], 2);

    const selectedAttributeSize = await foProductPage.getSelectedAttribute(page, 1, 'select');
    expect(selectedAttributeSize).toEqual('M');

    const selectedAttributeColor = await foProductPage.getSelectedAttribute(page, 2, 'radio');
    expect(selectedAttributeColor).toContain('Black');
  });

  test('should add to the wishlist a product with attributes and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist4', baseContext);

    await foProductPage.clickAddToWishlistButton(page);

    const textResult = await foModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foModalWishlistPage.messageAddedToWishlist);
  });

  test('should go to "My Account" page after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount5', baseContext);

    await foHomePage.goToMyAccountPage(page);

    const pageTitle = await foMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists5', baseContext);

    await foMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist5', baseContext);

    await foMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/36496
  test('should check the wishlist after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist5', baseContext);

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(4);

    // const nameProduct = await foMyWishlistsViewPage.getProductName(page, 4);
    const nameProduct = await foMyWishlistsViewPage.getProductName(page, 2);
    expect(nameProduct).toEqual(dataProducts.demo_1.name);

    //const qtyProduct = await foMyWishlistsViewPage.getProductQuantity(page, 4);
    const qtyProduct = await foMyWishlistsViewPage.getProductQuantity(page, 2);
    expect(qtyProduct).toEqual(1);

    //const sizeProduct = await foMyWishlistsViewPage.getProductAttribute(page, 4, 'Size');
    const sizeProduct = await foMyWishlistsViewPage.getProductAttribute(page, 2, 'Size');
    expect(sizeProduct).toEqual('M');

    //const colorProduct = await foMyWishlistsViewPage.getProductAttribute(page, 4, 'Color');
    const colorProduct = await foMyWishlistsViewPage.getProductAttribute(page, 2, 'Color');
    expect(colorProduct).toEqual('Black');
  });

  test('should empty the wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'emptyWishlist', baseContext);

    for (let idxProduct = 1; idxProduct <= 4; idxProduct++) {
      const message = await foMyWishlistsViewPage.removeProduct(page, 1);
      expect(message).toEqual(foMyWishlistsViewPage.messageSuccessfullyRemoved);
    }

    const numProducts = await foMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test('POST-Condition : Delete product out of stock not allowed', async () => {
    await opsBOProducts.deleteProduct(page, productOutOfStockNotAllowed, `${baseContext}_postTest_0`, 1);
  });

  test('POST-Condition : Delete product with a low stock', async () => {
    await opsBOProducts.deleteProduct(page, productLowStock, `${baseContext}_postTest_1`, 2);
  });
});
