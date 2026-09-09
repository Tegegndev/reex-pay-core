@php($role = $staff->roles->first())
<div class="col-xxl-3 col-lg-4 col-md-6 col-12">
	<div class="crancy-userprofile mg-top-40">
		<div class="crancy-userprofile__header">
			<img src="{{asset($staff->cover)}}" alt="#">
		</div>
		<div class="crancy-userprofile__user">
			<div class="crancy-userprofile__content">
				<div class="crancy-userprofile__thumb">
					<img src="{{ asset($staff->profile) }}" alt="#">
				</div>
				<div class="crancy-userprofile__info">
					<h4 class="crancy-userprofile__info-title">{{ $staff->name }}</h4>
					<div class="crancy-userprofile__location">
						<x-icon name="email" height="24" width="19"/>
						<p class="crancy-userprofile__info-text">{{ $staff->email }}</p>
					</div>
					<div class="crancy-achievement">
						<div class="crancy-achievement__single">
							<x-icon name="role-pink" height="32" width="32"/>
							<div class="crancy-achievement__content">
								<h5 class="crancy-achievement__title"><a href="{{ route('admin.role.edit', $role->id) }}" target="_blank">{{ title($role->name) }}</a><span>{{ __('Role') }}</span></h5>
							</div>
						</div>
						<div class="crancy-achievement__single mx-0">
							<x-icon name="badge" height="32" width="32"/>
							<div class="crancy-achievement__content">
								<h5 class="crancy-achievement__title">
									<div class="crancy-paymentm__badge crancy__{{ $staff->status == 1 ? 'ok' : 'error' }}">{{ $staff->status == 1 ? 'Active' : 'Inactive' }}</div>
									<span>{{ __('Status') }}</span>
								</h5>
							</div>
						</div>
					</div>
					@if($staff->id == Auth::user()->id)
						<a href="{{ route('admin.profile.info') }}" class="crancy-achievement__btn" >
							<x-icon name="manage-profile" height="24" width="19"/>{{ __('Manage') }}</a>
					@else
						<a class="crancy-achievement__btn staff-edit"  data-id="{{ $staff->id }}">
							<x-icon name="manage-profile" height="24" width="19"/>{{ __('Manage') }}</a>
					@endif
					
				</div>
			</div>
		
		</div>
	</div>
</div>