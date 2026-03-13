<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center" autofocus wire:model="category_id" wire:change="set_subcategory" disabled>
            <option class="text-center" hidden selected>-- Select Category --</option>
            @foreach($category_list as $c)
              <option class="text-center" value="{{ $c->id }}">{{ $c->category_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('category_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Sub Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center" autofocus wire:model="subcategory_id" wire:change="set_product" disabled>
            <option class="text-center" hidden selected>-- Select Sub Category --</option>
            @foreach($subcategory_list as $c)
              <option class="text-center" value="{{ $c->id }}">{{ $c->subcategory_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('subcategory_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Product') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center" autofocus wire:model="product_id" wire:change="set_item" disabled>
            <option class="text-center" hidden selected>-- Select Product --</option>
            @foreach($product_list as $c)
              <option class="text-center" value="{{ $c->id }}">{{ $c->product_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('product_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Item Name') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center" autofocus wire:model="item_name_id" disabled>
            <option class="text-center" hidden selected>-- Select Item --</option>
            @foreach($item_name_list as $c)
              <option class="text-center" value="{{ $c->id }}">{{ $c->item_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('item_name_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Bin Location') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center" autofocus wire:model="location_id" disabled>
            <option class="text-center" hidden selected>-- Select Bin Location --</option>
            @foreach($bin_location_list as $bloc)
              <option class="text-center" value="{{ $bloc->id }}">{{ $bloc->location_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('location_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Supplier') }}</label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center" autofocus wire:model="location_id" disabled>
            <option class="text-center" hidden selected>-- Select Supplier --</option>
            @foreach($supplier_list as $loc)
              <option class="text-center" value="{{ $loc->id }}">{{ $loc->supplier_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('supplier_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Description.') }}</label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center  @error('model_number') is-invalid @enderror" autofocus wire:model="model_number" placeholder="Description">
          <span class="text-danger">@error('model_number') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Item Card.') }}</label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center  @error('item_number') is-invalid @enderror" autofocus wire:model="item_number" placeholder="Item Card">
          <span class="text-danger">@error('item_number') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Unit/Msrmnt') }}<span class="text-danger"> * </span></label>
        <div class="col-sm-3">
          <select class="form-control text-center  text-center " autofocus wire:model="uom_id">
            <option class="text-center" hidden selected>-- Select U/M --</option>
            @foreach($uom_list as $uom)
              <option class="text-center" value="{{ $uom->id }}">{{ $uom->terminology }} - {{ $uom->abbreviation }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('uom_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Quantity On-Hand') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="number" class="form-control text-center @error('quantity') is-invalid @enderror" autofocus placeholder="quantity" wire:model="quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
          <span class="text-danger">@error('quantity') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        @inject('acc', '\App\Http\Controllers\AccessController')
        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Actual Quantity') }}</label>
        <div class="col-sm-5">
          <input type="number" class="form-control text-center @error('additional_quantity') is-invalid @enderror" autofocus placeholder="Additional Quantity" wire:keyup="add_to_quantity" wire:model="additional_quantity"
          @if(!$acc::checkAccess(\Auth::id(), 'inventory_diminish')) oninput="this.value = this.value.replace(/[^0-9]/g, '')" @endif>
          <span class="text-danger">@error('additional_quantity') {{ "* ". $message . "!" }} @enderror</span>
          @if($this->additional_quantity == 0)
            <span class="text-danger">* Cannot Input Number Zero(0)!</span>
          @endif
        </div>

        {{-- <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Re-Order Point') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center @error('reorder_threshold') is-invalid @enderror" autofocus wire:model="reorder_threshold" placeholder="Re-Order Threshold">
          <span class="text-danger">@error('reorder_threshold') {{ "* ". $message . "!" }} @enderror</span>
        </div> --}}
      </div>

      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Unit Price') }}</label>
        <div class="col-sm-3">
          <input type="number" step="any" class="form-control @error('unit_price') is-invalid @enderror" autofocus placeholder="Unit Price" wire:model="unit_price" wire:keyup="add_total_cost">

          <span class="text-danger">@error('unit_price') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Purchase Cost') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="number" step="any" class="form-control @error('purchase_cost') is-invalid @enderror" autofocus placeholder="Purchase Cost" wire:model="purchase_cost" readonly>
          <span class="text-danger">@error('purchase_cost') {{ "* ". $message . "!" }} @enderror</span>
        </div>


        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Purchase Date') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" autofocus placeholder="Purchase Date" wire:model="purchase_date">
          <span class="text-danger">@error('purchase_date') {{ "* ". $message . "!" }} @enderror</span>
        </div>


      </div>
      <div class="form-group row">


        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Expiry Date') }}</label>
        <div class="col-sm-3">
          <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" autofocus placeholder="Expiry Date" wire:model="expiry_date">
          <span class="text-danger">@error('expiry_date') {{ "* ". $message . "!" }} @enderror</span>
        </div>
        <div class="col-sm-8">
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Upload Item Image.') }}</label>
        <div class="col-sm-11">
          <input type="file" class="text-center  @error('item_image') is-invalid @enderror" autofocus wire:model="item_image">
          <span class="text-danger">@error('item_image') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        @if ($item_image && $item_image && strpos($item_image->getMimeType(), 'image/') === 0)
          <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Image Preview.') }}</label>
          <div class="col-sm-2">
            <img src="{{ $item_image->temporaryUrl() }}" alt="Image Preview Not Available" class="img-thumbnail" style="width: 200px; height: 200px; max-width: 200px; max-height: 200px;">
          </div>
        @else
          @if (!$is_editting)
            <div class="col-sm-1"></div>
            <div class="col-sm-2">
              <p class="text-danger">Please Select A Valid Image (Uploaded File Format Must Be: jpeg,png,jpg,gif,svg).</p>
            </div>
          @endif
        @endif

        @if ($is_editting && !$item_image)
            <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Uploaded Image.') }}</label>
            <div class="col-sm-2">
                <img src="{!! $item_id_img !!}" alt="Image Preview" style="width: 200px; height: 200px; max-width: 200px; max-height: 200px;">
            </div>
        @endif

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Remarks') }}</label>
        <div class="col-sm-8">
          <textarea class="form-control @error('remarks') is-invalid @enderror" autofocus placeholder="Remarks" wire:model="remarks" style="height: 200px;"></textarea>

          <span class="text-danger">@error('remarks') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
      {{-- @php
        var_dump($sampel);
      @endphp --}}
    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <a href="{{ route('item.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;" @if($this->additional_quantity == 0) disabled @endif>
      <i class="fas fa-plus"></i> Update
    </button>
  </div>
</div>

@push('scripts')
  <script>
    Livewire.on('showSuccessDialog', data => {
      Swal.fire(
        data.title,
        data.text,
        'success'
      );
    });
    $(document).ready(function() {
      $("#showNotif").click(function() {
        Swal.fire({
          title: 'Are you sure?',
          text: "You want to edit this item?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, update it!',
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
            Livewire.emit('editRecord'); // emit an event to submit the Livewire form
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
