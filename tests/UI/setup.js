require('jsdom-global')();
global.productsAlreadyTagged = [];
global.window.prestashop = {
  customer: {
    is_logged: false
  }
};
global.expect = require('expect');
