<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center" wire:model="category" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Sub Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center" wire:model="subcategory" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Product') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center" wire:model="product" disabled>
        </div>
        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Item Name') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center" wire:model="item_name" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Bin Location') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center" wire:model="bin_location" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Quantity On-Hand') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center" wire:model="quantity_on_hand" disabled>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Average Usage') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center @error('average_usage') is-invalid @enderror" autofocus placeholder="Average Usage" wire:model="average_usage" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:keyup="calculateReorderThreshold">
          <span class="text-danger">@error('average_usage') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-1 col-form-label text-right text-wrap pr-4">{{ __('Average Lead Time') }}<span class="text-danger"> * </span> </label>
        <div class="col-sm-5 container">
          <div class="row">
            <input type="text" class="col-sm-3 form-control text-center @error('average_lead_time') is-invalid @enderror" autofocus placeholder="Lead Time" wire:model="average_lead_time" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:keyup="calculateReorderThreshold">
            <input type="text" class="col-sm-8 form-control text-center @error('time_unit') is-invalid @enderror" autofocus placeholder="Lead Time" wire:model="time_unit" readonly>
            {{-- <select id="time-unit" wire:model="time_unit" class="col-sm-8 form-control text-center @error('time_unit') is-invalid @enderror" wire:change="calculateReorderThreshold" style="height: 40px">
              <option value="" selected hidden>-- Select Time Unit --</option>
              <option value="{{ strtoupper("Nanosecond") }}">{{ strtoupper("Nanosecond") }}</option>
              <option value="{{ strtoupper("Millisecond") }}">{{ strtoupper("Millisecond") }}</option>
              <option value="{{ strtoupper("Second") }}">{{ strtoupper("Second") }}</option>
              <option value="{{ strtoupper("Minute") }}">{{ strtoupper("Minute") }}</option>
              <option value="{{ strtoupper("Hour") }}">{{ strtoupper("Hour") }}</option>
              <option value="{{ strtoupper("Day") }}">{{ strtoupper("Day") }}</option>
              <option value="{{ strtoupper("Week") }}">{{ strtoupper("Week") }}</option>
              <option value="{{ strtoupper("Month") }}">{{ strtoupper("Month") }}</option>
              <option value="{{ strtoupper("Year") }}">{{ strtoupper("Year") }}</option>
              <option value="{{ strtoupper("Decade") }}">{{ strtoupper("Decade") }}</option>
              <option value="{{ strtoupper("Century") }}">{{ strtoupper("Century") }}</option>
              <option value="{{ strtoupper("Millennium") }}">{{ strtoupper("Millennium") }}</option>
            </select> --}}
          </div>
          <div class="row">
            <div class="col-sm-12">
              <span class="text-danger">@error('average_lead_time') {{ "* ". $message . "!" }} @enderror</span><br>
              <span class="text-danger">@error('time_unit') {{ "* ". $message . "!" }} @enderror</span>
            </div>
          </div>
        </div>

        {{-- <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Safety Stock') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control text-center @error('safety_stock') is-invalid @enderror" autofocus placeholder="Re-Order" wire:model="safety_stock" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          <span class="text-danger">@error('safety_stock') {{ "* ". $message . "!" }} @enderror</span>
        </div> --}}

        <label for="" class="col-sm-1 col-form-label text-right text-wrap">{{ __('Re-Order Threshold') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control text-center @error('reorder') is-invalid @enderror" autofocus placeholder="Re-Order" wire:model="reorder" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
          <span class="text-danger">@error('reorder') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <a href="{{ route('reorder.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;">
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
