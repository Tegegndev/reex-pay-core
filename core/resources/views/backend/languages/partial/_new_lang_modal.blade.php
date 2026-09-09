<div class="modal fade" id="new-lang-modal" tabindex="-1" role="dialog" aria-labelledby="modal-default" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="h6 modal-title">{{ __('Create Language') }}</h2>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 col-xl-12">
						<form method="POST" action="{{ route('admin.language.store') }}" enctype="multipart/form-data">
							@csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="flag" class="form-label">{{ __('Language Flag') }}</label>
                                    <x-img name="flag"/>
                                </div>
                            </div>

							<div class="row">
								<div class="col-md-12 mb-3">
									<div>
										<label class="form-label"  for="language_name">{{ __('Language Name') }}</label>
										<input class="form-control" name="language_name" id="language_name"  type="text" placeholder="EX: English" required>
									</div>
								</div>
								<div class="col-md-12 mb-3">
									<div>
										<label  class="form-label" for="language_code">{{ __('Language Code') }}</label>
										<input class="form-control" name="language_code" id="language_code"  type="text" placeholder="EX: en" required>
									</div>
								</div>
							</div>

							<div class="row">

								<div class="col-md-4 mb-3 mt-2">
									<div class="card">
										<div class="form-check form-switch card-body p-2  border rounded d-flex align-items-center">
											<label class="form-check-label flex-grow-1" for="default">{{ __('Default') }}</label>
											<input class="form-check-input coevs-switch me-2 flex-shrink-0" type="checkbox" value="1" name="is_default"  id="default">
										</div>
									</div>
								</div>
								<div class="col-md-4 mb-3 mt-2">
                                    <div class="card">
                                        <div class="form-check form-switch card-body p-2  border rounded d-flex align-items-center">
                                            <label class="form-check-label flex-grow-1" for="status">{{ __('Status') }}</label>
                                            <input class="form-check-input coevs-switch me-2 flex-shrink-0" type="checkbox" role="switch" name="status" value="1" id="status">
                                        </div>
                                    </div>
								</div>

							</div>

							<div class="mt-3 text-end">
								<button class="btn btn-primary" type="submit"><x-icon name="check" height="20"/> {{ __('Save Now') }}</button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
