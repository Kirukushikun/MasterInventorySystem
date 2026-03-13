<section class="py-2">
    <div class="container px-2 px-lg-3 my-3">
        <div class="row gx-4 gx-lg-5 align-items-start">
            <div id="print-section" class="col-md-3 text-center">
                {{-- <img id="print-image" class="card-img-top mb-5 mb-md-0" src="{!! $qr_code !!}" alt="QR Code Image" /> --}}
                <img src="{!! $itm_img !!}" class="img-thumbnail card-img-top mb-5 mb-md-5" style="width: 350px; height: 350px; max-width: 350px; max-height: 350px;">
                {{-- <img id="print-image" class="card-img-top mb-5 mb-md-0" src="{!! $itm_img !!}" alt="QR Code Image" /> --}}
            </div>
            <div id="print-section" class="col-md-3 text-center">
                <img id="print-image" class="card-img-top mb-5 mb-md-0" src="{!! $qr_code !!}" alt="QR Code Image" />
            </div>

            <div class="col-md-6">
                <div class="mb-4"><button onclick="printSection()" class="btn btn-dark"><i class="fas fa-print"></i></button></div>
                <h4 class="">Item: {{ $item_name }}</h4>
                <div class="small mb-1">Category: {{ $category }}</div>
                <div class="small mb-1">Sub Category: {{ $subcategory }}</div>
                <div class="small mb-1">Product: {{ $product }}</div>
                <div class="small mb-1">Item Card: {{ $item_number }}</div>
                <div class="small mb-1">Brand: {{ $model_number }}</div>
                {{-- <div class="small mb-1">Supplier: {{ $supplier }}</div> --}}
                <div class="small mb-1">U/M.: {{ $uom }}</div>

                <p class="lead">Total Quantity On-Hand: {{ $quantity }}</p>
                <p class="lead">Reorder Threshold: {{ $reorder_threshold }}</p>
                {{-- <div class="fs-5 mb-2">
                    <span class="text-decoration-line-through"> Purchase Date: {{ $purchase_date }}</span>
                </div>
                <div class="fs-5 mb-2">
                    <span class="text-decoration-line-through"> Expiry Date: {{ $expiry_date }}</span>
                </div>
                <div class="fs-5 mb-2">
                    <span class="text-decoration-line-through"> Remarks: {{ $remarks }}</span>
                </div>
                <div class="fs-5 mb-2">
                    <span class="text-decoration-line-through"> Created At: {{ $created_at }}</span>
                </div>
                <div class="fs-5 mb-2">
                    <span class="text-decoration-line-through"> Updated At: {{ $updated_at }}</span>
                </div> --}}
                {{-- <div class="d-flex">
                    <input class="form-control text-center me-3" id="inputQuantity" type="num" value="1" style="max-width: 3rem" />
                    <button class="btn btn-outline-dark flex-shrink-0" type="button">
                        <i class="bi-cart-fill me-1"></i>
                        Add to cart
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
</section>

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
