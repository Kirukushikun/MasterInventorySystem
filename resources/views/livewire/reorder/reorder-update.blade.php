<div class="card mims-form-card">
  <form wire:submit.prevent method="post" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="mims-form-grid">

        <div class="mims-form-field">
          <label for="">{{ __('Category') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center" wire:model="category" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Sub Category') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center" wire:model="subcategory" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Product') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center" wire:model="product" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Item Name') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center" wire:model="item_name" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Bin Location') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center" wire:model="bin_location" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Quantity On-Hand') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center" wire:model="quantity_on_hand" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Average Usage') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center @error('average_usage') is-invalid @enderror" autofocus placeholder="Average Usage" wire:model="average_usage" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:keyup="calculateReorderThreshold">
          <span class="text-danger">@error('average_usage') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Average Lead Time') }}<span class="text-danger"> *</span></label>
          <div class="row no-gutters">
            <div class="col-4 pr-1">
              <input type="text" class="form-control text-center @error('average_lead_time') is-invalid @enderror" autofocus placeholder="Lead Time" wire:model="average_lead_time" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:keyup="calculateReorderThreshold">
            </div>
            <div class="col-8">
              <input type="text" class="form-control text-center @error('time_unit') is-invalid @enderror" autofocus placeholder="Time Unit" wire:model="time_unit" readonly>
            </div>
          </div>
          <span class="text-danger">@error('average_lead_time') {{ "* ". $message . "!" }} @enderror</span>
          <span class="text-danger">@error('time_unit') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Re-Order Threshold') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control text-center @error('reorder') is-invalid @enderror" autofocus placeholder="Re-Order" wire:model="reorder" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
          <span class="text-danger">@error('reorder') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
    </div>
    <div class="mims-form-actions">
      <a href="{{ route('reorder.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
      <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;" type="button">
        <i class="fas fa-plus"></i> Update
      </button>
    </div>
  </form>
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
          text: "You want to set the Re-Order?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, set it!',
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
