<div class="card mims-form-card">
  @php
    if(isset($_GET['via'])){
      $this->via_item = $_GET['via'];
    }
  @endphp
  <form wire:submit.prevent method="post" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="mims-form-grid">
        <div class="mims-form-field">
          <label for="">{{ __('Unit Of Measurement') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('terminology') is-invalid @enderror
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" autofocus placeholder="Unit Of Measurement" wire:model="terminology">

          <span class="text-danger">
            @error('terminology') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Abbreviation') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" autofocus placeholder="Abbreviation" wire:model="abbreviation">

          @error('abbreviation')
            <p class="text-danger">* {{ $message }}. </p>
          @enderror
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

    @if(session()->has('success_two'))
      Swal.fire({
        title: '{{ session('success_two') }}',
        html: '<p>You Will Now Leave This Page After Confirmation.</p>',
        icon: 'success',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        cancelButtonColor: '#d33',
      }).then((result) => {
        if (result.isConfirmed) {
          window.close();
        }
      });
    @endif

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
