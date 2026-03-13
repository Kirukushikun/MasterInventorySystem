@extends('layouts.app')

@section('title')
	{{ __('For Approval List') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Manage Requests') }}
					</h4>
				</div>
				<div class="content">
					<hr>
					@livewire('for-approval.for-approval-list')
				</div>
			</div>
		</div>
		@inject('acc', '\App\Http\Controllers\AccessController')
		@if($acc::checkAccess(Auth::id(), 'forapprovals_update_mod'))
			<div class="row">
				<div class="col-md-12">
					<div class="header">
						<h4 class="title">
							{{ __('For Approvals (Farm Level Inventory Update)') }}
						</h4>
					</div>
					<div class="content">
						<hr>
						@livewire('for-approval.for-approval-inventory-update')
					</div>
				</div>
			</div>
		@endif
        @if($acc::checkAccess(Auth::id(), 'forapprovals_inv_create_mod'))
			<div class="row">
				<div class="col-md-12">
					<div class="header">
						<h4 class="title">
							{{ __('For Approvals (Inventory Creation/Additions)') }}
						</h4>
					</div>
					<div class="content">
						<hr>
						@livewire('for-approval.for-approval-inv-create')
					</div>
				</div>
			</div>
		@endif
	</div>
</div>
@endsection

@section('scriptss')
	<script>

        $(document).ready(function() {
		    var table1 = $('#items_tables').DataTable({
		        "order": [[1, 'asc']], // Set default sorting by the ID column
		        "dom": 'lfrtip', // Customize the table layout if needed
		    });

		    // Apply column-specific search filtering
		    table1.columns().every(function(index) {
		        if (index !== 0 && index !== table1.columns().indexes().length - 1 && index !== table1.columns().indexes().length - 1) {
		            var column = this;

		            // Create the input element for filtering
		            var input = $('<input type="text" style="height: 17px; width: 200px;" placeholder="Search" class="form-control form-control-sm">')
		                .appendTo($(column.header()))
		                .on('keyup change', function() {
		                    column.search($(this).val(), false, false, true).draw();
		                });
		        }
		    });

		    // Initialize FixedColumns extension
		    new $.fn.dataTable.FixedColumns(table1, {
		        rightColumns: 1 // Number of columns to fix on the right
		    });
		});



        $(document).ready(function() {
            var table2 = $('#for_update_table').DataTable({
                "order": [[1, 'asc']], // Set default sorting by the ID column
                "dom": 'lfrtip', // Customize the table layout if needed
            });

            // Apply column-specific search filtering
            table2.columns().every(function(index) {
                if (index !== 0 && index !== table2.columns().indexes().length - 1 && index !== table2.columns().indexes().length - 1) {
                    var column = this;

                    // Create the input element for filtering
                    var input = $('<input type="text" style="height: 17px; width: 200px;" placeholder="Search" class="form-control form-control-sm">')
                        .appendTo($(column.header()))
                        .on('keyup change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        });
                }
            });

            // Initialize FixedColumns extension
            new $.fn.dataTable.FixedColumns(table2, {
                rightColumns: 1 // Number of columns to fix on the right
            });
        });

        $(document).ready(function() {
            var table3 = $('#forapprovals_inv_create_mod').DataTable({
                "order": [[1, 'asc']], // Set default sorting by the ID column
                "dom": 'lfrtip', // Customize the table layout if needed
            });

            // Apply column-specific search filtering
            table3.columns().every(function(index) {
                if (index !== 0 && index !== table3.columns().indexes().length - 1 && index !== table3.columns().indexes().length - 1) {
                    var column = this;

                    // Create the input element for filtering
                    var input = $('<input type="text" style="height: 17px; width: 200px;" placeholder="Search" class="form-control form-control-sm">')
                        .appendTo($(column.header()))
                        .on('keyup change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        });
                }
            });

            // Initialize FixedColumns extension
            new $.fn.dataTable.FixedColumns(table3, {
                rightColumns: 1 // Number of columns to fix on the right
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
@endsection



