<div class="card mims-form-card">
  <form wire:submit.prevent method="post" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="mims-form-grid">
        <div class="mims-form-field">
          <label for="">{{ __('Deparment/Division') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('Deparment/Division') is-invalid @enderror
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" autofocus placeholder="Deparment/Division" wire:model="department_division">

          <span class="text-danger">
            @error('department_division') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Dept. Code') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" autofocus placeholder="Dept. Code" wire:model="abbreviation">
          <span class="text-danger">@error('abbreviation') {{ "* ". $message . "!" }} @enderror</span>
        </div>
      </div>
    </div>
    <div class="mims-form-actions">
      <button id="showNotif" class="btn btn-primary form-control" type="button">
        <i class="fas fa-plus"></i> Create
      </button>
    </div>
  </form>
</div>

@push('scripts')
  <script>
    @if(session()->has('already_exist'))
        Swal.fire(
        'Failed!',
        '{!! session('already_exist') !!}',
        'error'
      );
    @endif
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
