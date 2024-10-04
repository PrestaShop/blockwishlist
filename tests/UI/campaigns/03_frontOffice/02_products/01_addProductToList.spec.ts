import {
  dataCustomers,
  dataProducts,
  FakerProduct,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalWishlistPage,
  foClassicMyAccountPage,
  foClassicMyWishlistsPage,
  foClassicMyWishlistsViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  opsBOProducts,
  utilsTest,
} from '@prestashop-core/ui-testing';

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

    await foClassicHomePage.goTo(page, global.FO.URL);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).toBeTruthy();
  });

  test('should go the product page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 1);

    const productInformations = await foClassicProductPage.getProductInformation(page);
    expect(productInformations.name).toEqual(dataProducts.demo_1.name);
  });

  test('should click on the button "Add to wishlist" and cancel', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickAddToWishlistAndCancel', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const hasModalLogin = await foClassicModalWishlistPage.hasModalLogin(page);
    expect(hasModalLogin).toBeTruthy();

    const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalLogin(page);
    expect(isModalVisible).toBeFalsy();
  });

  test('should click on the button "Add to wishlist" and login', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickAddToWishlistAndLogin', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const hasModalLogin = await foClassicModalWishlistPage.hasModalLogin(page);
    expect(hasModalLogin).toBeTruthy();

    await foClassicModalWishlistPage.clickLoginOnModalLogin(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').toContain(foClassicLoginPage.pageTitle);
  });

  test('should login', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'foLogin', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).toBeTruthy();
  });

  test('should go to "My Account" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount1', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists1', baseContext);

    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyWishlistsPage.pageTitle);

    wishlistName = await foClassicMyWishlistsPage.getWishlistName(page, 1);
  });

  test('should click on the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist1', baseContext);

    await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist1', baseContext);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test(`should search the product ${dataProducts.demo_3.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductDemo3', baseContext);

    await foClassicMyWishlistsViewPage.searchProduct(page, dataProducts.demo_3.name);
    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(dataProducts.demo_3.name);

    await foClassicProductPage.setQuantityByArrowUpDown(page, 5, 'up');
  });

  test('should add to the wishlist and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist1', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foClassicModalWishlistPage.messageAddedToWishlist);
  });

  test('should retun to "My Account" page after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount2', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists2', baseContext);

    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist page after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist2', baseContext);

    await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist after adding a product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist2', baseContext);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(1);

    const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 1);
    expect(nameProduct).toEqual(dataProducts.demo_3.name);

    const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 1);
    expect(qtyProduct).toEqual(5);

    const sizeProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 1, 'Size');
    expect(sizeProduct).toEqual('S');
  });

  test(`should search the product ${productOutOfStockNotAllowed.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

    await foClassicMyWishlistsViewPage.searchProduct(page, productOutOfStockNotAllowed.name);
    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(productOutOfStockNotAllowed.name);
  });

  test('should add to the wishlist page a product out-of-stock and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist2', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foClassicModalWishlistPage.messageAddedToWishlist);
  });

  test('should go to "My Account" page after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount3', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists3', baseContext);

    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist3', baseContext);

    await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist after adding a out-of-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist3', baseContext);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(2);

    const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 2);
    expect(nameProduct).toEqual(productOutOfStockNotAllowed.name);

    const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 2);
    expect(qtyProduct).toEqual(1);

    const isProductOutOfStock = await foClassicMyWishlistsViewPage.isProductOutOfStock(page, 2);
    expect(isProductOutOfStock).toBeTruthy();

    const hasButtonAddToCartDisabled = await foClassicMyWishlistsViewPage.hasButtonAddToCartDisabled(page, 2);
    expect(hasButtonAddToCartDisabled).toBeTruthy();
  });

  test(`should search the product ${productLowStock.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductLowStock', baseContext);

    await foClassicMyWishlistsViewPage.searchProduct(page, productLowStock.name);
    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(productLowStock.name);
  });

  test('should add to the wishlist a low-stock product and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist3', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foClassicModalWishlistPage.messageAddedToWishlist);
  });

  test('should go to "My Account" page after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount4', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists4', baseContext);

    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist4', baseContext);

    await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  test('should check the wishlist after adding a low-stock product', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist4', baseContext);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(3);

    const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 3);
    expect(nameProduct).toEqual(productLowStock.name);

    const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 2);
    expect(qtyProduct).toEqual(1);

    const isProductLastItemsInStock = await foClassicMyWishlistsViewPage.isProductLastItemsInStock(page, 3);
    expect(isProductLastItemsInStock).toBeTruthy();
  });

  test(`should search the product ${dataProducts.demo_1.name}`, async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'searchProductDemo1', baseContext);

    await foClassicMyWishlistsViewPage.searchProduct(page, dataProducts.demo_1.name);
    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).toEqual(dataProducts.demo_1.name);
  });

  test('should select the size \'M\' / color "Black" and check it', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'selectSizeColor', baseContext);

    await foClassicProductPage.selectAttributes(page, 'select', [{name: 'size', value: 'M'}]);
    await foClassicProductPage.selectAttributes(page, 'radio', [{name: 'Color', value: 'Black'}], 2);

    const selectedAttributeSize = await foClassicProductPage.getSelectedAttribute(page, 1, 'select');
    expect(selectedAttributeSize).toEqual('M');

    const selectedAttributeColor = await foClassicProductPage.getSelectedAttribute(page, 2, 'radio');
    expect(selectedAttributeColor).toEqual('Black');
  });

  test('should add to the wishlist a product with attributes and select the first wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'addToWishlist4', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
    expect(textResult).toEqual(foClassicModalWishlistPage.messageAddedToWishlist);
  });

  test('should go to "My Account" page after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyAccount5', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyAccountPage.pageTitle);
  });

  test('should go to "My wishlists" page after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'goToMyWishlists5', baseContext);

    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).toContain(foClassicMyWishlistsPage.pageTitle);
  });

  test('should click on the first wishlist after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'clickFirstWishlist5', baseContext);

    await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).toContain(wishlistName);
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/36496
  test('should check the wishlist after adding a product with attributes', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'checkWishlist5', baseContext);

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(4);

    // const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 4);
    const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 2);
    expect(nameProduct).toEqual(dataProducts.demo_1.name);

    //const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 4);
    const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 2);
    expect(qtyProduct).toEqual(1);

    //const sizeProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 4, 'Size');
    const sizeProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 2, 'Size');
    expect(sizeProduct).toEqual('M');

    //const colorProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 4, 'Color');
    const colorProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 2, 'Color');
    expect(colorProduct).toEqual('Black');
  });

  test('should empty the wishlist', async function () {
    await utilsTest.addContextItem(test.info(), 'testIdentifier', 'emptyWishlist', baseContext);

    for (let idxProduct = 1; idxProduct <= 4; idxProduct++) {
      const message = await foClassicMyWishlistsViewPage.removeProduct(page, 1);
      expect(message).toEqual(foClassicMyWishlistsViewPage.messageSuccessfullyRemoved);
    }

    const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
    expect(numProducts).toEqual(0);
  });

  test('POST-Condition : Delete product out of stock not allowed', async () => {
    await opsBOProducts.deleteProduct(page, productOutOfStockNotAllowed, `${baseContext}_postTest_0`, 1);
  });

  test('POST-Condition : Delete product with a low stock', async () => {
    await opsBOProducts.deleteProduct(page, productLowStock, `${baseContext}_postTest_1`, 2);
  });
});
