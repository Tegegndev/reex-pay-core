<div class="col-12">
    <div class="single-form-card">
        <div class="card-main d-flex flex-column flex-lg-row align-items-center p-4">
            <div class="flex-grow-1">
                <div class="mb-3">
                    <h6 class="text-secondary">{{ $referralContent->getLocalizedHeading() }}</h6>
                </div>

                <div class="single-input-inner style-border mt-3">
                    <div class="input-group input-group-right">
                        <input type="text" id="referLink"
                               class="form-control fw-bold text-muted" name="amount"
                               value="{{ auth()->user()->referralLink }}" readonly>
                        <span class="input-group-text input-group-text-right cursor-pointer copyNow"
                              data-clipboard-target="#referLink"
                              data-bs-placement="top" data-bs-toggle="tooltip" title="{{ __('Copy Link') }}"><i
                                class="fa-solid fa-copy"></i>
                                </span>
                    </div>
                    <span class="small color-base fw-500 span-consistent">
                                {{ __(':count people have joined using this URL', ['count' => $referrals->count()]) }}
                            </span>
                </div>

                <ul class="list-group list-group-flush mt-3">
                    @php
                        // Get localized guidelines from the ReferralContent model
                        $positiveGuidelines = $referralContent->getLocalizedPositiveGuidelines();
                        $negativeGuidelines = $referralContent->getLocalizedNegativeGuidelines();
                        
                        // Use defaults if no guidelines found
                        if (empty($positiveGuidelines)) {
                            $positiveGuidelines = [
                                __('Easily share the link on social media platforms.'),
                                __('Promote your link through any marketing channel.')
                            ];
                        }
                        
                        if (empty($negativeGuidelines)) {
                            $negativeGuidelines = [
                                __('Multiple accounts from the same device are not allowed.'),
                                __('Automated signups using bots are prohibited.')
                            ];
                        }
                    @endphp
                    
                    @foreach($positiveGuidelines as $guideline)
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fa-solid fa-circle-check text-success me-2"></i>{{ $guideline }}
                        </li>
                    @endforeach
                    
                    @foreach($negativeGuidelines as $guideline)
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fa-solid fa-circle-xmark text-danger me-2"></i>{{ $guideline }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="text-center referral-image d-none d-lg-block">
                <img src="{{ asset($referralContent->image_path) }}"
                     alt="{{ __('Referral Program Illustration') }}"
                     class="img-fluid">
            </div>

        </div>
        <div class="card-main ps-0">
            @include('frontend.user.referral.partials._tree')
        </div>
    </div>
</div>