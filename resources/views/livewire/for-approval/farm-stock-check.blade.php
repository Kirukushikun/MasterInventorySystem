<div class="card">
  <div class="col-md-3 ml-auto">
    <button class="btn btn-warning text-center form-control" wire:click="clear_fields"><i class="fas fa-eraser"></i> CLEAR FIELDS</button>
  </div>
  {{-- @foreach($for_inject_list as $fil_key => $fil)
    @inject($fil, $fil_key)
  @endforeach --}}
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">
        <label for="" class="col-sm-3 col-form-label text-right" style="font-size: 18px;">
          {{ __('Choose Available Farm') }}<span class="text-danger"> *</span>
        </label>
        <div class="col-sm-6">
          <select name="" class="form-control" wire:model="available_farm" wire:change="checkFarmStock"  style="font-size: 20px; font-weight: bold;">
            <option class="text-center" hidden selected>Select Farm</option>
            @foreach($farms as $farm)
              <option value="{{ $farm->id }}">
                {{ $farm->farmLocation->farm_location }} - {{ $farm->departmentDivision->department_division }}
              </option>
            @endforeach
          </select>
          @if (!$available_farm)
            <p class="text-danger" style="font-size: 20px;">* Please, Choose Available Farm. </p>
          @endif
          @error('available_farm')
            <p class="text-danger">* {{ $message }} </p>
          @enderror
        </div>
      </div>
      <div class="card col-md-12">

        <div class="row">
          <h4 class="col-sm-12 pl-5"> Farm Stocks <i class="fas fa-inventory"></i></h4>
        </div>
        <div class="form-group row">
          <p for="" class="col-sm-4 text-center text-secondary" style="font-size: 20px; font-weight: bold;">{{ __('Item Name') }}</p>
          <p for="" class="col-sm-2 text-center text-secondary" style="font-size: 20px; font-weight: bold;">{{ __('Quantity On-Stock') }}</p>
          <p for="" class="col-sm-2 text-center text-secondary" style="font-size: 20px; font-weight: bold;">{{ __('Unit Of Measurement') }}</p>
          <p for="" class="col-sm-2 text-center text-secondary" style="font-size: 20px; font-weight: bold;">{{ __('Requested Quantity') }}</p>
          <p for="" class="col-sm-2 text-center text-secondary" style="font-size: 20px; font-weight: bold;">{{ __('Quantity to Release') }}<span class="text-danger"> *</span></p>
        </div>

        @if ($available_farm)

          @foreach ($available_farm_items as $index => $item)
            <div class="form-group row">
              <div class="col-sm-4 pt-4 text-center" style="font-size: 20px; font-weight: bold;">
                {{ $item['item_name'] }}
              </div>
              <div class="col-sm-2 text-center pt-4">
                <span class="badge badge-success pt-2" style="font-size: 20px; width: 150px; height: 38px;">
                  {{ $item['quantity_on_stock'] }}
                </span>
              </div>
              <div class="col-sm-2 text-center pt-4">
                <span class="badge badge-secondary pt-2" style="font-size: 20px; width: 175px; height: 38px;">{{ $item['unit_of_measurement'] }}</span>
              </div>
              <div class="col-sm-2 text-center pt-4">
                <span class="badge badge-primary pt-2" style="font-size: 20px; width: 150px; height: 38px;">
                  {{ $item['requested_quantity'] }}
                </span>
              </div>
              <div class="col-sm-2 text-center pt-4">
                <input type="number" class="form-control text-center" style="font-size: 20px; font-weight: bold;"
                  wire:model="available_farm_items.{{ $index }}.quantity_to_release"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <span class="text-danger">@error('available_farm_items.' . $index . '.quantity_to_release') {{ "* ". $message }} @enderror</span>
              </div>
            </div>
          @endforeach
          <br>

          <div class="form-group row">
            <div class="col-sm-2 text-right">
              <label for="notes" class="pb-3" style="font-size: 15px; color: #A84443; font-weight: bold;">{{ __('Comment/Remarks:') }}</label>
            </div>
            <div class="col-sm-6 text-center">
              <textarea name="notes" class="form-control" style="font-size: 20px; font-weight: bold; height: 150px;" wire:model="notes" id="notes"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-2 text-right">
              <label for="chk_fraction" class="pb-3" style="font-size: 15px; color: #A84443; font-weight: bold;">{{ __('Partial Release?') }}</label>
            </div>
            <div class="col-sm-10 text-left">
              <input type="checkbox" wire:model="chk_fraction" id="chk_fraction" style="font-size: 20px; width: 38px; height: 38px;">
            </div>
          </div>

        @endif


      </div>
    </div>
  </form>
  <div class="col-md-2 ml-auto">
    <button id="showNotif" class="btn btn-primary text-center form-control"  @if(!$available_farm) disabled @endif>
      <i class="fas fa-shopping-basket"></i> {{ __(' Proceed ') }}
    </button>
  </div>
</div>

@push('scripts')
  <script>


    $(document).ready(function() {
      $("#showNotif").click(function() {
        Swal.fire({
          title: 'Are you sure?',
          text: "You want release this item?",
          icon: 'info',
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
            Livewire.emit('proceed'); // emit an event to submit the Livewire form
          }
        });
      });
    });
  </script>
@endpush
