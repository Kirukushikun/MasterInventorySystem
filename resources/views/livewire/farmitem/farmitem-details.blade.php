
<div class="row">
    <div class="col-md-3">
        <img src="{!! $itm_img !!}" class="" style="width: 350px; height: 350px; max-width: 350px; max-height: 350px;">
    </div>
    <div class="col-md-6 px-5">
        <h2 class="display-5 fw-bolder">Item Name: {{ $item_name }}</h2>
        <div class="small mb-3">Category: <b>{{ $category }}</b></div>
        <div class="small mb-1">Sub Category: <b>{{ $subcategory }}</b></div>
        <div class="small mb-1">Product: <b>{{ $product }}</b></div>
        <div class="small mb-1">U/M: <b>{{ $uom }}</b></div>

        <p class="lead">Remaining Quantity On-Hand: {{ $quantity }}</p>
    </div>
</div>

@section('scripts')
    <script>
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
@endsection
