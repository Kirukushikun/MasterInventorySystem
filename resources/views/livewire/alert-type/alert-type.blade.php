<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">
        <label for="" class="col-sm-2 col-form-label text-right">
          {{ __('Notification Type') }}<span class="text-danger"> *</span>
        </label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('name') is-invalid @enderror 
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" 
            autofocus placeholder="Alert Type" wire:model="name">

          <span class="text-danger">
            @error('name') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Description') }}</label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('description') is-invalid @enderror" autofocus placeholder="1" wire:model="description">
          @error('description')
            <p class="text-danger">* {{ $message }}. </p>
          @enderror
        </div>
      </div>
    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <button id="showNotif" class="btn btn-primary form-control">
      <i class="fas fa-plus"></i> Create
    </button>
  </div>
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
