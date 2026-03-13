<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="category" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Sub Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="subcategory" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Product') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="product" disabled>
        </div>

      </div>

      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Item Name') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="item_name" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('UOM') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="uom" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Remaining Quantity') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="quantity">
        </div>

      </div>

      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Farm Location') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="farm" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Department / Division') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center" wire:model="department" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Withdraw Qty') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center @error('reduced_quantity') is-invalid @enderror @if($quantity < $reduced_quantity) is-invalid @endif" wire:keyup="sub_to_quantity" autofocus placeholder="Withdraw Qty" wire:model="reduced_quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
          <span class="text-danger">@error('reduced_quantity') {{ "* ". $message . "!" }} @enderror</span>
          @if($quantity < $reduced_quantity)
            <span class="text-danger">{{ __("* The Inputted Quantity Is Greater Than The Remaining Quantity.") }}</span>
          @endif
          {{-- @if($this->reduced_quantity == 0) <span class="text-danger">{{ __("* Cannot Input Number Zero(0)!") }}</span> @endif --}}
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-nowrap">{{ __('Reason') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <textarea class="form-control text-center @error('reason') is-invalid @enderror" autofocus placeholder="Reason" wire:model="reason" style="height: 150px;"></textarea>

          <span class="text-danger">@error('reason') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <a href="{{ route('farmitem.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;" @if($quantity < $reduced_quantity) disabled @endif {{-- @if($this->reduced_quantity==0)disabled@endif --}}>
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
