<nav class="wishlist-pagination pagination">
  <div class="col-md-4">
    {l s='Showing' mod='blockwishlist'} ((minShown)) - ((maxShown)) {l s='de' mod='blockwishlist'} ((total)) {l s='item(s)' mod='blockwishlist'}
  </div>

  <div class="col-md-6 offset-md-2 pr-0">
    <ul class="page-list clearfix text-sm-center">
      <li>
        <a rel="prev" v-if="currentPage !== 1" @click="paginate(currentPage - 1)" class="previous js-search-link">
          <i class="material-icons">keyboard_arrow_left</i> {l s='Previous' mod='blockwishlist'} 
        </a>
      </li>

      <li :class="{literal}{current: currentPage === page}{/literal}" v-for="page of pageNumber">
        <a class="js-search-link" @click="paginate(page)" :class="{literal}{disabled: currentPage === page}{/literal}">
          ((page))
        </a>
      </li>
      <li>
        <a rel="next" v-if="currentPage !== pageNumber" @click="paginate(currentPage + 1)" class="next js-search-link">
          {l s='Next' mod='blockwishlist'} <i class="material-icons"></i>
        </a>
      </li>
    </ul>
  </div>
</nav>
