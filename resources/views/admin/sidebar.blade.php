<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">HEADER</li>
            <!-- Optionally, you can add icons to the links -->
            @if(Auth::user()->role_id == 1)
                <li class="{{Route::currentRouteName() == 'admin.dashboard' ? 'active' : ' '}}"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard "></i> <span>Dashboard</span></a></li>
                <li class="{{Route::currentRouteName() == 'admin.get_teacher_leave_management' ? 'active' : ' '}} treeview">
                    <a href="#"><i class="fa fa-link"></i> <span>Leave Management</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('admin.get_teacher_leave_management') }}">Teacher</a></li>
                        <li><a href="{{ route('admin.get_student_leave_management') }}">Student</a></li>
                    </ul>
                </li>
                <li class="{{Route::currentRouteName() == 'admin.get_teacher_management' ? 'active' : ' '}}"><a href="{{ route('admin.get_teacher_management') }}"><i class="fa fa-link"></i> <span>Teacher Management</span></a></li>
                <li class="{{Route::currentRouteName() == 'admin.get_student_management' ? 'active' : ' '}}"><a href="{{ route('admin.get_student_management') }}"><i class="fa fa-link"></i> <span>Student Management</span></a></li>


            @elseif(Auth::user()->role_id == 2)
                <li class="{{Route::currentRouteName() == 'teacher.dashboard' ? 'active' : ' '}}"><a href="{{ route('teacher.dashboard') }}"><i class="fa fa-dashboard "></i> <span>Dashboard</span></a></li>
                <li class="{{Route::currentRouteName() == 'teacher.get_student_leave_management' ? 'active' : ' '}}"><a href="{{ route('teacher.get_student_leave_management') }}"><i class="fa fa-link"></i> <span>Student Leave Management</span></a></li>
                <li class="{{Route::currentRouteName() == 'teacher.get_leave_management' ? 'active' : ' '}}"><a href="{{ route('teacher.get_leave_management') }}"><i class="fa fa-link"></i> <span>Leave Management</span></a></li>
            @else
                <li class="{{Route::currentRouteName() == 'student.dashboard' ? 'active' : ' '}}"><a href="{{ route('student.dashboard') }}"><i class="fa fa-dashboard "></i> <span>Dashboard</span></a></li>
                <li class="{{Route::currentRouteName() == 'student.get_leave_management' ? 'active' : ' '}}"><a href="{{ route('student.get_leave_management') }}"><i class="fa fa-link"></i> <span>Leave Management</span></a></li>
            @endif

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>