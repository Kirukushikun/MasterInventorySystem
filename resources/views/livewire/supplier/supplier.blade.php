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
          <label for="">{{ __('Supplier Name') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('supplier_name') is-invalid @enderror 
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" autofocus placeholder="Supplier Name" wire:model="supplier_name">
          <span class="text-danger">

            @error('supplier_name') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Contact Person') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('contact_person') is-invalid @enderror" autofocus placeholder="Contact Person" wire:model="contact_person">
          <span class="text-danger">@error('contact_person') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Email') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('contact_email') is-invalid @enderror" autofocus placeholder="Email" wire:model="contact_email">

          <span class="text-danger">
            @error('contact_email') {{ "* ". $message }} @enderror
          </span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Tel No.') }}</label>
          <input type="text" class="form-control @error('contact_tel_no') is-invalid @enderror" autofocus placeholder="Telephone Number" wire:model="contact_tel_no">

          <span class="text-danger">
            @error('contact_tel_no') {{ "* ". $message }} @enderror
          </span>
        </div>

        <div class="mims-form-field mims-form-field--full">
          <label for="">{{ __('Mobile No.') }}<span class="text-danger"> *</span></label>
          <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" autofocus placeholder="Mobile Number" wire:model="contact_phone">
          
          <span class="text-danger">
            @error('contact_phone') {{ "* ". $message }} @enderror
          </span>
          <div class="mims-form-hint"><i class="fas fa-mobile"></i> Format Must Be +639**********</div>
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
