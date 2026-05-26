<div class="card mims-form-card">
  <form wire:submit.prevent method="post" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="mims-form-grid">

        <div class="mims-form-field">
          <label for="">{{ __('Name') }}</label>
          <input type="text" class="form-control text-center" autofocus wire:model="user_name" placeholder="Name" disabled>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Access Type') }}<span class="text-danger"> *</span></label>
          <select class="form-control text-center" autofocus wire:model="user_role" required>
            <option class="text-center" hidden selected>Select User Privilege</option>
            @foreach($access_types as $key => $at)
              <option class="text-center" value="{{ $key }}">{{ strtoupper($key) }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('user_role') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Farm Location') }}<span class="text-danger"> *</span></label>
          <select class="form-control text-center" autofocus wire:model="user_farm_location" required>
            <option class="text-center" hidden selected>Select Farm Location</option>
            @foreach($f_list as $key => $fl)
              <option class="text-center" value="{{ $fl->id }}">{{ strtoupper($fl->farm_location) }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('user_farm_location') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <div class="mims-form-field">
          <label for="">{{ __('Department/Division') }}<span class="text-danger"> *</span></label>
          <select class="form-control text-center" autofocus wire:model="user_department_division" required>
            <option class="text-center" hidden selected>Select Department Division</option>
            @foreach($dd_list as $key => $dd)
              <option class="text-center" value="{{ $dd->id }}">{{ strtoupper($dd->department_division) }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('user_department_division') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
    </div>
    <div class="mims-form-actions">
      <a href="{{ route('user') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
      <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;" type="button">
        <i class="fas fa-check"></i> Grant
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
          title: 'Grant Access to this User?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
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
            Livewire.emit('grantAccess'); // emit an event to submit the Livewire form
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
