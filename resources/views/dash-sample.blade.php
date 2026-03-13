@extends('layouts.app')

@section('title')
	Dashboard
@endsection

@section('content')
	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<div class="card form-card-1">
					    <div class="card-body">
					        <div class="d-flex align-items-start">
					            <div class="mr-3">
					                <i class="fas fa-inventory fa-2x"></i>
					            </div>
					            <div class="flex-grow-1">
					                <h5 class="card-title text-primary">{{ __('Total Items')}}</h5>
					                <p class="card-text">Description</p>
					            </div>
					        </div>
					    </div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card form-card-2">
					    <div class="card-body">
					        <div class="d-flex align-items-start">
					            <div class="mr-3">
					                <i class="fas fa-box fa-2x"></i>
					            </div>
					            <div class="flex-grow-1">
					                <h5 class="card-title text-warning">{{ __('Low Stock Items')}}</h5>
					                <p class="card-text">Description</p>
					            </div>
					        </div>
					    </div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card form-card-3">
					    <div class="card-body">
					        <div class="d-flex align-items-start">
					            <div class="mr-3">
					                <i class="fas fa-box fa-2x"></i>
					            </div>
					            <div class="flex-grow-1">
					                <h5 class="card-title text-danger">{{ __('Out Of Stock')}}</h5>
					                <p class="card-text">Description</p>
					            </div>
					        </div>
					    </div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card form-card-4">
					    <div class="card-body">
					        <div class="d-flex align-items-start">
					            <div class="mr-3">
					                <i class="fas fa-box fa-2x"></i>
					            </div>
					            <div class="flex-grow-1">
					                <h5 class="card-title text-success">{{ __('Most Stock product')}}</h5>
					                <p class="card-text">Description</p>
					            </div>
					        </div>
					    </div>
					</div>
				</div>		
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="card ">
						<div class="card-header ">
							<h4 class="card-title">Inventory</h4>
							<p class="card-category">Last Campaign Performance</p>
						</div>
						<div class="card-body ">
							<div id="chartPreferences" class="ct-chart ct-perfect-fourth"></div>
							<div class="legend">
								<i class="fa fa-circle text-info"></i> Open
								<i class="fa fa-circle text-danger"></i> Bounce
								<i class="fa fa-circle text-warning"></i> Unsubscribe
							</div>
							<hr>
							<div class="stats">
								<i class="fa fa-clock-o"></i> Campaign sent 2 days ago
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card ">
						<div class="card-header ">
							<h4 class="card-title">Users Frequent Visits</h4>
							<p class="card-category">24 Hours performance</p>
						</div>
						<div class="card-body ">
							<div id="chartHours" class="ct-chart"></div>
						</div>
						<div class="card-footer ">
							<div class="legend">
								<i class="fa fa-circle text-info"></i> Open
								<i class="fa fa-circle text-danger"></i> Click
								<i class="fa fa-circle text-warning"></i> Click Second Time
							</div>
							<hr>
							<div class="stats">
								<i class="fa fa-history"></i> Updated 3 minutes ago
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="card ">
						<div class="card-header ">
							<h4 class="card-title">2023 Delivered</h4>
							<p class="card-category">All products including Taxes</p>
						</div>
						<div class="card-body ">
							<div id="chartActivity" class="ct-chart"></div>
						</div>
						<div class="card-footer ">
							<div class="legend">
								<i class="fa fa-circle text-info"></i> Tesla Model S
								<i class="fa fa-circle text-danger"></i> BMW 5 Series
							</div>
							<hr>
							<div class="stats">
								<i class="fa fa-check"></i> Data information certified
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card  card-tasks">
						<div class="card-header ">
							<h4 class="card-title">Request</h4>
							<p class="card-category">Backend development</p>
						</div>
						<div class="card-body ">
							<div class="table-full-width">
								<table class="table">
									<tbody>
										<tr>
											<td>
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox" value="">
														<span class="form-check-sign"></span>
													</label>
												</div>
											</td>
											<td>Sign contract for "What are conference organizers afraid of?"</td>
											<td class="td-actions text-right">
												<button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td>
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox" value="" checked>
														<span class="form-check-sign"></span>
													</label>
												</div>
											</td>
											<td>Lines From Great Russian Literature? Or E-mails From My Boss?</td>
											<td class="td-actions text-right">
												<button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td>
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox" value="" checked>
														<span class="form-check-sign"></span>
													</label>
												</div>
											</td>
											<td>
												Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit
											</td>
											<td class="td-actions text-right">
												<button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td>
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox" checked>
														<span class="form-check-sign"></span>
													</label>
												</div>
											</td>
											<td>Create 4 Invisible User Experiences you Never Knew About</td>
											<td class="td-actions text-right">
												<button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td>
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox" value="">
														<span class="form-check-sign"></span>
													</label>
												</div>
											</td>
											<td>Read "Following makes Medium better"</td>
											<td class="td-actions text-right">
												<button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td>
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox" value="" disabled>
														<span class="form-check-sign"></span>
													</label>
												</div>
											</td>
											<td>Unfollow 5 enemies from twitter</td>
											<td class="td-actions text-right">
												<button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="card-footer ">
							<hr>
							<div class="stats">
								<i class="now-ui-icons loader_refresh spin"></i> Updated 3 minutes ago
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scriptss')
	<script type="text/javascript">
		$(document).ready(function () {
	      document.querySelectorAll('.form-card-1').forEach(function (input) {
	          input.style.borderRight = '6px solid #007bff';
	      });
	      document.querySelectorAll('.form-card-2').forEach(function (input) {
	          input.style.borderRight = '6px solid #ffc107';
	      });
	      document.querySelectorAll('.form-card-3').forEach(function (input) {
	          input.style.borderRight = '6px solid #dc3545';
	      });
	      document.querySelectorAll('.form-card-4').forEach(function (input) {
	          input.style.borderRight = '6px solid #28a745';
	      });
	    });
	</script>

	{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>
	<script>
		$.ajax({
			type: "GET",
			url: "{{ config('app.root_domain') . config('app.user_details_slug') . \Crypt::encryptString(Auth::user()->id) }}",
			dataType: 'json',
			success: function(response){
				document.getElementById('fullname').innerHTML = response['first_name'] + " " + response['last_name'];
				document.getElementById('email').innerHTML = response['email'];
			}
		});
	</script> --}}
@endsection



