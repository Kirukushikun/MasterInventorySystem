<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="card-body">
        <div class="form-group row">
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap">{{ __('Category') }}</label>
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap">{{ __('Sub Category') }}</label>
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap">{{ __('Product') }}</label>
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Item') }}</label>
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Remaining') }}</label>
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Quantity') }}</label>
        </div>
        <div class="form-group row">
            <div class="col-lg-2">
              <input type="text" class="form-control text-center" autofocus wire:model="category" placeholder="Category" disabled style="font-weight: bold;">
            </div>
            <div class="col-lg-2">
              <input type="text" class="form-control text-center" autofocus wire:model="subcategory" placeholder="Sub Category" disabled style="font-weight: bold;">
            </div>
            <div class="col-lg-2">
              <input type="text" class="form-control text-center" autofocus wire:model="product" placeholder="Product" disabled style="font-weight: bold;">
            </div>
            <div class="col-lg-2">
                <input type="text" class="form-control text-center" autofocus wire:model="item_name" placeholder="Item" disabled style="font-weight: bold;">
            </div>
            <div class="col-lg-2">
                <input type="text" class="form-control text-center" autofocus wire:model="rem_quantity" placeholder="Remaining" id="remainingQuantityInput" disabled style="font-weight: bold;">
            </div>
            <div class="col-lg-2 form-control-input">
                <input type="text" class="form-control text-center" autofocus wire:model.defer="quantity" placeholder="Quantity" id="quantityInput" oninput="this.value = this.value.replace(/[^-100-9]/g, '');" style="font-weight: bold;">
                <div id="invalidFeedback" style="color: #dc3545;">
                </div>
            </div>
        </div>

      <div id="selectedUser" class="form-group row">
          <label for="" class="col-md-1 col-form-label text-right text-nowrap">{{ __('Select User') }}<span class="text-danger"> *</span></label>
          <div class="col-md-11 form-control-input">
              <select class="form-control text-center form-control-input" autofocus id="selectUser" required>
                  <option class="text-center" hidden selected value="">Select User</option>
                  @foreach($user_list as $c)
                      <option class="text-center" value="{{ $c['id'] }}">{{ $c['num'] . ". " . $c['full_name'] }}</option>
                  @endforeach
              </select>
              <span class="text-danger">@error('users_id') {{ "* ". $message . "!" }} @enderror</span>
          </div>
      </div>

      <div class="form-group row">
        <label for="" class="col-md-1 col-form-label text-right text-wrap">{{ __('Farm Location') }}</label>
        <div class="col-md-11 form-control-input">
            <input type="text" class="form-control text-center" autofocus id="farm_loc" placeholder="Farm Location" disabled>
        </div>
      </div>
      <div class="form-group row">
        <label for="" class="col-md-1 col-form-label text-right text-wrap">{{ __('Department/Division') }}</label>
        <div class="col-md-11 form-control-input">
            <input type="text" class="form-control text-center" autofocus id="department_div" placeholder="Department/Division" disabled>
        </div>
      </div>

      <div class="form-group row">
        <label for="" class="col-md-1"></label>
        <h3 class="btn btn-primary col-md-11 alert-style text-left" style="padding-bottom: 25px; padding-top: 25px; border-left: 8px solid blue;"><i class="fab fa-slack"></i> A Notification Will Be Sent Via Slack </h3>
      </div>

      <div class="form-group row">
        <label for="" class="col-md-1 col-form-label text-right text-nowrap">{{ __('Notes') }}</label>
        <div class="col-md-11 form-control-input">
          <textarea class="form-control text-center @error('notes') is-invalid @enderror" autofocus placeholder="Notes" wire:model.defer="notes" style="height: 150px;"></textarea>
        </div>
      </div>
      
    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <a href="{{ url()->previous() }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 115px;" disabled>
      <i class="fas fa-plus"></i> Checkout
    </button>
  </div>
</div>

@push('scripts')
  <script>

    const quantityInput = document.getElementById('quantityInput');
    const remainingQuantityInput = "{{ $rem_quantity }}";
    const invalidFeedback = document.getElementById('invalidFeedback');
    const buttonCheckout = document.getElementById('showNotif');
    invalidFeedback.innerHTML = 'Required Field.';

    quantityInput.addEventListener('input', () => {
      const inputValue = quantityInput.value;

      if (isNaN(inputValue) || parseInt(inputValue) < 1) {
        quantityInput.classList.add('is-invalid');
        invalidFeedback.innerHTML = 'Must be Positive Number.';
        invalidFeedback.style.display = 'block';
        buttonCheckout.setAttribute("disabled", true);
        buttonCheckout.classList.add('is-invalid');
      }
      else if(inputValue == ''){
        quantityInput.classList.add('is-invalid');
        invalidFeedback.innerHTML = 'Quantity is Required.';
        invalidFeedback.style.display = 'block';
        buttonCheckout.setAttribute("disabled", true);
        buttonCheckout.classList.add('is-invalid');
      }
      else if(parseInt(inputValue) > parseInt(remainingQuantityInput)){
        quantityInput.classList.add('is-invalid');
        invalidFeedback.innerHTML = 'Quantity Must Be Less Than Or Equal to Remaining Quantity.';
        invalidFeedback.style.display = 'block';
        buttonCheckout.setAttribute("disabled", true);
        buttonCheckout.classList.add('is-invalid');
      }      
      else {
        quantityInput.classList.remove('is-invalid');
        quantityInput.classList.add('is-valid');
        invalidFeedback.style.display = 'none';
        buttonCheckout.removeAttribute("disabled");
        buttonCheckout.classList.remove('is-invalid');
        buttonCheckout.classList.add('is-valid');

        if($('#selectUser').val() == '' || $('#selectUser').val() == "" || $('#selectUser').val() == null){
          buttonCheckout.setAttribute("disabled", true);
          buttonCheckout.classList.add('is-invalid');
        }
        else{
          buttonCheckout.removeAttribute("disabled");
          buttonCheckout.classList.remove('is-invalid');
          buttonCheckout.classList.add('is-valid');
        }
      }
    });

    function adjustSelect2DropdownWidth(select2Element, fixedWidth) {
      var screenWidth = window.innerWidth;
      
      // Adjust the dropdown width based on screen size
      if (screenWidth < fixedWidth) {
        select2Element.select2({ width: screenWidth + "%" });
      } else {
        select2Element.select2({ width: fixedWidth + "%" });
      }
    }

    $(document).ready(function() {
      // Initialize Select2 for the elements
      var selectUser = $('#selectUser');
      
      selectUser.select2();
      
      // Initial adjustment on page load
      var fixedWidth = 100; // Set your desired initial width here
      adjustSelect2DropdownWidth(selectUser, fixedWidth);
      
      // Attach the adjustSelect2DropdownWidth function to the window resize event
      $(window).on("resize", function() {
        adjustSelect2DropdownWidth(selectUser, fixedWidth);
      });
    });


        
    function updateButtonContent() {
      var screenSize = window.innerWidth;
      var button = document.getElementById('showNotif');

      if (screenSize < 1200 && screenSize > 750) { // Adjust the breakpoint as needed
        button.innerHTML = '<i class="fas fa-shopping-cart"></i>';
      } else {
        button.innerHTML = '<i class="fas fa-shopping-cart"></i> Checkout';
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

    var farmLocationInput = document.getElementById('farm_loc');
    var departmentDivisionInput = document.getElementById('department_div');
    var id = 0;

    $('#selectUser').select2();
    $('#selectUser').on('change', function(e) { 
      var commaSeparatedString = $(this).val();
      var arrayFromString = commaSeparatedString.split(',');
      farmLocationInput.value = arrayFromString[1];
      departmentDivisionInput.value = arrayFromString[2];
      id = parseInt(arrayFromString[0]);
      console.log(id);

      if (isNaN(quantityInput.value) || parseInt(quantityInput.value) < 1) {
        quantityInput.classList.add('is-invalid');
        invalidFeedback.innerHTML = 'Must be Positive Number.';
        invalidFeedback.style.display = 'block';
        buttonCheckout.setAttribute("disabled", true);
        buttonCheckout.classList.add('is-invalid');
      }
      else if(quantityInput.value == ''){
        quantityInput.classList.add('is-invalid');
        invalidFeedback.innerHTML = 'Quantity is Required.';
        invalidFeedback.style.display = 'block';
        buttonCheckout.setAttribute("disabled", true);
        buttonCheckout.classList.add('is-invalid');
      }
      else if(parseInt(quantityInput.value) > parseInt(remainingQuantityInput)){
        quantityInput.classList.add('is-invalid');
        invalidFeedback.innerHTML = 'Quantity Must Be Less Than Or Equal to Remaining Quantity.';
        invalidFeedback.style.display = 'block';
        buttonCheckout.setAttribute("disabled", true);
        buttonCheckout.classList.add('is-invalid');
      }      
      else {
        quantityInput.classList.remove('is-invalid');
        quantityInput.classList.add('is-valid');
        invalidFeedback.style.display = 'none';
        buttonCheckout.removeAttribute("disabled");
        buttonCheckout.classList.remove('is-invalid');
        buttonCheckout.classList.add('is-valid');

        if($('#selectUser').val() == '' || $('#selectUser').val() == "" || $('#selectUser').val() == null){
          buttonCheckout.setAttribute("disabled", true);
          buttonCheckout.classList.add('is-invalid');
          console.log($('#selectUser').val());
        }
        else{
          buttonCheckout.removeAttribute("disabled");
          buttonCheckout.classList.remove('is-invalid');
          buttonCheckout.classList.add('is-valid');
          console.log("asd1");
        }
      }
      // Livewire.emit('listenerUsersValue', parseInt(arrayFromString[0]));
    });

    $("#showNotif").click(function() {
      Swal.fire({
        title: 'Checkout this item?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, checkout it!',
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
          console.log(id)
          Livewire.emit('createNewRecord', id); // emit an event to submit the Livewire form
        }
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
