@extends('layouts.app')

@section('title')
	{{ __('Category') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Category') }}
						<a href="{{ route('category.list') }}" class="btn btn-primary text-primary">
							<i class="fas fa-list"></i> SHOW LIST
						</a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								@inject('acc', '\App\Http\Controllers\AccessController')
								@if($acc::checkAccess(Auth::id(), 'cat_add'))
									<div class="card-header">
										<h5 class="card-title"><i class="fas fa-plus"></i> Create Category</h5>
									</div>
									@livewire('category.category')
								@endif
							</div>
						</div>
					</div>
                    {{-- <div class="container-fluid">
						<div class="row">
							<div class="col-12">'
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title"><i class="fas fa-list"></i> Sub Category List</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>CATEGORY</th>
                                                    <th>SUB CATEGORY</th>
                                                    <th>DESCRIPTION</th>
                                                    <th>DATE CREATED</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sub_categories as $category)
                                                    <tr>
                                                        <td>{{ $category->id }}</td>
                                                        <td>{{ $category->my_category->category_name }}</td>
                                                        <td>{{ $category->subcategory_name }}</td>
                                                        <td>{{ $category->subcategory_description }}</td>
                                                        <td>{{ $category->created_at }}</td>
                                                        <td><button class="btn btn-info"><i class="fas fa-edit"></i> EDIT</button></td>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
							</div>
						</div>
					</div> --}}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

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



