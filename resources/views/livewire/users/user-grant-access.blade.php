<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">
          <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Name') }}</label>
          <div class="col-md-9 form-control-input">
              <input type="text" class="form-control text-center" autofocus wire:model="user_name" placeholder="Item Name" disabled>
          </div>
      </div>

      <div class="form-group row">
          <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Access Type') }}<span class="text-danger"> *</span></label>
          <div class="col-md-9 form-control-input">
              <select class="form-control text-center" autofocus wire:model="user_role" required>
                  <option class="text-center" hidden selected>Select User Privilege</option>
                  @foreach($access_types as $key => $at)
                      <option class="text-center" value="{{ $key }}">{{ strtoupper($key) }}</option>
                  @endforeach
              </select>
              <span class="text-danger">@error('user_role') {{ "* ". $message . "!" }} @enderror</span>
          </div>
      </div>

      <div class="form-group row">
          <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Farm Location') }}<span class="text-danger"> *</span></label>
          <div class="col-md-9 form-control-input">
              <select class="form-control text-center" autofocus wire:model="user_farm_location" required>
                  <option class="text-center" hidden selected>Select Farm Location</option>
                  @foreach($f_list as $key => $fl)
                      <option class="text-center" value="{{ $fl->id }}">{{ strtoupper($fl->farm_location) }}</option>
                  @endforeach
              </select>
              <span class="text-danger">@error('user_farm_location') {{ "* ". $message . "!" }} @enderror</span>
          </div>
      </div>

      <div class="form-group row">
          <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Department/Division') }}<span class="text-danger"> *</span></label>
          <div class="col-md-9 form-control-input">
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
  </form>
  <div class="col-md-6 ml-auto">
    <a id="showNotif2" href="{{ route('user') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;">
      <i class="fas fa-plus"></i> Grant
    </button>
  </div>
</div>

@push('scripts')
  <script>
    $(document).ready(function () {
      function updateButtonContent() {
        var screenSize = window.innerWidth;
        var button = document.getElementById('showNotif');
        var button2 = document.getElementById('showNotif2');

        if (screenSize < 1200 && screenSize > 750) { // Adjust the breakpoint as needed
            button.innerHTML = '<i class="fas fa-check"></i>';
            button2.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            button.innerHTML = '<i class="fas fa-check"></i> Grant';
            button2.innerHTML = '<i class="fas fa-times"></i> Cancel';
        }
      }
      var inputs = document.querySelectorAll('.form-control-input');
      inputs.forEach(function (input) {
          input.style.borderRight = '6px solid blue';
      });

      // Initial call to set the button content based on the screen size
      updateButtonContent();

      // Update the button content when the window is resized
      window.addEventListener('resize', updateButtonContent);
    });

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
