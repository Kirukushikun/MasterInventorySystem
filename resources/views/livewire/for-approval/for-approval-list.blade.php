<div class="mims-table-card">
    @inject('itemlist', 'App\Models\ItemList')
    @inject('itemname', 'App\Models\ItemName')
    @inject('itm', 'App\Models\Item')
    @inject('prd', 'App\Models\Product')
    @inject('uom', 'App\Models\UnitOfMeasurement')
    <div class="mims-table-toolbar">
        <div>
            <h3 class="mims-table-title">For Approval</h3>
            <p class="mims-table-subtitle">Review item requests, attachments, approval status, and release actions.</p>
        </div>
    </div>
    <div class="mims-table-wrap">
        <table id="items_table" class="table table-hover mims-table">
            <thead>
                <tr>
                    <th>ID.</th>
                    <th>SERIES NO.</th>
                    <th>REQUESTED BY</th>
                    <th>STATUS</th>
                    <th>ITEM REQUESTED</th>
                    <th>DEPARTMENT</th>
                    <th>FARM LOCATION</th>
                    <th>DATE REQUESTED</th>
                    <th>DATE NEEDED</th>
                    <th>COMMENTS/NOTES</th>
                    <th>ATTACHMENT</th>
                    <th class="text-nowrap">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $fal)
                    <tr>
                        <td class="td-num">{{ str_pad($fal['id'], 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $fal['series_number'] }}</td>
                        <td>{{ $fal['requested_by'] }}</td>
                        <td>{!! $fal['status'] !!}</td>
                        <td>{!! $fal['items_requested'] !!}</td>
                        <td>{{ $fal['department_division'] }}</td>
                        <td>{{ $fal['location'] }}</td>
                        <td>{{ $fal['date_requested'] }}</td>
                        <td>{{ $fal['date_needed'] }}</td>
                        <td>{!! $fal['comment'] !!}</td>
                        <td>{!! $fal['attachment'] !!}</td>
                        <td wire:defer class="text-nowrap">
                            <div class="mims-table-actions">
                                {!! $fal['action'] !!}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted">No approval requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- Gate pass data — keyed by request ID, pre-rendered server-side for the in-transit modal --}}
@php
  $gatePassData = [];
  if (!empty($this->ris1)) {
    foreach ($this->ris1 as $ris1) {
      $items = $itemlist::where('request_item_id', $ris1)->where('active_status', 1)->get();
      $rows  = '';
      foreach ($items as $k => $il) {
        if ($il->item_partially_release_quantity > 0) {
          $iname = $itemname::findorfail($il->item_id);
          $rows .= '<tr>'
            . '<td>' . ($k + 1) . '</td>'
            . '<td><img src="' . asset('photos/' . $itm::findorfail($il->item_id)->item_image) . '" class="img-thumbnail" style="width:80px;height:80px;"></td>'
            . '<td>' . strtoupper($iname->item_name ?? '-') . '</td>'
            . '<td>' . strtoupper($prd::findorfail($iname->product_id)->product_name ?? '-') . '</td>'
            . '<td>' . strtoupper($uom::findorfail($il->uom_id)->abbreviation ?? '-') . '</td>'
            . '<td>' . ($il->item_partially_release_quantity ?? '-') . '</td>'
            . '</tr>';
        }
      }
      $gatePassData[$ris1] = [
        'rows'               => $rows,
        'location'           => $dat2[$ris1]['location']           ?? '',
        'abbrvtn'            => $dat2[$ris1]['abbrvtn']            ?? '',
        'department_division'=> $dat2[$ris1]['department_division']?? '',
        'sender_name'        => $dat2[$ris1]['sender_name']        ?? '',
        'signature'          => $dat2[$ris1]['signature']          ?? '',
        'approvers_signature'=> $dat2[$ris1]['approvers_signature']?? '',
        'requested_by'       => $dat2[$ris1]['requested_by']       ?? '',
        'farm_stock_route'   => route('farm.stock.check', ['id' => encrypt(intval($ris1))]),
      ];
    }
  }
@endphp

<script id="gate-pass-data" type="application/json">
  {!! json_encode($gatePassData) !!}
</script>

@push('scripts')
<script>
  const gatePassData = JSON.parse(document.getElementById('gate-pass-data').textContent);

  // ── Image zoom ──────────────────────────────────────────────────────────────
  $(document).on('click', '.imageZoomButton', function () {
    Swal.fire({
      imageUrl: $(this).data('imageurl'),
      imageAlt: 'Image',
      showConfirmButton: true,
      customClass: { image: 'zoomable-image' },
      allowOutsideClick: true,
      allowEscapeKey: true,
      imageWidth: 500,
      imageHeight: 500,
    });
  });

  // ── Shared cancel dialog ─────────────────────────────────────────────────────
  function showCancelled() {
    Swal.fire({ title: 'Action Cancelled', icon: 'error', timer: 2000, timerProgressBar: true, showConfirmButton: true });
  }

  // ── Approve ──────────────────────────────────────────────────────────────────
  $(document).on('click', '[id^="showNotif"]:not([id*="Deny"]):not([id*="ForRelease"]):not([id*="InTransit"]):not([id*="Received"])', function (e) {
    e.preventDefault();
    const val = $(this).data('value');
    Swal.fire({
      title: 'Are you sure?', text: 'Do you want to approve this request?',
      icon: 'question', showCancelButton: true,
      confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, I approve!', allowOutsideClick: false, allowEscapeKey: false
    }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('approved', val));
  });

  // ── Deny ─────────────────────────────────────────────────────────────────────
  $(document).on('click', '[id^="showNotifDeny"]', function (e) {
    e.preventDefault();
    const val = $(this).data('value');
    Swal.fire({
      title: 'Are you sure?', text: 'Do you want to deny this request?',
      icon: 'question', showCancelButton: true,
      confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
      confirmButtonText: 'Yes', allowOutsideClick: false, allowEscapeKey: false
    }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('denied', val));
  });

  // ── For Release ───────────────────────────────────────────────────────────────
  $(document).on('click', '[id^="showNotifForRelease"]', function (e) {
    e.preventDefault();
    const val = $(this).data('value');
    Swal.fire({
      title: 'Are you sure?', text: 'Do you want to change the status to For Release?',
      icon: 'question', showCancelButton: true,
      confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
      confirmButtonText: 'Yes', allowOutsideClick: false, allowEscapeKey: false
    }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('for_release', val));
  });

  // ── Received ──────────────────────────────────────────────────────────────────
  $(document).on('click', '[id^="showNotifReceived"]', function (e) {
    e.preventDefault();
    const val = $(this).data('value');
    Swal.fire({
      title: 'Are you sure?', text: 'Do you want to change the status to Received?',
      icon: 'question', showCancelButton: true,
      confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
      confirmButtonText: 'Yes', allowOutsideClick: false, allowEscapeKey: false
    }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('received', val));
  });

  // ── Checkout ──────────────────────────────────────────────────────────────────
  $(document).on('click', '[id^="checkout-btn"]', function (e) {
    e.preventDefault();
    const $btn = $(this);
    const id   = this.id.replace('checkout-btn', '');
    const gp   = gatePassData[id] || {};
    Swal.fire({
      title: 'Are you sure?', text: 'Do you want to checkout this item?',
      icon: 'question', showCancelButton: true,
      confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, Checkout', cancelButtonText: 'Cancel'
    }).then(r => {
      if (!r.isConfirmed) return;
      Swal.fire({
        title: 'How do you want to Checkout?',
        icon: 'question', showDenyButton: true, showCancelButton: true,
        confirmButtonColor: '#223a5e', denyButtonColor: '#fe840e', cancelButtonColor: '#17a2b8',
        confirmButtonText: 'Checkout In Full', denyButtonText: 'Checkout Partially',
        cancelButtonText: 'Check Other Farms', allowOutsideClick: false, allowEscapeKey: false
      }).then(r2 => {
        if (r2.isConfirmed)      window.location.href = $btn.attr('href') + '&checkout=full';
        else if (r2.isDenied)    window.location.href = $btn.attr('href') + '&checkout=partial';
        else if (r2.dismiss === Swal.DismissReason.cancel) window.location.href = gp.farm_stock_route;
      });
    });
  });

  // ── In Transit + Gate Pass print ─────────────────────────────────────────────
  $(document).on('click', '[id^="showNotifInTransit"]', function (e) {
    e.preventDefault();
    const val = $(this).data('value');
    const id  = this.id.replace('showNotifInTransit', '');
    const gp  = gatePassData[id] || {};

    Swal.fire({
      title: 'Are you sure?',
      html: "<h4>Do you want to change the status to In-Transit?</h4><br><h5><span class='text-danger'>Note:</span> This will also print a Gate Pass.</h5>",
      icon: 'question', showCancelButton: true, showConfirmButton: false,
      cancelButtonColor: '#3085d6', cancelButtonText: 'Yes',
      allowOutsideClick: false, allowEscapeKey: false
    }).then(r => {
      if (r.dismiss !== Swal.DismissReason.cancel) return;

      Swal.fire({
        title: '',
        width: '770px',
        showCancelButton: true,
        confirmButtonText: 'Print',
        cancelButtonText: 'Close',
        html: `
          <style>
            @media print {
              .swal2-confirm, .swal2-cancel, .form-container { display: none !important; }
              @page { size: auto; margin: 0; }
            }
          </style>
          <div style="width:100%;">
            <div class="container-fluid" style="border:1px solid black;">
              <div class="row pt-1">
                <div class="col-sm-12 text-center">
                  <img src="https://brooksidegroup.org/wp-content/uploads/2021/04/BGC-transparent.png" alt="BGC LOGO" style="width:200px;height:auto;">
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6"><h3>GATE PASS</h3></div>
              </div>
              <div class="row">
                <div class="col-sm-12 text-left">
                  <h6><br>UNIT: <u>${gp.location} (${gp.abbrvtn})</u>
                    <span class="text-danger float-right">NO: ${String(Math.floor(Math.random()*90000)+10000).padStart(7,'0')}</span>
                  </h6>
                </div>
                <div class="col-sm-12 text-left">
                  <h6>DATE: <u>${new Date().toLocaleDateString('en-US')}</u></h6>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <table class="table table-bordered">
                    <tbody>
                      <tr><td><b>NO.</b></td><td><b>IMAGE</b></td><td><b>ITEM</b></td><td><b>PRODUCT</b></td><td><b>U/M</b></td><td><b>QTY</b></td></tr>
                      ${gp.rows || ''}
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 text-left">
                  <h6>DESTINATION FARM: <u>${gp.location}</u> &nbsp; DEPARTMENT: <u>${gp.department_division}</u></h6>
                </div>
                <div class="col-sm-12 text-left" style="position:relative;">
                  <h6>SENDER'S NAME/SIGNATURE: <u>${gp.sender_name}</u>
                    <img src="${gp.signature}" alt="E-Signature" style="max-width:60px;max-height:70px;position:absolute;top:-10px;left:200px;">
                  </h6>
                </div>
                <div class="col-sm-12 text-left" style="position:relative;">
                  <h6>APPROVED BY: <u>Donna Isla</u>
                    <img src="${gp.approvers_signature}" alt="E-Signature" style="max-width:60px;max-height:70px;position:absolute;top:-35px;left:130px;opacity:0.59;">
                  </h6>
                </div>
                <div class="col-sm-12 text-left form-container">
                  <h6>DELIVERED BY: <input type="text" id="inputDeliveredBy" class="form-control d-inline-block" style="width:auto;border:none;border-bottom:1px solid black;" placeholder="Enter name"></h6>
                </div>
                <div class="col-sm-12 text-left" id="deliveredByText" style="display:none;">
                  <h6>DELIVERED BY: <u id="deliveredByValue"></u></h6>
                </div>
                <div class="col-sm-12 text-left">
                  <h6>RECEIVED BY: <u>${gp.requested_by}</u></h6>
                </div>
                <div class="col-sm-12 text-left form-container">
                  <h6>GUARD ON DUTY: <input type="text" id="inputGuardOnDuty" class="form-control d-inline-block" style="width:auto;border:none;border-bottom:1px solid black;" placeholder="Enter name"></h6>
                </div>
                <div class="col-sm-12 text-left" id="guardOnDutyText" style="display:none;">
                  <h6>GUARD ON DUTY: <u id="guardOnDutyValue"></u></h6>
                </div>
              </div>
            </div>
          </div>`,
        preConfirm: () => {
          document.getElementById('deliveredByValue').textContent = document.getElementById('inputDeliveredBy').value;
          document.getElementById('guardOnDutyValue').textContent = document.getElementById('inputGuardOnDuty').value;
          document.querySelectorAll('.form-container').forEach(el => el.style.display = 'none');
          document.getElementById('deliveredByText').style.display = 'block';
          document.getElementById('guardOnDutyText').style.display = 'block';
          window.print();
          Livewire.emit('in_transit', val);
        }
      });
    });
  });
</script>
@endpush


