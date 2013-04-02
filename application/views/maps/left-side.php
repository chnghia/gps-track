	<div class="wrap-left">
		<div class="wrap-innercontent">
			<div class="clear02"></div>
			<!-- Column 2 start -->
			<form id="point_maps" action="<? echo site_url('/maps/point'); ?>" method="GET">
			<fieldset class="frmfields">
                <dl>
                <div class="menu-list">
                <span></span>
				<dt style="text-align:left;">Devices List</dt>
                </div>
				<? foreach ($device_list as $imei=>$val) : ?> 
                <div class="menulbg">
                <div class="list-device">
				<dd>
				<? echo form_checkbox('imei', $imei, ($val['display'])? TRUE: FALSE, 'id="imei_'.$imei.'"'); ?>
				<label for="imei_<?= $imei; ?>"><? echo ($val['default'])? '<strong>'.$val['number_plate'].'</strong>' : $val['number_plate']; ?></label>
				</dd>
				<? endforeach; ?>
                </div>
                </div>
				</dl>
			</fieldset>
			</form>
			<!-- Column 2 end -->
			<?/*
			<div style="margin-top:15px;">
				<input id="auto_refresh" type="checkbox"/><label for="auto_refresh">AUTO REFRESH</label>
			</div>*/?>
            <div class="bottom"></div>
		</div>
	</div>