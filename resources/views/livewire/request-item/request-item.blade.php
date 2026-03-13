<div class="card">
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right text-nowrap">{{ __('Series #:') }}</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" autofocus wire:model="series_number" disabled>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right text-nowrap">{{ __('Requestors Name:') }}</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" autofocus wire:model="name" disabled>
        </div>

      </div>
      {{-- @php
        var_dump($sampel);
      @endphp --}}

      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Date Requested') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-4">
          <input type="date" class="form-control @error('date_requested') is-invalid @enderror" autofocus placeholder="1" wire:model="date_requested">

          <span class="text-danger">@error('date_requested') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Date Needed') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-4">
          <input type="date" class="form-control @error('date_needed') is-invalid @enderror" autofocus placeholder="1" wire:model="date_needed">

          <span class="text-danger">@error('date_needed') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Location') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-4">
          <select class="form-control text-center" autofocus wire:model="farm_location_id" wire:change="setSeries" disabled>

            <option class="text-center" hidden selected>Select Farm Location</option>

            @foreach($farm_location_list as $fll)
              <option class="text-center" value="{{ $fll->id }}">{{ $fll->farm_location }}</option>
            @endforeach
          </select>

          <span class="text-danger">@error('farm_location_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Div/Dept') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-4">
          <select class="form-control text-center" autofocus wire:model="department_division_id" wire:change="setSeries" @if($department_division_list == null) disabled @endif disabled>
              <option class="text-center" hidden selected>Select Department/Division</option>
              @foreach($department_division_list as $ddl)
                <option class="text-center" value="{{ $ddl->id }}">{{ $ddl->department_division }}</option>
              @endforeach
              <option class="text-center text-danger" hidden selected>Please Select Farm Location First!</option>
          </select>
          <span class="text-danger">@error('department_division_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

      <div class="card col-md-12">

        <div class="row">
          <h4 class="col-sm-12">ITEMS <i class="fas fa-list"></i></h4>
        </div>

        <div class="form-group row">
          <p for="" class="col-sm-1 text-center">{{ __('Item Image') }}</p>
          <p for="" class="col-sm-5 text-center">{{ __('Item Name') }}</p>
          {{-- <p for="" class="col-sm-2 text-center">{{ __('Category') }}<span class="text-danger"> *</span></p> --}}
          {{-- <p for="" class="col-sm-2 text-center">{{ __('Sub Category') }}<span class="text-danger"> *</span></p> --}}
          <p for="" class="col-sm-2 text-center">{{ __('Product') }}<span class="text-danger"> *</span></p>

          {{-- <p for="" class="col-sm-1 text-center">{{ __('Quantity On-Stock') }}<span class="text-danger"> *</span></p> --}}

          <p for="" class="col-sm-2 text-center">{{ __('U/M') }}<span class="text-danger"> *</span></p>
          <p for="" class="col-sm-1 text-center">{{ __('Quantity') }}<span class="text-danger"> *</span></p>
          <p for="" class="col-sm-1 text-center">{{ __('Option') }}</p>
        </div>
        @php
            $valid_status = [];
        @endphp
        @foreach ($items_requested as $key => $item)
          <div class="form-group row">
            <div class="col-sm-1 text-center">
                {!! $item['item_image'] !!}
            </div>
            <div class="col-sm-5 pt-4">
              <select class="form-control text-center" autofocus wire:model="items_requested.{{ $key }}.item_name" wire:change="automate_input({{ $key }})">
                  <option class="text-center" hidden selected>-- Select Item Name -- </option>
                  @foreach($item_name_list as $inl)
                    <option class="text-center" value="{{ $inl['id'] }}">{{ strtoupper($inl['name']) }}</option>
                  @endforeach
              </select>
              <span class="text-danger">@error('items_requested.' . $key . '.item_name') {{ "* ". $message . "!" }} @enderror</span>
              <input type="hidden" wire:model="items_requested.{{ $key }}.item_subcategory">
              <input type="hidden" wire:model="items_requested.{{ $key }}.item_category">
            </div>
            {{-- <div class="col-sm-2 text-center">
                <input type="text" class="form-control" wire:model="items_requested.{{ $key }}.item_category" disabled>
            </div>
            <div class="col-sm-2 text-center">
                <input type="text" class="form-control" wire:model="items_requested.{{ $key }}.item_subcategory" disabled>
            </div> --}}
            <div class="col-sm-2 text-center pt-4">
                <input type="text" class="form-control text-center" wire:model="items_requested.{{ $key }}.item_product" disabled>
            </div>
            {{-- <div class="col-sm-1 text-center pt-4">
                <input type="text" class="form-control text-center" wire:model="items_requested.{{ $key }}.rem_quan" disabled>
            </div> --}}
            <div class="col-sm-2 text-center pt-4">
                <select class="form-control text-center" autofocus wire:model="items_requested.{{ $key }}.uom_id" disabled>
                    <option class="text-center" hidden selected></option>
                    @foreach($uom_list as $ul)
                      <option class="text-center" value="{{ $ul->id }}">{{ strtoupper($ul->abbreviation) }}</option>
                    @endforeach
                </select>
                <span class="text-danger">@error('items_requested.' . $key . '.uom_id') {{ "* ". $message . "!" }} @enderror</span>
            </div>
            <div class="col-sm-1 text-center pt-4">
                <input type="number" class="form-control" wire:model="items_requested.{{ $key }}.item_quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <span class="text-danger">@error('items_requested.' . $key . '.item_quantity') {{ "* ". $message . "!" }} @enderror</span>
                @if ($item['item_quantity'] > $item['rem_quan'])
                    @php $valid_status[] = 0; @endphp
                    <span class="text-danger">* The Central Warehouse can no longer fulfill your request.</span>
                @else
                    @php $valid_status[] = 1; @endphp
                @endif
            </div>
            <div class="col-sm-1 text-center pt-4">
                <button class="btn btn-danger" wire:click.prevent="removeRow({{ $key }})"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        @endforeach

        <div class="form-group row">
            <div class="col-sm-10"></div>
            <div class="col-sm-2 text-center">
                <button class="btn btn-primary" wire:click.prevent="addAnotherRow">Add <i class="fas fa-plus"></i></button>
            </div>
        </div>
      </div>
      <div class="form-group row">

            {{-- <label for="" class="col-sm-2 col-form-label text-right">{{ __('Farm Requested') }} <span class="text-danger">*</span></label>
            <div class="col-sm-10">
            <textarea class="form-control @error('remarks') is-invalid @enderror" autofocus placeholder="Remarks" wire:model="remarks" style="height: 150px;"></textarea>

            <select name="" class="form-control" wire:model="requested_to">
                <option class="text-center" hidden selected>-- Choose Farm For Request --</option>
            </select>

            <span class="text-danger">@error('remarks') {{ "* ". $message . "!" }} @enderror</span>
            </div> --}}

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Disposition') }}</label>
        <div class="col-sm-10">
          <textarea class="form-control @error('remarks') is-invalid @enderror" autofocus placeholder="Disposition" wire:model="remarks" style="height: 150px;"></textarea>

          <span class="text-danger">@error('remarks') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Attachment') }} <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <input type="file" accept=".pdf" class="@error('jl_pdf') is-invalid @enderror" autofocus placeholder="Attachment" wire:model="jl_pdf" style="height: 150px;">

          <span class="text-danger">@error('jl_pdf') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <div class="col-sm-12">
          @if ($jl_pdf)
            <embed src="{{ $jl_pdf->temporaryUrl() }}" type="application/pdf" width="100%" height="600px">
          @endif
        </div>

      </div>
    </div>
  </form>
    @if(!$this->no_series_available)
      <div class="col-md-4 ml-auto">
        <button id="showNotif" class="btn btn-primary form-control">
          <i class="fas fa-plus"></i> REQUEST
        </button>
      </div>
    @endif
</div>

@push('scripts')
  <script>

    const imageButtons = document.querySelectorAll('.imageZoomButton');
    imageButtons.forEach(button => {
        button.addEventListener('click', () => {
            const imageUrl = button.getAttribute('data-imageurl');
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: 'Image',
                showConfirmButton: true,
                customClass: {
                    image: 'zoomable-image',
                },
                allowOutsideClick: true,
                allowEscapeKey: true,
                imageWidth: 500, // Set the width here (e.g., 400px)
                imageHeight: 500, // Set the height here (e.g., 300px)
            });
        });
    });

    @if($this->no_series_available)
      Swal.fire({
        title: 'Warning!',
        html: `Assigned Series Has Been Fully Utilized, <br>Please Contact System Administrator Before Proceeding!`,
        icon: 'warning'
      });
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
          title: '{{ (in_array(1, $valid_status)) ? "Request Cannot Be Fulfilled by the Central Warehouse!" : "Confirm Request" }}',
          text: `{{ (in_array(1, $valid_status)) ? "Continue?" : "You want to proceed with the request?" }}`,
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Proceed',
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
