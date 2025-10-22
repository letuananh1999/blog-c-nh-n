<section id="sidebar">
		<a href="{{ route('admin.index') }}" class="brand">
			<i class='bx bxs-smile'></i>
			<span class="text">AdminHub</span>
		</a>
		<ul class="side-menu top">
			<li @if(Route::is('admin.index')) class="active" @endif>
				<a href="{{ route('admin.index') }}">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li @if(Route::is('admin.categories.*')) class="active" @endif>
				<a href="{{ route('admin.categories.index')}}">
					<i class='bx bxs-shopping-bag-alt' ></i>
					<span class="text">Category</span>
				</a>
			</li>
			<li @if(Route::is('admin.posts.*')) class="active" @endif>
				<a href="{{ route('admin.posts.index')}}">
					<i class='bx bxs-doughnut-chart' ></i>
					<span class="text">Post</span>
				</a>
			</li>
			<li @if(Route::is('admin.users.*')) class="active" @endif>
				<a href="{{ route('admin.users.index')}}">
					<i class='bx bxs-group' ></i>
					<span class="text">User</span>
				</a>
			</li>
			<li @if(Route::is('admin.comments.*')) class="active" @endif>
				<a href="{{ route('admin.comments.index')}}">
					<i class='bx bxs-message-dots' ></i>
					<span class="text">Comment</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#">
					<i class='bx bxs-cog' ></i>
					<span class="text">Settings</span>
				</a>
			</li>
			<li>
				<a href="#" class="logout">
					<i class='bx bxs-log-out-circle' ></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
</section>
	<!-- SIDEBAR -->