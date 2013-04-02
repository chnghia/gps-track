<form  id="signupform"  method="post" action="" >
      <div style="float:left;padding-left:50px">
     <?php echo validation_errors(); ?>
		<?php echo form_open('form_validation'); ?>
     <br />
      <div style="width:100px;height:30px;float:left;">UserName : </div><div style="width:300px"><input type="text" name="username" id="username" value="<?=$username;?>"/></div><br />
      <div style="width:100px;height:30px;float:left;">Password : </div><div style="width:300px"><input type="password" name="password" id="password" value="<?=$password;?>"/></div><br />
      <div style="width:100px;height:30px;float:left;">Confirm Password : </div><div style="width:300px"><input type="password" name="confirm_password" id="copassword" value=""/></div><br />
      <div style="width:100px;height:30px;float:left;">Email : </div><div style="width:300px"><input type="text" name="email" id="email" value="<?=$email?>"/></div><br />
       <div style="width:100px;height:30px;float:left;">Product Name : </div><div style="width:300px"><select name="product_id" >
       <?php
	  						 foreach($products as $pro)
							{
								echo "<option value=\"".$pro->id."\">".$pro->name."</option>";	
							}

	   ;?>/>
       </select></div><br />
      <div style="width:100px;height:30px;float:left;">Imei : </div><div style="width:300px"><input type="text" name="imei" class="required" minlength="8" id="imei" value="<?=$imei;?>"/></div><br />
      <div style="width:100px;height:30px;float:left;">Number Plate: </div><div style="width:300px"><input type="text" name="number_plate" class="required" minlength="8" id="number_plate" value="<?=$number_plate;?>"/></div><br /><br />
      <input type="submit" id="insert" name="register" value="register"/>
      </div>
</form>
