<aside class="main-sidebar">

    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ auth()->user()->name }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-th"></i><span>@lang('site.dashboard')</span></a>
            </li>
            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{ route('dashboard.users.index') }}"><i
                            class="fa fa-th"></i><span>@lang('site.users')</span></a></li>
            @endif
            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{ route('dashboard.student_details.index') }}"><i
                            class="fa fa-th"></i><span>@lang('site.student_graduated')</span></a></li>
            @endif
            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{ route('dashboard.jobs.index') }}"><i
                            class="fa fa-th"></i><span>@lang('site.jobs')</span></a></li>
            @endif
            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{ route('dashboard.companies.index') }}"><i
                            class="fa fa-th"></i><span>@lang('site.companies')</span></a></li>
            @endif
            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{ route('dashboard.posts.index') }}"><i
                            class="fa fa-th"></i><span>@lang('site.posts')</span></a></li>
            @endif
            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{ route('dashboard.trainings.index') }}"><i
                            class="fa fa-th"></i><span>@lang('site.trainings')</span></a></li>
            @endif


            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span>@lang("site.setting")</span>
                    <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    @if (auth()->user()->hasRole('super_admin'))
                    <li>
                        <a href="{{ route('dashboard.sliders.index') }}"><i class="fa fa-picture-o"></i> @lang("site.sliders")</a>
                    </li>

                    @endif
{{--                    <li>--}}
{{--                        <a href="../charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a href="../charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a href="../charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a>--}}
{{--                    </li>--}}
                </ul>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out"></i>{{ __('site.Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>

    </section>

</aside>

