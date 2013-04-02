<!-- Header -->
<div id="header">
	<div id="header-logo">
		<h1 id="logo">
			<a href="<? echo site_url(''); ?>" title="<?echo $this->lang->line('site_name'); ?>"><span></span>
				<?echo $this->lang->line('site_name'); ?> </a>
		</h1>
		<hr class="noscreen" />
	</div>
	<!-- /header -->
	<!-- Tray -->
	<div id="tray">
		<div class="auth">
		<? if ($logged) : ?>
			Chào <strong><? echo $user_name; ?> </strong> <span>|</span> <a href="#" id="logout-lnk">Thoát</a>
		<? else : ?>
			<!-- <a href="#" id="signup-lnk"> --><strong>Đăng ký</strong><!-- </a> --> <span>|</span> <a href="#" id="login-lnk">Đăng nhập</a>
		<? endif; ?>
		</div>
		<ul class="box">
			<li <?= ($activetab == 'home')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps'); ?>">Trang chủ</a></li>
			<!-- Active page -->
			<li <?= ($activetab == 'location')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps/location'); ?>">Vị trí</a></li>
			<li <?= ($activetab == 'path')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps/path'); ?>">Đường đi</a></li>
			<li <?= ($activetab == 'summary')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps/summary'); ?>">Thống kê</a></li>
			<li <?= ($activetab == 'options')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps/options'); ?>">Hệ thống</a></li>
			<li <?= ($activetab == 'help')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps/help'); ?>">Giúp đỡ</a></li>
			<li <?= ($activetab == 'contact')? 'id="tray-active"': '';?>><a href="<? echo site_url('/gps/contact'); ?>">Liên hệ</a></li>
		</ul>
		<hr class="noscreen" />
	</div>
	<!-- /tray -->
</div>
				<?/*
<div id="hdrwrap">
	<div class="row">
	<h1><a href="/" title="<?echo $this->lang->line('site_name'); ?>"><?echo $this->lang->line('site_name'); ?></a>
    <span id="slogan">Success in you hand</span>
    </h1>
	<div class="main-menu">
		<div class="auth">
			<? if ($logged) : ?>
			Welcome <strong><? echo $user_name; ?></strong>
			<span>|</span>
			<a href="#" id="logout-lnk">Log Out</a>
			<? else : ?>
			<a href="#" id="signup-lnk">Sign Up</a>
			<span>|</span>
			<a href="#" id="login-lnk">Log In</a>
			<? endif; ?>
		</div>
        <div class="menu-left">
		<ul>
			<li id="gps_position">
				<a href="#">GPS TRACKING</a>
				<ul>
					<li><a href="<? echo site_url('/maps/point'); ?>">GPS POSITION</a></li>
					<li><a href="<? echo site_url('/maps/path'); ?>">GPS PATH</a></li>
				</ul>
			</li>
			<li id="gps_manage" >
				<a href="#">GPS MANAGEMENT</a>
				<ul>
					<li><a href="<? echo site_url('/devices/Display_devices'); ?>">GPS MANAGEMENT</a></li>
					<li><a href="<? echo site_url('/devices/command'); ?>">GPS COMMAND</a></li>
				</ul>
			</li>
			<li id="about">
				<a href="#">ABOUT</a>
				<ul>
					<li><a href="<? echo site_url('/pages/mission'); ?>">MISSION &amp; HISTORY</a></li>
					<li><a href="<? echo site_url('/pages/sponsors'); ?>">SPONSORS &amp; PARTNERS</a></li>
					<li><a href="<? echo site_url('/pages/organization'); ?>">MEMBER ORGANIZATIONS</a></li>
				</ul>
			</li>
			<?/*<li id="support">
				<a href="#">SUPPORT</a>
				<ul>
					<li><a href="#">VIEW SPONSORS</a></li>
				</ul>
			</li>* /?>
			<li id="contact"><a href="<? echo site_url('/pages/contact'); ?>" class="red">CONTACT US</a></li>
		</ul>
        </div>
		<div class="relax"></div>
        </div>
	</div>
</div>
*/?>