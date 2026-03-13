<form wire:submit.prevent="set_access" autocomplete="off">
    @csrf
    <table id="access_table" class="table table-bordered table-hover text-nowrap" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">MODULE NAME</th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            @php $ctr = 1; @endphp
            @foreach($modules_list as $outer_key => $outer_value)
              <tr>
                <td class="text-center">{{ $ctr }}</td>
                <td class="text-center">{{ strtoupper($outer_key) }}</td>
                @foreach($outer_value as $inner_key => $inner_value)
                  <td class="text-center" style="vertical-align: middle;">
                    @if(!is_null($inner_value) || $inner_value != null)
                      <div class="form-group">
                        <label for="{{ $inner_value }}">{{ strtoupper($inner_key) }}</label>
                        <input id="{{ $inner_value }}" type="checkbox" name="{{ $inner_value }}" value="{{ $inner_value }}" wire:model="access" wire:click="chk_box" class="form-control">
                      </div>
                    @endif
                  </td>
                @endforeach
              </tr>
              @php $ctr++; @endphp
            @endforeach
        </tbody>
    </table>
    <div class="d-flex flex-row-reverse">
        <input type="submit" class="btn btn-outline-secondary" value="Set Access">&nbsp;
        <a href="{{ route('access') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
    </div>
<div>
    @dump($userID, $temp_acc)
</div>
</form>



@push('scripts')
  <script>
    $(document).ready(function () {
      jQuery('#access_table').DataTable();
    });
    document.addEventListener('DOMContentLoaded', function () {
      $(document).ready(function () {
        jQuery('#access_table').DataTable();
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
