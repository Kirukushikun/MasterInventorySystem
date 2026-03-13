<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">
        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Supplier Name') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('supplier_name') is-invalid @enderror 
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" autofocus placeholder="Supplier Name" wire:model="supplier_name">
          <span class="text-danger">

            @error('supplier_name') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Contact Person') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('contact_person') is-invalid @enderror" autofocus placeholder="Contact Person" wire:model="contact_person">
          <span class="text-danger">@error('contact_person') {{ "* ". $message . "!" }} @enderror</span>
        </div>
      </div>
      
      <div class="form-group row">
        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Email') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('contact_email') is-invalid @enderror" autofocus placeholder="Email" wire:model="contact_email">

          <span class="text-danger">
            @error('contact_email') {{ "* ". $message }} @enderror
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Tel No.') }}</label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('contact_tel_no') is-invalid @enderror" autofocus placeholder="Telephone Number" wire:model="contact_tel_no">

          <span class="text-danger">
            @error('contact_tel_no') {{ "* ". $message }} @enderror
          </span>
        </div>
      </div>


      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Mobile No.') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" autofocus placeholder="Mobile Number" wire:model="contact_phone">
          
          <span class="text-danger">
            @error('contact_phone') {{ "* ". $message }} @enderror
          </span>
          <h3 class="btn btn-warning alert-style text-left" style="padding-bottom: 10px; padding-top: 10px; border-left: 8px solid orange;"><i class="fas fa-mobile"></i> Format Must Be +639********** </h3>
        </div>

      </div>
    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <a href="{{ route('supplier.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;">
      <i class="fas fa-plus"></i> Update
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
