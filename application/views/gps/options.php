    <!-- Columns -->
		<script type="text/javascript">
			var treeData = [
						<? $i=0;foreach ($device_groups as $key=>$group) : ?>
							{title: "<?= $key . " (".count($group).")"; ?>", isFolder: true, hideCheckbox: true, key: "group_<?=$i;?>", <?= ($i==0)? 'expand: true,' : ''; ?>
								children: [
									<? foreach ($group as $device) : ?>
									{title: "<?= $device['number_plate']; ?>", icon: "web-car-node.png", key: "imei_<?= $device['imei']; ?>" },
									<? endforeach; ?>
								]
							},
						<? $i++;endforeach; ?>
						];
		</script>
		<script type="text/javascript">
			var outerLayout, middleLayout, innerLayout_Center, innerLayout_South;
			var availableGroups = [
									<? foreach ($device_groups as $key=>$group) : ?>
										"<?= $key; ?>",
									<? endforeach; ?>
								  ];
			<!--
			$(document).ready(function () {
				<? if ($device_edit && $logged) : ?>
				$( ".button_clss" ).button();
				<? else : ?>
				$( ".button_clss" ).button({disabled: true});
				<? endif;?>
				$( "#gps_group" ).autocomplete({
					source: availableGroups
				});
				$( '#edit_submit' ).click(function(evt) {
					evt.preventDefault();

					var device_id = jQuery('#device-management #device_id').val();
					var number_plate = jQuery('#device-management #number_plate').val();
					var gps_group = jQuery('#device-management #gps_group').val();
					var avatar = jQuery('#device-management input:radio[name=avatar]:checked').val();
					
					jQuery.getJSON(base_url + "/gps/options", { act : "edit-save", device_id: device_id, number_plate: number_plate, gps_group: gps_group, avatar: avatar }, function(data){
						if (!data.success) {
							open_dialog("<p style='text-align: center; color: red;'>Có lỗi khi cập nhật.</p>");
							return;
						}
						/*
						setInterval(function() {
							$('#responsecontainer').fadeOut("slow").load('response.php').fadeIn("slow");
						}, 10000);
						*/
						$( "#device-management .message" ).html("Cập nhật thành công. Tự động hiển thị lại trang trong 3 giây ...").addClass( "ui-state-highlight" );
						setInterval(function() {
							location.reload();
						}, 3000); 
						// END getJSON
					});
				});
			});
			//-->
		</script>
	<div id="cols" class="box">
		<!-- Content -->
		<div id="content">
				<div id="container">
					<div id="container-content" class="pane ui-layout-center">
						<div id="" class="middle-center"><!-- Inner-Center Layout Container -->
							<!-- <div class="ui-widget-header summary-header" style="border: 0;">HỆ THỐNG</div> -->
							<div id="tabs-options-div" class="no_scrollbar no_border" style="height:100%;">
								<UL class="tabs-options-ul">
									<LI><A href="#tabs-options-1"><SPAN>Thiết lập thiết bị</SPAN></A></LI>
									<LI><A href="#tabs-options-2"><SPAN>Thiết lập khu vực</SPAN></A></LI>
									<LI><A href="#tabs-options-3"><SPAN>Thiết lập bảo trì</SPAN></A></LI>
								</UL>
								<DIV id="tabs-options-1" class="add_scrollbar">
									<div id="" class="ui-widget ui-widget-content ui-corner-all" style="height:260px">
										<!-- Devices management -->
										<div id="device-management">
										<form id="device-man-form" method="post" action="">
										<input type="hidden" name="device_id" id="device_id" value="<?= $device_edit['id']; ?>"></input>
										<fieldset>
										<p class="message" style="margin: 5px 0 10px;"></p>
										<dl>
											<dt><label for="gps_name">Thiết bị GPS</label></dt>
											<dd><input type="text" name="gps_name" id="gps_name" class="text ui-widget-content ui-corner-all ui-state-hover" value="<?= $device_edit['product_name']; ?>" READONLY /></dd>
										</dl>
										<dl>
											<dt><label for="gps_imei">Mã thiết bị GPS</label></dt>
											<dd><input type="text" name="gps_imei" id="gps_imei" class="text ui-widget-content ui-corner-all ui-state-hover" value="<?= $device_edit['imei']; ?>" READONLY /></dd>
										</dl>
										<dl>
											<dt><label for="number_plate">Số xe</label></dt>
											<dd><input type="text" name="number_plate" id="number_plate" class="text ui-widget-content ui-corner-all" value="<?= $device_edit['number_plate']; ?>" />
											<span class="helptip" title="Đặt biển số xe cho thiết bị.">Đặt biển số xe cho thiết bị.</span></dd>
										</dl>
										<dl>
											<dt><label for="gps_group">Nhóm</label></dt>
											<dd><input type="text" name="gps_group" id="gps_group" class="text ui-widget-content ui-corner-all" value="<?= $device_edit['group_name']; ?>" />
											<span class="helptip" title="Đặt tên nhóm thiết bị hoặc chọn tự động.">Đặt tên nhóm thiết bị hoặc chọn tự động.</span></dd>
										</dl>
										<dl style="width: 440px;">
											<dt style="height: 70px;"><label for="password">Avatar</label></dt>
											<? 
												$avatar_array = array('icon_car.png', 'icon_plane2.png', 'icon_locker.png', 'icon_train.png', 'icon_mobile.png', 'icon_person.png', 'icon_bikini.png', 'icon_plane.png', 'icon_gift.png', 'icon_cart.png', 'icon_truck.png', 'icon_boat.png');
												$device_avt = ($device_edit['avatar'])? $device_edit['avatar'] : 'icon_car.png'; 
											?>
											<? foreach ($avatar_array as $avt) : ?>
											<dd>
												<input name="avatar" id="<?= $avt ?>" type="radio" value="<?= $avt ?>" <?= ($device_avt == $avt)? 'checked': '';?> />
                      							<label for="<?= $avt; ?>"><img src="/assets/images/icons/<?= $avt; ?>"></img></label>
                      						</dd>
                      						<? endforeach; ?>
										</dl>
										<dl>
											<dt>&nbsp;</dt>
											<dd><button id="edit_submit" class="button_clss">LƯU</button> <? /*<button id="search_submit" class="button_clss">BỎ</button> */?></dd>
										</dl>
										</fieldset>
										</form>
										</div>
									</div>
									<div id="devices-container" class="ui-widget" style="">
									<table id="devices-table" class="ui-widget ui-widget-content">
										<thead>
											<tr class="ui-widget-header ">
												<th style="width:20px">STT</th>
												<th>Thiết bị GPS</th>
												<th>Mã thiết bị GPS</th>
												<th>Số xe</th>
												<th>Tên nhóm</th>
												<? /*<th style="width: 50px">Hiển thị</th> <!-- img -->
												<th style="width: 55px">Mặc định</th> <!-- img -->*/?>
												<th style="width: 20px">Avatar</th>
												<th style="width: 60px">Thao tác</th>
											</tr>
										</thead>
										<tbody>
											<? $i=1;foreach ($device_groups as $group_name=>$group) : ?>
											<? foreach ($group as $device) : ?>
											<tr>
												<td><?= $i; ?></td>
												<td><?= $device['product_name']; ?></td>
												<td><?= $device['imei']; ?></td>
												<td><?= $device['number_plate']; ?></td>
												<td><?= $group_name; ?></td>
												<? $icon = ($device['avatar'])? '/assets/images/icons/'.$device['avatar'] : '/assets/images/icons/icon_car.png' ; ?>
												<td><img src="<?= $icon;?>" width="20" height="20"></td>
												<td><a href="#" id="" onclick="javascript:edit_device(<?= $device['id']; ?>);"><img width="16" height="16" src='/assets/images/icons/edit_icon.png' alt="Sửa thông tin" title="Sửa thông tin"/></a> <? if (false): ?><a href="#" id="" onclick="javascript:delete_device(<?= $device['id']; ?>);"><? endif; ?><img width="16" height="16" src='/assets/images/icons/delete_icon.png' alt="Xóa" title="Xóa"/><? if (false): ?></a><? endif; ?></td>
											</tr>
											<? $i++;endforeach; ?>
											<? endforeach; ?>
											<? /* 
											<? for ($i=1; $i<=100; $i++) :?>
											<tr>
												<td><?= $i; ?></td>
												<td>VT310</td>
												<td>998877665544</td>
												<td>52F1-<?= sprintf("%04d", $i); ?></td>
												<td>Nhóm 1</td>
												<td style="text-align:center;"><img src='/assets/images/slick/tick.png'/></td>
												<td style="text-align:center;"><img src='/assets/images/slick/tick.png'/></td>
												<td style="text-align:center;"><img width="16" height="16" src='/assets/images/icons/web-car-node.png'/></td>
												<td><a href="#"><img width="16" height="16" src='/assets/images/icons/edit_icon.png'/></a> <a href="#"><img width="16" height="16" src='/assets/images/icons/delete_icon.png'/></a></td>
											</tr>
											<? endfor; ?>
											*/ ?>
										</tbody>
									</table>
									</div>
								</div>
								<DIV id="tabs-options-2" class="add_scrollbar">
									<P>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
									Vestibulum condimentum neque a velit laoreet dapibus. 
									Etiam eleifend tempus pharetra. Aliquam vel ante mauris, eget aliquam sapien. 
									Aenean euismod vulputate quam, eget vehicula lectus placerat eu. 
									Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. 
									Curabitur et ipsum orci, at fermentum metus. Etiam volutpat metus sit amet sapien tincidunt 
									non fermentum velit aliquet. Pellentesque malesuada accumsan mi a accumsan. 
									Nam commodo lectus non tellus rhoncus in facilisis metus iaculis. 
									Proin id sapien felis, sit amet pretium dui. Suspendisse purus erat, blandit ut mollis elementum, 
									bibendum a leo. Curabitur pulvinar arcu quis orci ultricies vestibulum. 
									Cras convallis nisi eget tortor tristique gravida. Nam augue magna, dapibus in luctus ac, 
									tincidunt dapibus tellus. Donec massa metus, pretium sit amet pulvinar id, ultrices ac eros. 
									Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. 
									Maecenas placerat lacus nec tortor feugiat condimentum.</P>
					
									<P>Cras nec arcu sed nisi varius fermentum ut non nulla. Pellentesque ultricies condimentum nibh, 
									nec imperdiet felis laoreet sit amet. Aenean a molestie tortor. 
									Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. 
									Praesent enim magna, imperdiet adipiscing tempus nec, molestie id elit. Ut varius ante gravida 
									est dignissim sodales. Nulla consectetur nibh eget metus sodales vulputate. 
									Mauris lacinia risus nec ipsum sodales elementum. Nunc non tortor turpis. 
									Vestibulum a euismod ligula.</P>
					
									<P>Nam non hendrerit augue. Nunc sit amet est lectus. Morbi non nisl eget dolor rutrum ullamcorper. 
									Sed dictum commodo elit sed rutrum. Nunc eu massa nulla, at gravida dolor. Aenean at interdum nisi. 
									Integer consequat malesuada urna quis dignissim. Duis luctus porta ullamcorper. 
									Aliquam tortor nunc, porta vel vestibulum at, egestas id mi. 
									In quis arcu in felis laoreet varius a et ligula. 
									Sed in magna a orci posuere ullamcorper ultrices ut ante. Suspendisse velit enim, venenatis et 
									pharetra sed, mollis ut dui. Donec erat eros, dignissim ac ultrices ac, hendrerit a elit.</P>
					
									<P>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
									Vestibulum condimentum neque a velit laoreet dapibus. 
									Etiam eleifend tempus pharetra. Aliquam vel ante mauris, eget aliquam sapien. 
									Aenean euismod vulputate quam, eget vehicula lectus placerat eu. 
									Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. 
									Curabitur et ipsum orci, at fermentum metus. Etiam volutpat metus sit amet sapien tincidunt 
									non fermentum velit aliquet. Pellentesque malesuada accumsan mi a accumsan. 
									Nam commodo lectus non tellus rhoncus in facilisis metus iaculis. 
									Proin id sapien felis, sit amet pretium dui. Suspendisse purus erat, blandit ut mollis elementum, 
									bibendum a leo. Curabitur pulvinar arcu quis orci ultricies vestibulum. 
									Cras convallis nisi eget tortor tristique gravida. Nam augue magna, dapibus in luctus ac, 
									tincidunt dapibus tellus. Donec massa metus, pretium sit amet pulvinar id, ultrices ac eros. 
									Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. 
									Maecenas placerat lacus nec tortor feugiat condimentum.</P>
					
									<P>Cras nec arcu sed nisi varius fermentum ut non nulla. Pellentesque ultricies condimentum nibh, 
									nec imperdiet felis laoreet sit amet. Aenean a molestie tortor. 
									Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. 
									Praesent enim magna, imperdiet adipiscing tempus nec, molestie id elit. Ut varius ante gravida 
									est dignissim sodales. Nulla consectetur nibh eget metus sodales vulputate. 
									Mauris lacinia risus nec ipsum sodales elementum. Nunc non tortor turpis. 
									Vestibulum a euismod ligula.</P>
								</DIV>
								<DIV id="tabs-options-3" class="add_scrollbar">
									<P>Nam non hendrerit augue. Nunc sit amet est lectus. Morbi non nisl eget dolor rutrum ullamcorper. 
									Sed dictum commodo elit sed rutrum. Nunc eu massa nulla, at gravida dolor. Aenean at interdum nisi. 
									Integer consequat malesuada urna quis dignissim. Duis luctus porta ullamcorper. 
									Aliquam tortor nunc, porta vel vestibulum at, egestas id mi. 
									In quis arcu in felis laoreet varius a et ligula. 
									Sed in magna a orci posuere ullamcorper ultrices ut ante. Suspendisse velit enim, venenatis et 
									pharetra sed, mollis ut dui. Donec erat eros, dignissim ac ultrices ac, hendrerit a elit.</P>
								</DIV>
							</div>
							<div id="tabs-options-footer" class="ui-widget-header summary-footer" style="border: 0;">&nbsp;</div>
						</div>
						<?/*
						<div class="middle-south"><!-- Inner-South Layout Container -->
							Inner-South
						</div>
						*/?>
					</div> <!-- /container-content -->
					<div class="pane ui-layout-west">
						<? $this->load->view('gps/partials/devices-left'); ?>
					</div> <!-- /ui-layout-west -->
				</div>
        <hr class="noscreen" />
        </div> <!-- /content -->
    </div> <!-- /cols -->