<div class="card mims-form-card">
  @foreach($for_inject_list as $fil_key => $fil)
    @inject($fil, $fil_key)
  @endforeach
  <form wire:submit.prevent method="post" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="mims-form-grid">
        <div class="mims-form-section">
          <h3 class="mims-form-section-title">Item Classification</h3>
        </div>

        <div class="mims-form-field" wire:poll>
          <div class="mims-field-head">
            <label for="">{{ __('Category') }}<span class="text-danger"> *</span></label>
            <a onclick="openCategoryInNewTab();" class="mims-field-settings" title="Manage category values"><i class="fas fa-cog"></i></a>
          </div>
          @php
            $this->category_list = $ct::where('active_status', 1)->get();
          @endphp
          <div class="mims-input-action">
            <select class="form-control text-center text-center" autofocus wire:model="category_id" wire:change="set_subcategory"
            @if((!is_null($subcategory_id) || $subcategory_id != null)) disabled @endif>
              <option class="text-center" hidden selected>-- Select Category --</option>
              @foreach($category_list as $c)
                <option class="text-center" value="{{ $c->id }}">{{ $c->category_name }}</option>
              @endforeach
            </select>
          </div>
          <span class="text-danger">@error('category_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        @if(!is_null($category_id) || $category_id != null)

          <div class="mims-form-field">
            <div class="mims-field-head">
              <label for="">{{ __('Sub Category') }}<span class="text-danger"> *</span></label>
              <a onclick="openSubCategoryInNewTab();" class="mims-field-settings" title="Manage sub category values"><i class="fas fa-cog"></i></a>
            </div>
            @php
              $this->subcategory_list = $sct::where('category_id', $this->category_id)->where('active_status', 1)->get();
            @endphp
            <div class="mims-input-action">
              <select class="form-control text-center  text-center" autofocus wire:model="subcategory_id" wire:change="set_product"
              @if(!is_null($product_id) || $product_id != null) disabled @endif>
                <option class="text-center" hidden selected>-- Select Sub Category --</option>
                @foreach($subcategory_list as $c)
                  <option class="text-center" value="{{ $c->id }}">{{ $c->subcategory_name }}</option>
                @endforeach
              </select>
            </div>
            <span class="text-danger">@error('subcategory_id') {{ "* ". $message . "!" }} @enderror</span>
          </div>

          @if((!is_null($category_id) || $category_id != null) && (!is_null($subcategory_id) || $subcategory_id != null))

            <div class="mims-form-field">
              <div class="mims-field-head">
                <label for="">{{ __('Product') }}<span class="text-danger"> *</span></label>
                <a onclick="openProductInNewTab();" class="mims-field-settings" title="Manage product values"><i class="fas fa-cog"></i></a>
              </div>
              @php
                $this->product_list = $prd::where('category_id', $this->category_id)
                                ->where('subcategory_id', $this->subcategory_id)
                                ->where('active_status', 1)
                                ->get();
              @endphp
              <div class="mims-input-action">
                <select class="form-control text-center  text-center" autofocus wire:model="product_id" wire:change="set_item"
                    @if((!is_null($item_name_id) || $item_name_id != null)) disabled @endif>
                  <option class="text-center" hidden selected>-- Select Product --</option>
                  @foreach($product_list as $c)
                    <option class="text-center" value="{{ $c->id }}">{{ $c->product_name }}</option>
                  @endforeach
                </select>
              </div>
              <span class="text-danger">@error('product_id') {{ "* ". $message . "!" }} @enderror</span>
            </div>

            @if((!is_null($category_id) || $category_id != null) &&
                (!is_null($subcategory_id) || $subcategory_id != null) &&
                (!is_null($product_id) || $product_id != null))
              <div class="mims-form-field">
                <div class="mims-field-head">
                  <label for="">{{ __('Item Name') }}<span class="text-danger"> *</span></label>
                  <a onclick="openItemNameInNewTab();" class="mims-field-settings" title="Manage item name values"><i class="fas fa-cog"></i></a>
                </div>
                @php
                    $this->item_name_list = $itn::where('category_id', $this->category_id)
                        ->where('subcategory_id', $this->subcategory_id)
                        ->where('product_id', $this->product_id)
                        ->where('active_status', 1)
                        ->get();
                @endphp
                <div class="mims-input-action">
                  <select class="form-control text-center text-center @error('item_name_id') is-invalid @enderror" autofocus wire:model="item_name_id"
                  {{-- @if((!is_null($item_name_id) || $item_name_id != null)) disabled @endif --}} wire:change="find_item_data">
                    <option class="text-center" hidden selected>-- Select Item --</option>
                    @foreach($item_name_list as $c)
                      <option class="text-center" value="{{ $c->id }}">{{ $c->item_name }}</option>
                    @endforeach
                  </select>
                </div>
                <span class="text-danger">@error('item_name_id') {{ "* ". $message . "!" }} @enderror</span>
              </div>

              @if((!is_null($item_name_id) || $item_name_id != null))
                <div class="mims-form-section">
                  <h3 class="mims-form-section-title">Stock Details</h3>
                </div>

                <div class="mims-form-field">
                  <div class="mims-field-head">
                    <label for="">{{ __('Bin Location') }}<span class="text-danger"> *</span></label>
                    <a onclick="openBinLocationInNewTab();" class="mims-field-settings" title="Manage bin location values"><i class="fas fa-cog"></i></a>
                  </div>
                  @php
                    $this->bin_location_list = $bl::where('active_status', 1)->get();
                  @endphp
                  <div class="mims-input-action">
                    <select class="form-control text-center  text-center" autofocus wire:model="location_id" {{--wire:change="setSeries"--}}>
                      <option class="text-center" hidden selected>-- Select Bin Location --</option>
                      @foreach($bin_location_list as $bloc)
                        <option class="text-center" value="{{ $bloc->id }}">{{ $bloc->location_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <span class="text-danger">@error('location_id') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <div class="mims-field-head">
                    <label for="">{{ __('Supplier') }}</label>
                    <a onclick="openSupplierInNewTab();" class="mims-field-settings" title="Manage supplier values"><i class="fas fa-cog"></i></a>
                  </div>
                  @php
                    $this->supplier_list = $sp::where('active_status', 1)->get();
                  @endphp
                  <div class="mims-input-action">
                    <select class="form-control text-center text-center" autofocus wire:model="supplier_id" {{--wire:change="setSeries"--}}>
                      <option class="text-center" hidden selected>-- Select Supplier --</option>
                      @foreach($supplier_list as $sl)
                        <option class="text-center" value="{{ $sl->id }}">{{ $sl->supplier_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <span class="text-danger">@error('supplier_id') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Description.') }}</label>
                  <input type="text" class="form-control text-center  @error('model_number') is-invalid @enderror" autofocus wire:model="model_number" placeholder="Description">
                  <span class="text-danger">@error('model_number') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Item Card.') }}</label>
                  <input type="text" class="form-control text-center  @error('item_number') is-invalid @enderror" autofocus wire:model="item_number" placeholder="Item Card">
                  <span class="text-danger">@error('item_number') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Quantity On-Hand') }}<span class="text-danger"> *</span></label>
                  <input type="number" class="form-control text-center" autofocus placeholder="Quantity On-Hand" wire:model="quantity" disabled>
                </div>
                @inject('acc', '\App\Http\Controllers\AccessController')
                <div class="mims-form-field">
                  <label for="">{{ $this->temp_quantity == 0 ? __('Quantity') : __('Additional Quantity') }}<span class="text-danger"> *</span></label>
                  <input type="number" class="form-control text-center @error('quantity_to_add') is-invalid @enderror" autofocus placeholder="{{ $this->temp_quantity == 0 ? __('Quantity') : __('Additional Quantity') }}" wire:model="quantity_to_add" wire:keyup="add_to_quantity"
                  @if(!$acc::checkAccess(\Auth::id(), 'inventory_diminish')) oninput="this.value = this.value.replace(/[^0-9]/g, '')" @endif>
                  <span class="text-danger">@error('quantity_to_add') {{ "* ". $message . "!" }} @enderror</span>
                  {{-- @if($this->quantity_to_add == 0)
                    <span class="text-danger">* Cannot Input Number Zero(0)!</span>
                  @endif --}}
                </div>

                <div class="mims-form-field">
                  <div class="mims-field-head">
                    <label for="">{{ __('Unit/Msrmnt') }}<span class="text-danger"> * </span></label>
                    <a onclick="openUomInNewTab();" class="mims-field-settings" title="Manage unit values"><i class="fas fa-cog"></i></a>
                  </div>
                  @php
                    $this->uom_list = $uom::where('active_status', 1)->get();
                  @endphp
                  <div class="mims-input-action">
                    <select class="form-control text-center  text-center " autofocus wire:model="uom_id" {{--wire:change="setSeries"--}}>
                      <option class="text-center" hidden selected>-- Select U/M --</option>
                      @foreach($uom_list as $uom)
                        <option class="text-center" value="{{ $uom->id }}">{{ $uom->terminology }} - {{ $uom->abbreviation }}</option>
                      @endforeach
                    </select>
                  </div>
                  <span class="text-danger">@error('uom_id') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-section">
                  <h3 class="mims-form-section-title">Purchase & Attachment</h3>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Purchase Date') }}<span class="text-danger"> *</span></label>
                  <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" autofocus placeholder="Purchase Date" wire:model="purchase_date">

                  <span class="text-danger">@error('purchase_date') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Expiry Date') }}</label>
                  <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" autofocus placeholder="Expiry Date" wire:model="expiry_date">
                  <span class="text-danger">@error('expiry_date') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Unit Price') }}</label>
                  <input type="number" step="any" class="form-control @error('unit_price') is-invalid @enderror" autofocus placeholder="Unit Price" wire:model="unit_price" wire:keyup="add_total_cost">

                  <span class="text-danger">@error('unit_price') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field">
                  <label for="">{{ __('Purchase/Total Cost') }}</label>
                  <input type="number" step="any" class="form-control @error('purchase_cost') is-invalid @enderror" autofocus placeholder="Purchase Cost" wire:model="purchase_cost" readonly>

                  <span class="text-danger">@error('purchase_cost') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                <div class="mims-form-field mims-form-field--full">
                  <label for="">{{ __('Upload Item Image.') }}</label>
                  <input type="file" class="text-center  @error('item_image') is-invalid @enderror" autofocus wire:model="item_image">
                  <span class="text-danger">@error('item_image') {{ "* ". $message . "!" }} @enderror</span>
                </div>

                @if ($item_image && $item_image && strpos($item_image->getMimeType(), 'image/') === 0)
                  <div class="mims-form-field">
                    <label for="">{{ __('Image Preview.') }}</label>
                    <img src="{{ $item_image->temporaryUrl() }}" alt="Image Preview Not Available" class="img-thumbnail mims-image-preview">
                  </div>
                @else
                  @if (!$is_editting)
                    <div class="mims-form-field">
                      <label for="">{{ __('Image Preview.') }}</label>
                      <p class="text-danger">Please Select A Valid Image (Uploaded File Format Must Be: jpeg,png,jpg,gif,svg).</p>
                    </div>
                  @endif
                @endif

                @if ($is_editting && !$item_image)
                    <div class="mims-form-field">
                        <label for="">{{ __('Uploaded Image.') }}</label>
                        <img src="{!! $item_id_img !!}" alt="Image Preview" class="mims-image-preview">
                    </div>
                @endif

                <div class="mims-form-field mims-form-field--full">
                  <label for="">{{ __('Remarks') }}</label>
                  <textarea class="form-control @error('remarks') is-invalid @enderror" autofocus placeholder="Remarks" wire:model="remarks" style="height: 200px;"></textarea>

                  <span class="text-danger">@error('remarks') {{ "* ". $message . "!" }} @enderror</span>
                </div>
              @endif
            @endif
          @endif
        @endif
      </div>
    </div>
    <div class="mims-form-actions mims-form-actions--split">
      <button class="btn btn-warning text-center" wire:click="clear_fields" type="button"><i class="fas fa-eraser"></i> Clear Fields</button>
      <button id="showNotif" class="btn btn-primary text-center form-control" type="button" {{-- @if($this->quantity_to_add == 0) disabled @endif --}}>
        <i class="fas fa-plus"></i> {{ __(' Add ') }}
      </button>
    </div>
  </form>
</div>

@push('scripts')
  <script>
    @foreach($func_add_list as $fal_key => $fal)
      function open{{ $fal_key }}InNewTab() {
        Swal.fire({
          title: 'Confirmation',
          text: 'Are you sure you want to leave this page?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes',
          cancelButtonText: 'No',
          cancelButtonColor: '#d33',
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('{{ route($fal) }}?via=item', '_blank');
          }
        });
      }
    @endforeach
    $(document).ready(function() {
      $("#showNotif").click(function() {
        Swal.fire({
          title: 'Are you sure?',
          text: "You want to add this item?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, add it!',
          allowOutsideClick: false,
          allowEscapeKey: false
        }).then((result) => {
          if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
              title: 'Action Cancelled',
              icon: 'error',
              timer: 2000,
              timerProgressBar: true,
              showConfirmButton: true
            });
          } else {
            Livewire.emit('createNewRecord'); // emit an event to submit the Livewire form
          }
        });
      });
    });

    @if (session()->has('success'))

      Swal.fire(
        'Success!',
        '{{ session('success') }}',
        'success'
      );

    @elseif(session()->has('failed'))

      Swal.fire(
        'Failed!',
        '{{ session('failed') }}',
        'error'
      );

    @endif
  </script>
@endpush
