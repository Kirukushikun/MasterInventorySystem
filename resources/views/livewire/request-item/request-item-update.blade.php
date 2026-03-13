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
          <input type="date" class="form-control @error('date_requested') is-invalid @enderror" autofocus placeholder="1" wire:model="date_requested" disabled>
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
            @if($department_division_list != null)
              <option class="text-center" hidden selected>Select Department/Division</option>
              @foreach($department_division_list as $ddl)
                <option class="text-center" value="{{ $ddl->id }}">{{ $ddl->department_division }}</option>
              @endforeach
            @else
              <option class="text-center text-danger" hidden selected>Please Select Farm Location First!</option>
            @endif
          </select>
          <span class="text-danger">@error('department_division_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>

      <div class="card col-md-12">

        <div class="row">
          <h4 class="col-sm-12">ITEMS <i class="fas fa-list"></i></h4>
        </div>

        <div class="form-group row">
          <p for="" class="col-sm-3 text-center">{{ __('Item Name') }}</p>
          {{-- <p for="" class="col-sm-2 text-center">{{ __('Category') }}<span class="text-danger"> *</span></p> --}}
          {{-- <p for="" class="col-sm-2 text-center">{{ __('Sub Category') }}<span class="text-danger"> *</span></p> --}}
          <p for="" class="col-sm-3 text-center">{{ __('Product') }}<span class="text-danger"> *</span></p>
          <p for="" class="col-sm-3 text-center">{{ __('Quantity On-Stock') }}<span class="text-danger"> *</span></p>
          <p for="" class="col-sm-1 text-center">{{ __('U/M') }}<span class="text-danger"> *</span></p>
          <p for="" class="col-sm-1 text-center">{{ __('Quantity') }}<span class="text-danger"> *</span></p>
          <p for="" class="col-sm-1 text-center">{{ __('Option') }}</p>
        </div>

        @foreach ($items_requested as $key => $item)
          <div class="form-group row">
            <div class="col-sm-3">
                <select class="form-control text-center" autofocus wire:model="items_requested.{{ $key }}.item_name" wire:change="automate_input({{ $key }})">
                    <option class="text-center" hidden selected>-- Select Item Name -- </option>
                    @foreach($item_name_list as $inl)
                      <option class="text-center" value="{{ $inl['id'] }}">{{ strtoupper($inl['name']) }}</option>
                    @endforeach
                </select>
                <span class="text-danger">@error('items_requested.' . $key . '.item_name') {{ "* ". $message . "!" }} @enderror</span>
            </div>
            {{-- <div class="col-sm-2 text-center">
                <input type="text" class="form-control" wire:model="items_requested.{{ $key }}.item_category" disabled>
            </div>
            <div class="col-sm-2 text-center">
                <input type="text" class="form-control" wire:model="items_requested.{{ $key }}.item_subcategory" disabled>
            </div> --}}
            <div class="col-sm-3 text-center">
                <input type="text" class="form-control" wire:model="items_requested.{{ $key }}.item_product" disabled>
            </div>
            <div class="col-sm-3 text-center">
                <input type="text" class="form-control" wire:model="items_requested.{{ $key }}.rem_quan" disabled>
            </div>
            <div class="col-sm-1 text-center">
                <select class="form-control text-center" autofocus wire:model="items_requested.{{ $key }}.uom_id" disabled>
                    <option class="text-center" hidden selected>-- Select U/M --</option>
                    @foreach($uom_list as $ul)
                      <option class="text-center" value="{{ $ul->id }}">{{ strtoupper($ul->abbreviation) }}</option>
                    @endforeach
                </select>
                <span class="text-danger">@error('items_requested.' . $key . '.uom_id') {{ "* ". $message . "!" }} @enderror</span>
            </div>
            <div class="col-sm-1 text-center">
                <input type="number" class="form-control" wire:model="items_requested.{{ $key }}.item_quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <span class="text-danger">@error('items_requested.' . $key . '.item_quantity') {{ "* ". $message . "!" }} @enderror</span>
            </div>
            <div class="col-sm-1 text-center">
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

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Remarks') }}</label>
        <div class="col-sm-6">
          <textarea class="form-control @error('remarks') is-invalid @enderror" autofocus placeholder="Remarks" wire:model="remarks" style="height: 150px;"></textarea>

          <span class="text-danger">@error('remarks') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <a href="{{ route('request.item.list') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    <button id="showNotif" class="btn btn-primary form-control" style="max-width: 100px;">
      <i class="fas fa-plus"></i> Update
    </button>
  </div>
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
          title: 'Are you sure?',
          text: "You want to edit this item?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Update it!',
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
