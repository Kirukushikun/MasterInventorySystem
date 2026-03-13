  <div class="card">
    <div class="card-header">
        <h3><i class="fas fa-shopping-cart"></i> {{ $this->checkout_type == 'partial' ? 'Partial Checkout' : ($this->checkout_type == 'full' ? 'Full Checkout' : 'Checkout') }}</h3>
    </div>
    <form wire:submit.prevent autocomplete="off">
      @csrf
      <div class="card-body">
        <div class="form-group row">
          <label for="" class="col-lg-1 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Item Image') }}</label>
          <label for="" class="col-lg-3 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Item') }}</label>
          <label for="" class="col-lg-3 col-form-label text-center text-nowrap">{{ __('Product') }}</label>
          @if($this->req_id != 0)
            <label for="" class="col-lg-1 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Rqstd Qty') }}</label>
            <label for="" class="col-lg-1 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Qty Released') }}</label>
          @endif
          <label for="" class="col-lg-{{$this->req_id != 0 ? '1' : '2'}} col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Qty On-Hand') }}</label>
          <label for="" class="col-lg-2 col-form-label text-center text-nowrap" style="font-weight: bold;">{{ __('Quantity') }}</label>
        </div>
        {{-- @dump($this->requested_items) --}}
        @php
          $ctr = 0;
        @endphp
        @foreach($selectedItems as $key => $item)
          <div class="form-group row">
              <div class="col-lg-1 text-center justify-content-center">
                {!! $item['item_image'] !!}
              </div>
              <div class="col-lg-3 pt-4">
                  <input type="text" class="form-control text-center" autofocus wire:model.defer="selectedItems.{{ $key }}.item_name" placeholder="Item Name" disabled>
              </div>
              <div class="col-lg-3 pt-4">
                <input type="text" class="form-control text-center" autofocus wire:model.defer="selectedItems.{{ $key }}.item_product" placeholder="Product" disabled>
                <input type="hidden" class="form-control text-center" autofocus wire:model.defer="selectedItems.{{ $key }}.item_subcategory" placeholder="Sub Category" disabled>
                <input type="hidden" class="form-control text-center" autofocus wire:model.defer="selectedItems.{{ $key }}.item_category" placeholder="Category" disabled>
              </div>

              @if($this->req_id != 0)
                <div class="col-lg-1 pt-4">
                    <input type="text" class="form-control text-center" autofocus placeholder="Requested Qty" disabled value="{{ $this->arrayqty[$ctr] }}">
                </div>
                <div class="col-lg-1 pt-4">
                    <input type="text" class="form-control text-center" autofocus placeholder="Requested Qty" disabled value="{{ $this->rqstd_itms[$ctr] }}">
                </div>
              @endif

              <div class="col-lg-{{$this->req_id != 0 ? '1' : '2'}} pt-4">
                  <input type="text" class="form-control text-center" autofocus wire:model.defer="selectedItems.{{ $key }}.item_remaining_quantity" placeholder="Remaining Quantity" disabled>
              </div>

              <div class="col-lg-2 form-control-input pt-4">
                  <input type="text" class="form-control text-center" autofocus wire:model.defer="selectedItems.{{ $key }}.item_selected_quantity"
                    placeholder="Quantity" id="quantityInput{{ $key }}"
                    oninput="this.value = this.value.replace(/[^-100-9]/g, '');"
                    {{ $this->checkout_type == 'full' ? 'disabled' : '' }}
                    >
                  <div id="invalidFeedback{{ $key }}" style="color: #dc3545;">
                  </div>
              </div>
          </div>
          @php $ctr++; @endphp
        @endforeach

        {{-- <div class="form-group row">
        </div> --}}


        <div class="form-group row">
            <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Select POC') }}<span class="text-danger"> *</span></label>
            <div class="col-md-9 form-control-input">
                <select id="selectUser" class="form-control text-center form-control-input" autofocus required>
                    <option class="text-center"hidden selected value="">Select POC</option>
                    @foreach($user_list as $c)
                        <option class="text-center" value="{{ $c['id'] }}"
                          {{ $this->req_id == $c['reqid'] ? "selected" : "" }}>
                          {{ $c['num'] . ". " . $c['full_name'] }}
                        </option>
                    @endforeach
                </select>
                <span class="text-danger">@error('users_id') {{ "* ". $message . "!" }} @enderror</span>
            </div>
        </div>

        <div class="form-group row">
          <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Farm Location') }}</label>
          <div class="col-md-9 form-control-input">
              <input type="text" class="form-control text-center" autofocus id="farm_loc" placeholder="Farm Location" disabled>
          </div>
        </div>
        <div class="form-group row">
          <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Department/Division') }}</label>
          <div class="col-md-9 form-control-input">
              <input type="text" class="form-control text-center" autofocus id="department_div" placeholder="Department/Division" disabled>
          </div>
        </div>

        <div class="form-group row">
            <label for="" class="col-md-3 col-form-label text-right text-nowrap">{{ __('Comment/Note') }}</label>
          <div class="col-md-9 form-control-input">
            <textarea class="form-control text-center @error('notes') is-invalid @enderror" autofocus placeholder="Comment/Note" wire:model.defer="notes" style="height: 150px;"></textarea>
          </div>
        </div>

      </div>
    </form>
    <div class="col-md-4 ml-auto">
      <a href="{{ $this->req_id != 0 ? route('for.approval.list') : route('item.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
      <button id="showNotif" class="btn btn-primary form-control" style="max-width: 115px;" disabled>
        <i class="fas fa-plus"></i> Checkout
      </button>
    </div>
  </div>

{{--   <div>
    @dump($users_id)
  </div> --}}

  @push('scripts')
    <script>
      const buttonCheckout = document.getElementById('showNotif');

      @php
        $ctr1 = 0;
      @endphp
      @foreach($selectedItems as $key => $item)
        const quantityInput{{ $key }} = document.getElementById('quantityInput{{ $key }}');

        const remainingQuantityInput{{ $key }} = "{{ $item['item_remaining_quantity'] }}";

        @if($this->req_id != 0)
          const requestedQuantity{{ $key }} = {{ $this->arrayqty[$ctr1] }} - {{ $this->rqstd_itms[$ctr1] }};
        @endif

        const invalidFeedback{{ $key }} = document.getElementById('invalidFeedback{{ $key }}');
        // invalidFeedback{{-- $key --}}.innerHTML = 'Required Field.';

        @if ($this->checkout_type == 'full')
            if (quantityInput{{ $key }}.value != '' || !isNaN(quantityInput{{ $key }}.value) || parseInt(quantityInput{{ $key }}.value) > 1) {
                invalidFeedback{{ $key }}.style.display = 'none';
                quantityInput{{ $key }}.classList.remove('is-invalid');
                quantityInput{{ $key }}.classList.add('is-valid');
                buttonCheckout.removeAttribute("disabled");
                buttonCheckout.classList.remove('is-invalid');
                buttonCheckout.classList.add('is-valid');
            }
        @endif

        quantityInput{{ $key }}.addEventListener('input', () => {

          const inputValue{{ $key }} = quantityInput{{ $key }}.value;


          if (isNaN(inputValue{{ $key }}) || parseInt(inputValue{{ $key }}) < 0) {
            quantityInput{{ $key }}.classList.add('is-invalid');
            invalidFeedback{{ $key }}.innerHTML = 'Must be Positive Number.';
            invalidFeedback{{ $key }}.style.display = 'block';
            buttonCheckout.setAttribute("disabled", true);
            buttonCheckout.classList.add('is-invalid');
          }
          else if(inputValue{{ $key }} == ''){
            quantityInput{{ $key }}.classList.add('is-invalid');
            invalidFeedback{{ $key }}.innerHTML = 'Quantity is Required.';
            invalidFeedback{{ $key }}.style.display = 'block';
            buttonCheckout.setAttribute("disabled", true);
            buttonCheckout.classList.add('is-invalid');
          }
          @if($this->req_id != 0)
            else if(parseInt(inputValue{{ $key }}) > parseInt(requestedQuantity{{ $key }})){
              quantityInput{{ $key }}.classList.add('is-invalid');
              invalidFeedback{{ $key }}.innerHTML = 'Qty that will be Released must not exceed <u>' + requestedQuantity{{ $key }} +'</u>(Quantity Requested - Quantity Released).';
              invalidFeedback{{ $key }}.style.display = 'block';
              buttonCheckout.setAttribute("disabled", true);
              buttonCheckout.classList.add('is-invalid');
            }
          @endif

          else if(parseInt(inputValue{{ $key }}) > parseInt(remainingQuantityInput{{ $key }})){
            quantityInput{{ $key }}.classList.add('is-invalid');
            invalidFeedback{{ $key }}.innerHTML = 'Quantity Must Be Less Than Or Equal to Remaining Quantity.';
            invalidFeedback{{ $key }}.style.display = 'block';
            buttonCheckout.setAttribute("disabled", true);
            buttonCheckout.classList.add('is-invalid');
          }
          else {
            quantityInput{{ $key }}.classList.remove('is-invalid');
            quantityInput{{ $key }}.classList.add('is-valid');
            invalidFeedback{{ $key }}.style.display = 'none';
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
        });
        @php $ctr1++; @endphp
      @endforeach

      // Function to adjust the Select2 dropdown width based on the screen size
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

      @if($this->req_id != 0)
        var commaSeparatedString = $('#selectUser').val();
        var arrayFromString = commaSeparatedString.split(',');
        farmLocationInput.value = arrayFromString[1];
        departmentDivisionInput.value = arrayFromString[2];
        id = parseInt(arrayFromString[0]);
        console.log(id);
      @endif

      $('#selectUser').select2();
      $('#selectUser').on('change', function(e) {
        var commaSeparatedString = $(this).val();
        var arrayFromString = commaSeparatedString.split(',');
        farmLocationInput.value = arrayFromString[1];
        departmentDivisionInput.value = arrayFromString[2];
        id = parseInt(arrayFromString[0]);
        console.log(id);

        @foreach($selectedItems as $key => $item)

          if (isNaN(quantityInput{{ $key }}.value) || parseInt(quantityInput{{ $key }}.value) < 0) {
            quantityInput{{ $key }}.classList.add('is-invalid');
            invalidFeedback{{ $key }}.innerHTML = 'Must be Positive Number.';
            invalidFeedback{{ $key }}.style.display = 'block';
            buttonCheckout.setAttribute("disabled", true);
            buttonCheckout.classList.add('is-invalid');
          }
          else if(quantityInput{{ $key }}.value == ''){
            quantityInput{{ $key }}.classList.add('is-invalid');
            invalidFeedback{{ $key }}.innerHTML = 'Quantity is Required.';
            invalidFeedback{{ $key }}.style.display = 'block';
            buttonCheckout.setAttribute("disabled", true);
            buttonCheckout.classList.add('is-invalid');
          }
          @if($this->req_id != 0)
            else if(parseInt(inputValue{{ $key }}) > parseInt(requestedQuantity{{ $key }})){
              quantityInput{{ $key }}.classList.add('is-invalid');
              invalidFeedback{{ $key }}.innerHTML = 'Qty that will be Released must not exceed the requested Qty.';
              invalidFeedback{{ $key }}.style.display = 'block';
              buttonCheckout.setAttribute("disabled", true);
              buttonCheckout.classList.add('is-invalid');
            }
          @endif
          else if(parseInt(quantityInput{{ $key }}.value) > parseInt(remainingQuantityInput{{ $key }})){
            quantityInput{{ $key }}.classList.add('is-invalid');
            invalidFeedback{{ $key }}.innerHTML = 'Quantity Must Be Less Than Or Equal to Remaining Quantity.';
            invalidFeedback{{ $key }}.style.display = 'block';
            buttonCheckout.setAttribute("disabled", true);
            buttonCheckout.classList.add('is-invalid');
          }
          else {
            quantityInput{{ $key }}.classList.remove('is-invalid');
            quantityInput{{ $key }}.classList.add('is-valid');
            invalidFeedback{{ $key }}.style.display = 'none';
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
        @endforeach
        // Livewire.emit('listenerUsersValue', parseInt(arrayFromString[0]));
      });

      $("#showNotif").click(function() {
        Swal.fire({
          title: 'Checkout this item?',
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
