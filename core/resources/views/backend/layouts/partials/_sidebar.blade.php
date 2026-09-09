<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    {{-- Sidebar Header --}}
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
            <img class="sidebar-brand-full" height="32" src="{{ asset(setting('light_logo')) }}" alt="Logo">
            <img class="sidebar-brand-narrow" width="32" height="32" src="{{ asset(setting('small_logo')) }}" alt="Small Logo">
        </div>
        <button class="btn-close d-lg-none" type="button"
                data-coreui-dismiss="offcanvas" data-coreui-theme="dark"
                aria-label="Close"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
        </button>
    </div>
    
    {{-- Sidebar Navigation --}}
    <ul class="sidebar-nav overflow-auto" data-coreui="navigation">
        @foreach(config('admin_menus') as $section)
            @php
                $visibleMenus = collect($section['menus'] ?? [])->filter(function ($menu) {
					$permission = $menu['permission'] ?? null;
					$hasPermission = is_null($permission) || in_array($permission, session('admin_permissions', []));
	
					if ($menu['type'] === 'groups') {
						$hasVisibleSub = collect($menu['sub_menus'] ?? [])->filter(function ($sub) {
							$subPerm = $sub['permission'] ?? $sub['can'] ?? null;
							return is_null($subPerm) || in_array($subPerm, session('admin_permissions', []));
						})->isNotEmpty();
						return $hasPermission && $hasVisibleSub;
					}
	
					return $hasPermission;
				});
            @endphp
            
            @if($visibleMenus->isNotEmpty())
                @if(!empty($section['label']))
                    <li class="nav-title">{{ __($section['label']) }}</li>
                @endif
                
                @foreach($visibleMenus as $menu)
                    @if($menu['type'] === 'single')
                        <li class="nav-item">
                            <a class="nav-link {{ isActive($menu['route']) }}" href="{{ route($menu['route']) }}">
                                <x-icon name="{{ $menu['icon'] }}" class="nav-icon"/>
                                {{ __($menu['label']) }}
                            </a>
                        </li>
                    @elseif($menu['type'] === 'groups')
                        <li class="nav-group {{ isActive(array_column($menu['sub_menus'], 'route'), null, 'show') }}">
                            <a class="nav-link nav-group-toggle" href="#">
                                <x-icon name="{{ $menu['icon'] }}" class="nav-icon"/>
                                {{ __($menu['label']) }}
                            </a>
                            <ul class="nav-group-items compact">
                                @foreach($menu['sub_menus'] as $sub)
                                    @php
                                        $subPermission = $sub['permission'] ?? $sub['can'] ?? null;
										$hasSubPermission = is_null($subPermission) || in_array($subPermission, session('admin_permissions', []));
                                    @endphp
                                    
                                    @if($hasSubPermission)
                                        <li class="nav-item">
                                            <a class="nav-link {{ isActive($sub['route'], $sub['params'] ?? []) }}"
                                               href="{{ route($sub['route'], $sub['params'] ?? []) }}">
                                            <span class="nav-icon">
                                                <x-icon name="sub" class="icon"/>
                                            </span>
                                                {{ __($sub['label']) }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach
    </ul>
    
    
    {{-- Sidebar Footer --}}
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>
@push('scripts')
    <script>
        
    
    
    
    </script>
@endpush