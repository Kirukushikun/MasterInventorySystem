<div class="card">
  @php
    if(isset($_GET['via'])){
      $this->via_item = $_GET['via'];
    }
  @endphp
  <form wire:submit.prevent method="post">
    @csrf
    <div class="card-body">
      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control @error('category_id') is-invalid @enderror" autofocus wire:model="category_id" wire:change="set_subcategory">
            <option class="text-center" hidden selected>-- Select Category --</option>
            @foreach($category_list as $category)
              <option class="text-center" value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
          </select>
          <span class="text-danger">@error('category_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Sub Category') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control @error('subcategory_id') is-invalid @enderror" autofocus wire:model="subcategory_id" wire:change="set_product">
            <option class="text-center" hidden selected>-- Select Sub Category --</option>
            @if($subcategory_list != null)
              @foreach($subcategory_list as $subcategory)
                <option class="text-center" value="{{ $subcategory->id }}">{{ $subcategory->subcategory_name }}</option>
              @endforeach
            @endif
          </select>
          <span class="text-danger">@error('subcategory_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
      <div class="form-group row">

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Product') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <select class="form-control @error('product_id') is-invalid @enderror" autofocus wire:model="product_id">
            <option class="text-center" hidden selected>-- Select Product --</option>
            @if($product_list != null)
              @foreach($product_list as $product)
                <option class="text-center" value="{{ $product->id }}">{{ $product->product_name }}</option>
              @endforeach
              @endif
          </select>
          <span class="text-danger">@error('product_id') {{ "* ". $message . "!" }} @enderror</span>
        </div>

      </div>
      <div class="form-group row">
        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Item') }}<span class="text-danger"> *</span></label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('item_name') is-invalid @enderror 
              {{ session()->has('already_exist')? 'is-invalid' : '' }}" autofocus placeholder="Item" wire:model="item_name">

          <span class="text-danger">
            @error('item_name') {{ "* ". $message }} @enderror
            @if(session()->has('already_exist'))
              <p>* {!! session('already_exist') !!}</p>
            @endif
          </span>
        </div>

        <label for="" class="col-sm-2 col-form-label text-right">{{ __('Description') }}</label>
        <div class="col-sm-3">
          <input type="text" class="form-control @error('item_description') is-invalid @enderror" autofocus placeholder="Description" wire:model="item_description">

          <span class="text-danger">@error('item_description') {{ "* ". $message . "!" }} @enderror</span>
        </div>
      </div>
    </div>
  </form>
  <div class="col-md-4 ml-auto">
    <button id="showNotif" class="btn btn-primary form-control">
      <i class="fas fa-plus"></i> Create
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
