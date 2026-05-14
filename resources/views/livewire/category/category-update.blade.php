<div class="card mims-form-card">
  <form wire:submit.prevent method="post" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="mims-form-grid">
        <div class="mims-form-field">
          <label for="">{{ __('Category') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('category_name') is-invalid @enderror 
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" autofocus placeholder="Category" wire:model="category_name">

          <span class="text-danger">
            @error('category_name') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Description') }}</label>
          <input type="text" class="form-control @error('category_description') is-invalid @enderror" autofocus placeholder="Description" wire:model="category_description">
          @error('category_description')
          <p class="text-danger">* {{ $message }} </p>
          @enderror
        </div>
      </div>
    </div>
    <div class="mims-form-actions">
      <a href="{{ route('category.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
      <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;" type="button">
        <i class="fas fa-plus"></i> Update
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
