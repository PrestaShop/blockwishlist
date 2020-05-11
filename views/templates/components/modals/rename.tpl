<div
  class="wishlist-rename"
  data-url="{$url}"
  data-title="{l s='Rename wishlist' mod='blockwishlist'}"
  data-label="{l s='Wishlist name' mod='blockwishlist'}"
  data-placeholder="{l s='Wishlist name' mod='blockwishlist'}"
  data-cancel-text="{l s='Cancel' mod='blockwishlist'}"
  data-rename-text="{l s='Rename wishlist' mod='blockwishlist'}"
>
  <div
    class="wishlist-modal modal fade"
    {literal}
      :class="{show: !isHidden}"
    {/literal}
    tabindex="-1"
    role="dialog"
    aria-modal="true"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">((title))</h5>
          <button
            type="button"
            class="close"
            @click="toggleModal"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group form-group-lg">
            <label class="form-control-label" for="input2"
              >((label))</label
            >
            <input
              type="text"
              class="form-control form-control-lg"
              v-model="value"
              id="input2"
            />
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="modal-cancel btn btn-secondary"
            data-dismiss="modal"
            @click="toggleModal"
          >
            ((cancelText))
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="renameWishlist"
          >
            ((renameText))
          </button>
        </div>
      </div>
    </div>
  </div>

  <div 
    class="modal-backdrop fade"
    {literal}
      :class="{in: !isHidden}"
    {/literal}
  >
  </div>
</div>

