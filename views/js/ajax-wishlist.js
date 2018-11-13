/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
(function initBlockWishlistModule() {
  var current;
  if (typeof window.$ === 'undefined' || typeof window.BlockWishlistModule === 'undefined') {
    return setTimeout(function () {
      initBlockWishlistModule.apply(null, arguments);
    }, 100);
  }

  var config = window.BlockWishlistModule.config;
  var translations = window.BlockWishlistModule.translations;
  window.BlockWishlistModule.front = window.BlockWishlistModule.front || {};
  window.BlockWishlistModule.front.cart = function (id, action, idProduct, idProductAttribute, quantity, idWishlist) {
    $.ajax({
      type: 'GET',
      url: config.urls.cart,
      headers: { 'cache-control': 'no-cache' },
      cache: false,
      data: {
        rand: new Date().getTime(),
        action: action,
        id_product: idProduct,
        quantity: quantity,
        token: static_token,
        id_product_attribute: idProductAttribute,
        id_wishlist: idWishlist
      },
      success: function(data) {
        if (action === 'add') {
          if (config.isLogged) {
            window.BlockWishlistModule.front.addId(idProduct);
            window.BlockWishlistModule.front.refreshStatus();

            if (!!$.prototype.fancybox) {
              $.fancybox.open([
                {
                  type: 'inline',
                  autoScale: true,
                  minHeight: 30,
                  content: '<p class="fancybox-error">' + translations.addedToWishlist + '</p>'
                }
              ], {
                padding: 0
              });
            } else {
              alert(translations.addedToWishlist);
            }
          } else {
            if (!!$.prototype.fancybox) {
              $.fancybox.open([
                {
                  type: 'inline',
                  autoScale: true,
                  minHeight: 30,
                  content: '<p class="fancybox-error">' + translations.loginRequired + '</p>'
                }
              ], {
                padding: 0
              });
            } else {
              alert(translations.loginRequired);
            }
          }
        }
        if (action === 'delete') {
          window.BlockWishlistModule.front.removeId(idProduct);
          window.BlockWishlistModule.front.refreshStatus();
        }
        var $id = $('#' + id);
        if($id.length !== 0) {
          $id.slideUp('normal');
          $id.html(data);
          $id.slideDown('normal');
        }
      }
    });
  };
  /**
   * Buy Product
   *
   * @return {boolean}
   */
  window.BlockWishlistModule.front.buyProduct = function (token, id_product, id_product_attribute, id_quantity, button, ajax) {
    if (ajax) {
      ajaxCart.add(id_product, id_product_attribute, false, button, 1, [token, id_quantity]);
    } else {
      $('#' + id_quantity).val(0);
      window.BlockWishlistModule.front.addProductCart(token, id_product, id_product_attribute, id_quantity)
      document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].method = 'POST';
      document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].action = baseUri + '?controller=cart';
      document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].elements['token'].value = static_token;
      document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].submit();
    }

    return true;
  };
  /**
   * Change customer default wishlist
   *
   * @return void
   */
  window.BLockWishlistModule.front.changeDefault = function (id, id_wishlist) {
    $.ajax({
      type: 'GET',
      url: config.urls.cart,
      headers: { 'cache-control': 'no-cache' },
      data: {
        rand: new Date().getTime(),
        id_wishlist: id_wishlist,
        token: + static_token,
      },
      success: function (data) {
        var $id = $('#' + id);
        $id.slideUp('normal');
        $id.html(data);
        $id.slideDown('normal');
      }
    });
  };
  window.BlockWishlistModule.front.addProductCart = function (token, id_product, id_product_attribute, id_quantity) {
    if ($('#' + id_quantity).val() <= 0) {
      return (false);
    }

    $.ajax({
      type: 'GET',
      url: config.urls.buywishlistproduct,
      headers: { 'cache-control': 'no-cache' },
      data: {
        rand: new Date().getTime(),
        token: token,
        static_token: static_token,
        id_product: id_product,
        id_product_attribute: id_product_attribute,
      },
      success: function (data) {
        if (data) {
          if (!!$.prototype.fancybox)
            $.fancybox.open([
              {
                type: 'inline',
                autoScale: true,
                minHeight: 30,
                content: '<p class="fancybox-error">' + data + '</p>'
              }
            ], {
              padding: 0
            });
          else
            alert(data);
        }
        else
          $('#' + id_quantity).val($('#' + id_quantity).val() - 1);
      }
    });

    return (true);
  };
  /**
   * Show wishlist managment page
   *
   * @return void
   */
  window.BlockWishlistModule.front.manage = function (id, id_wishlist) {
    $.ajax({
      type: 'GET',
      async: true,
      url: config.urls.managewishlist,
      headers: { 'cache-control': 'no-cache' },
      data: {
        rand: new Date().getTime(),
        id_wishlist: id_wishlist,
        refresh: false
      },
      success: function (data) {
        var $id = $('#' + id);
        $id.hide();
        $id.html(data);
        $id.fadeIn('slow');

        $('.wishlist_change_button').each(function () {
          $(this).change(function () {
            window.BlockwishlistModule.changeProduct($('option:selected', this).attr('data-id-product'), $('option:selected', this).attr('data-id-product-attribute'), $('option:selected', this).attr('data-id-old-wishlist'), $('option:selected', this).attr('data-id-new-wishlist'));
          });
        });
      }
    });
  };
  window.BlockWishlistModule.front.default = function (id, id_wishlist) {
    $.ajax({
      type: 'GET',
      url: config.urls.mywishlist,
      headers: { 'cache-control': 'no-cache' },
      data: {
        rand: new Date().getTime(),
        'default': 1,
        id_wishlist: id_wishlist,
        myajax: 1,
        action: 'setdefault'
      },
      success: function (data) {
        var old_default_id = $(".is_wishlist_default").parents("tr").attr("id");
        var td_check = $(".is_wishlist_default").parent();
        $(".is_wishlist_default").remove();
        td_check.append('<a href="#" onclick="javascript:event.preventDefault();(window.BlockWishlistModule.front.default(\'' + old_default_id + '\', \'' + old_default_id.replace("wishlist_", "") + '\'));"><i class="icon icon-square"></i></a>');
        var td_default = $("#" + id + " > .wishlist_default");
        $("#" + id + " > .wishlist_default > a").remove();
        td_default.append('<p class="is_wishlist_default"><i class="icon icon-check-square"></i></p>');
      }
    });
  };
  /**
   * Delete wishlist
   *
   * @return boolean succeed
   */
  window.BlockWishlistModule.front.delete = function (id, id_wishlist, msg) {
    var res = confirm(msg);
    if (res === false) {
      return (false);
    }

    $.ajax({
      type: 'GET',
      dataType: "json",
      url: config.urls.mywishlist,
      headers: { "cache-control": "no-cache" },
      data: {
        rand: new Date().getTime(),
        deleted: 1,
        myajax: 1,
        id_wishlist: id_wishlist,
        action: 'deletelist'
      },
      success: function (data) {
        var mywishlist_siblings_count = $('#' + id).siblings().length;
        $('#' + id).fadeOut('slow').remove();
        $("#block-order-detail").html('');
        if (mywishlist_siblings_count == 0)
          $("#block-history").remove();

        if (data.id_default) {
          var td_default = $("#wishlist_" + data.id_default + " > .wishlist_default");
          $("#wishlist_" + data.id_default + " > .wishlist_default > a").remove();
          td_default.append('<p class="is_wishlist_default"><i class="icon icon-check-square"></i></p>');
        }
      }
    });
  };
  /**
   * Show wishlist product managment page
   *
   * @return void
   */
  window.BlockWishlistModule.front.manage = function (id, action, id_wishlist, id_product, id_product_attribute, quantity, priority) {
    $.ajax({
      type: 'GET',
      url: config.urls.managewishlist,
      headers: { 'cache-control': 'no-cache' },
      data: {
        rand: new Date().getTime(),
        action: action,
        id_wishlist: id_wishlist,
        id_product: id_product,
        id_product_attribute: id_product_attribute,
        quantity: quantity,
        priority: priority,
        refresh: true
      },
      success: function () {
        if (action === 'delete')
          $('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('fast');
        else if (action == 'update') {
          $('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('fast');
          $('#wlp_' + id_product + '_' + id_product_attribute).fadeIn('fast');
        }
        nb_products = 0;
        $("[id^='quantity']").each(function (index, element) {
          nb_products += parseInt(element.value, 10);
        });
        $("#wishlist_" + id_wishlist).children('td').eq(1).html(nb_products);
      }
    });
  };
  /**
   * Hide/Show bought product
   *
   * @return void
   */
  window.BlockWishlistModule.front.visibility = function (bought_class, id_button) {
    if ($('#hide' + id_button).is(':hidden')) {
      $('.' + bought_class).slideDown('fast');
      $('#show' + id_button).hide();
      $('#hide' + id_button).css('display', 'block');
    } else {
      $('.' + bought_class).slideUp('fast');
      $('#hide' + id_button).hide();
      $('#show' + id_button).css('display', 'block');
    }
  };
  /**
   * Send wishlist by email
   *
   * @return void
   */
  window.BlockWishlistModule.front.send = function (id, id_wishlist, id_email) {
    $.post(
      baseDir + 'modules/blockwishlist/sendwishlist.php',
      {
        token: static_token,
        id_wishlist: id_wishlist,
        email1: $('#' + id_email + '1').val(),
        email2: $('#' + id_email + '2').val(),
        email3: $('#' + id_email + '3').val(),
        email4: $('#' + id_email + '4').val(),
        email5: $('#' + id_email + '5').val(),
        email6: $('#' + id_email + '6').val(),
        email7: $('#' + id_email + '7').val(),
        email8: $('#' + id_email + '8').val(),
        email9: $('#' + id_email + '9').val(),
        email10: $('#' + id_email + '10').val()
      },
      function (data) {
        if (data) {
          if (!!$.prototype.fancybox) {
            $.fancybox.open([
              {
                type: 'inline',
                autoScale: true,
                minHeight: 30,
                content: '<p class="fancybox-error">' + data + '</p>'
              }
            ], {
              padding: 0
            });
          } else {
            alert(data);
          }
        } else {
          window.BlockWishlistModule.front.visibility(id, 'hideSendWishlist');
        }
      }
    );
  };
  window.BlockWishlistModule.front.addId = function (id) {
    if ($.inArray(parseInt(id, 10), window.BlockWishlistModule.idProducts) === -1) {
      window.BlockWishlistModule.idProducts.push(parseInt(id, 10));
    }
  };
  window.BlockWishlistModule.front.removeId = function (id) {
    window.BlockWishlistModule.idProducts.splice($.inArray(parseInt(id, 10), window.BlockWishlistModule.idProducts), 1)
  };
  window.BlockWishlistModule.front.refreshStatus = function () {
    $('.addToWishlist').each(function () {
      if ($.inArray(parseInt($(this).prop('rel'), 10), wishlistProductsIds) != -1)
        $(this).addClass('checked');
      else
        $(this).removeClass('checked');
    });
  };
  window.BlockWishlistModule.front.changeProduct = function (id_product, id_product_attribute, id_old_wishlist, id_new_wishlist) {
    var quantity = $('#quantity_' + id_product + '_' + id_product_attribute).val();

    $.ajax({
      type: 'GET',
      url: config.urls.mywishlist,
      headers: { 'cache-control': 'no-cache' },
      dataType: 'json',
      data: {
        id_product: id_product,
        id_product_attribute: id_product_attribute,
        quantity: quantity,
        priority: $('#priority_' + id_product + '_' + id_product_attribute).val(),
        id_old_wishlist: id_old_wishlist,
        id_new_wishlist: id_new_wishlist,
        myajax: 1,
        action: 'productchangewishlist'
      },
      success: function (data) {
        console.log(data);
        console.log(data.msg);
        if (data.success == true) {
          $('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('slow');
          $('#wishlist_' + id_old_wishlist + ' td:nth-child(2)').text($('#wishlist_' + id_old_wishlist + ' td:nth-child(2)').text() - quantity);
          $('#wishlist_' + id_new_wishlist + ' td:nth-child(2)').text(+$('#wishlist_' + id_new_wishlist + ' td:nth-child(2)').text() + +quantity);
        }
        else {
          if (!!$.prototype.fancybox) {
            $.fancybox.open([
              {
                type: 'inline',
                autoScale: true,
                minHeight: 30,
                content: '<p class="fancybox-error">' + data.error + '</p>'
              }
            ], {
              padding: 0
            });
          }
        }
      }
    });
  };

  $(document).ready(function () {
      window.BlockWishlistModule.front.refreshStatus();

      $(document).on('change', 'select[name=wishlists]', function () {
        window.BlockWishlistModule.front.default('wishlist_block_list', $(this).val());
      });


      $('.wishlist').each(function () {
        current = $(this);
        $(this).children('.wishlist_button_list').popover({
          html: true,
          content: function () {
            return current.children('.popover-content').html();
          }
        });
      });
    }
  );
}());
