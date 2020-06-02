<?php

class WishlistRepository
{
    public function getAllWishlistsProductID()
    {
        return Db::getInstance()
            ->getRow('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'wishlist_product`');
    }
}
