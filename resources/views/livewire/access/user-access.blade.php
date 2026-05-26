<div class="card mims-form-card">
  <form wire:submit.prevent="set_access" autocomplete="off" class="mims-form">
    @csrf
    <div class="card-body mims-form-body">
      <div class="table-responsive">
        <table class="table table-hover" style="width: 100%;">
          <thead class="thead-light">
            <tr>
              <th class="text-center text-muted" style="width: 50px; font-weight: 500;">#</th>
              <th style="font-weight: 600; letter-spacing: 0.05em; width: 220px;">MODULE</th>
              <th class="text-center" style="font-weight: 600; letter-spacing: 0.05em; width: 100px;">ENABLE</th>
              <th style="font-weight: 600; letter-spacing: 0.05em;">PERMISSIONS</th>
            </tr>
          </thead>
          <tbody>
            @php $ctr = 1; @endphp
            @foreach($modules_list as $outer_key => $outer_value)
              <tr>
                <td class="text-center align-middle text-muted">{{ $ctr }}</td>
                <td class="align-middle" style="font-weight: 600;">{{ strtoupper($outer_key) }}</td>

                {{-- MODULE toggle: controls sidebar visibility --}}
                <td class="text-center align-middle">
                  @if(!is_null($outer_value['MODULE']))
                    <div class="custom-control custom-switch d-inline-block">
                      <input type="checkbox" class="custom-control-input" id="{{ $outer_value['MODULE'] }}" name="{{ $outer_value['MODULE'] }}" value="{{ $outer_value['MODULE'] }}" wire:model="access" wire:click="chk_box">
                      <label class="custom-control-label" for="{{ $outer_value['MODULE'] }}"></label>
                    </div>
                  @else
                    <span class="text-muted" style="font-size: 0.75rem;">—</span>
                  @endif
                </td>

                {{-- Action permissions: everything except MODULE --}}
                <td class="align-middle">
                  <div class="d-flex flex-wrap" style="gap: 0.5rem 1.5rem;">
                    @foreach($outer_value as $inner_key => $inner_value)
                      @if($inner_key !== 'MODULE' && !is_null($inner_value))
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="{{ $inner_value }}" name="{{ $inner_value }}" value="{{ $inner_value }}" wire:model="access" wire:click="chk_box">
                          <label class="custom-control-label text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.04em;" for="{{ $inner_value }}">{{ $inner_key }}</label>
                        </div>
                      @endif
                    @endforeach
                  </div>
                </td>
              </tr>
              @php $ctr++; @endphp
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mims-form-actions">
      <a href="{{ route('access') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
      <button type="submit" class="btn btn-primary" style="max-width: 120px;">
        <i class="fas fa-check"></i> Set Access
      </button>
    </div>
  </form>
</div>

@push('scripts')
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
@endpush
