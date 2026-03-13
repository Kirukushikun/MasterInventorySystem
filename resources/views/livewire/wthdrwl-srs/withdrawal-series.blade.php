<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">
        <label for="" class="col-sm-2 col-form-label text-right text-nowrap">{{ __('Series From:') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="number" class="form-control @if($to != null || !is_null($to) || !empty($to)) @if($to < $from) @error('from') is-invalid @enderror @endif @endif" autofocus placeholder="1" wire:model="from" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          @if($to != null || !is_null($to) || !empty($to))
            @if($to < $from)
              <span class="text-danger">@error('from') {{ "* ". $message . "!" }} @enderror</span>
            @endif
          @endif
        </div>
        <label for="" class="col-sm-2 col-form-label text-right text-nowrap">{{ __('Series To:') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="number" class="form-control @error('to') is-invalid @enderror" autofocus placeholder="100" wire:model="to" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          <span class="text-danger">@error('to') {{ "* ". $message . "!" }} @enderror</span>
        </div>
      </div>
      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Farm Location') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center @error('farm_location_id') is-invalid @enderror" autofocus wire:model.defer="farm_location_id">

            <option class="text-center" hidden selected>Select Farm Location</option>
            @foreach($farm_location_list as $fll)
              <option class="text-center" value="{{ $fll->id }}">{{ $fll->farm_location }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('farm_location_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Div/Dept') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control text-center @error('department_division_id') is-invalid @enderror" autofocus wire:model.defer="department_division_id">

            <option class="text-center" hidden selected>Select Department/Division</option>
            @foreach($department_division_list as $ddl)
              <option class="text-center" value="{{ $ddl->id }}">{{ $ddl->department_division }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('department_division_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>
      </div>
    </div>
  </form>
  <div class="container">
    <div class="form-group row">
      <div class="col-md-4 ml-auto">
        <button id="showNotif" class="btn btn-primary form-control">
          <i class="fas fa-plus"></i> ASSIGN
        </button>
      </div>
    </div>
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
