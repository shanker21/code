<?php $this->extend('templateadmin');?>
<?php $this->section('content');?>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/tinymce.dev.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/plugins/table/plugin.dev.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/plugins/paste/plugin.dev.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/plugins/spellchecker/plugin.dev.js"></script>
<script>
tinymce.init({
	selector: "textarea#description",
	theme: "modern",
	plugins: [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality emoticons template paste textcolor importcss colorpicker textpattern codesample"
	],
	setup: function (editor) {
		editor.on('keyup', function (e) {  
			// clearError('description'); 
		});
	},
	// content_css: "css/development.css",
	add_unload_trigger: false,

	toolbar: "insertfile undo redo | bold italic | underline | fontsizeselect | fontselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons table codesample",

	image_advtab: true,
	image_caption: true,

	
	spellchecker_callback: function(method, data, success) {
		if (method == "spellcheck") {
			var words = data.match(this.getWordCharPattern());
			var suggestions = {};

			for (var i = 0; i < words.length; i++) {
				suggestions[words[i]] = ["First", "second"];
			}

			success({words: suggestions, dictionary: true});
		}
		
		if (method == "addToDictionary") {
			success();
		}
	}
});
</script>
<style>
.no-pd-left{
    padding-left: unset;
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/public/assets/imagecroping/css/jquery.Jcrop.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/public/assets/imagecroping/css/jcrop.css"/>
<style>
.delete_img {
    right: 29% !important;
    top: 21% !important;
}
.pl-0{
    padding-left: 0px;
}
.mt-20{
    margin-top: 20px;
}
.group-img{
	height: 55px;
}
</style>
<section class="wrapper">
	<!--mini statistics start-->
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumbs-alt">
                <li>
                    <a href="<?php echo base_url();?>/Admin/Dashboard">Dashboard</a>
                </li>
                <li>
                    <a class="active-trail active" href="<?php echo base_url();?>/Admin/OurTeam">Our Team</a>
                </li>
                <li>
                    <a class="active-trail active" href="#">Manage Our Team</a>
                </li>
            </ul>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Manage Our Team
					<span class="pull-right">
						<a class="btn btn-primary btn-xs" href="<?php echo base_url();?>/Admin/OurTeam">Our Team List</a>
					</span>
				</header>
				<div class="panel-body">
					<form id="content-form" action="" method="post">
                        <?php 
						// echo '<pre>';print_r($result);echo '</pre>';
                        $team_id = 0;$role_id = 0;$title = '';$image = '';$pdf='';$mini_description = '';$description='';$desigination='';$btnval = 'Submit';
                        if(!empty($result))
                        {
                            $team_id = $result[0]['team_id'];
                            $role_id = $result[0]['role_id'];
                            $title = $result[0]['name'];
                            $image = $result[0]['image'];
							$desigination = $result[0]['desigination'];
                            $mini_description = $result[0]['mini_description'];
                            $description = $result[0]['description'];
                            $btnval = 'Update';
                        }
                        ?>
                        <input type="hidden" id="team_id" name="team_id" value="<?php echo $team_id;?>" />
                        <input type="hidden" id="hid_val" name="hid_val" value="<?php echo $title;?>" />
                        <input type="hidden" id="aval" name="aval" value="0" />
                        <div class="row">
							<div class="col-md-4">
								<div class="form-group">
                                    <label>Select Roles</label>
                                   <select name="role_id" id="role_id" class="form-control">
										<option value="">-- Select Roles --</option>

										<?php if (!empty($roles)) : ?>
											<?php foreach ($roles as $role): ?>
												<option value="<?= $role['role_id'];?>"
													<?= (isset($role_id) && $role['role_id'] == $role_id) ? 'selected' : ''; ?>>
													<?= $role['role_name']; ?>
												</option>
											<?php endforeach; ?>
										<?php endif; ?>

									</select>
                                </div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="title">Name</label>
									<input type="text" class="form-control" id="title" name="title" placeholder="Enter Name" value="<?php echo $title;?>" />
									<span class="error" id="title_error"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="desigination" class="form-label">Desigination</label>
									<input type="text" name="desigination" class="form-control" id="desigination" value="<?php echo $desigination; ?>">
									<span class="error" id="desigination_error"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								 <div class="form-group">
									<label for="pdf" class="label-control">Choose pdf</label>
									<input type="file" class="form-control" id="pdf" name="pdf" onkeyup="clearError(this.id);" />
									<span class="error" id="pdf_error"></span>
								</div>
							</div>
							
							<div class="col-md-4">
								<label>Image</label>
								<!-- File Input -->
								<input type="file" name="image" id="image" 
									class="form-control" 
									accept="image/png, image/jpeg, image/jpg">

								<span class="error" id="image_error"></span>
							</div>
							<?php if ($image != '') { ?>
							<div class="col-md-4">
								<img src="<?php echo base_url();?>/public/assets/uploads/team/<?php echo $image;?>" 
									height="50">
								<br>
								<a class="delete_img">Delete</a>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="mini_description">Mini Description</label>
									<textarea class="form-control" id="mini_description" name="mini_description" placeholder="Enter About Team Member" rows="3" ><?php echo $mini_description;?></textarea>
								</div>
								<span class="error" id="mini_description_error"></span>
							</div>
                        </div>                        
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="description">Description</label>
									<textarea class="form-control" id="description" name="description" placeholder="Enter News Description" rows="8" ><?php echo $description;?></textarea>
								</div>
								<span class="error" id="description_error"></span>
							</div>
                        </div>                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-info"><?php echo $btnval;?></button>
                                </div>
                            </div>
                        </div>
                	</form>
				</div>
			</section>
		</div>
	</div>
	<!--mini statistics end-->
</section>
<input type="hidden" id="main_role_id" name="main_role_id" value="<?php echo $id;?>" />
<script src="<?php echo base_url();?>/public/assets/admin/js/iCheck/jquery.icheck.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/js/icheck-init.js"></script>
<script>
$(document).ready(function()
{
	// alert('Ready');
	$("#content-form").submit(function()
	{
		tinymce.triggerSave();
		var base_url = $("#base_url").val();
		var role_id = $("#role_id").val();
		var title = $("#title").val();
		var desigination = $("#desigination").val();
		var hid_val = $("#hid_val").val();
		var error_count = 0;
		if(title == '')
		{
			error_count += 1;
			$("#title_error").html('Enter News Title.');
		}
		if(title != '')
		{
			if(title != hid_val)
			{
				var aval = $("#aval").val();
				if(aval == 1)
				{
					error_count += 1;
					$("#title_error").html('News Title Already Exist.');
				}
			}
		}
		if(desigination == '')
		{
			error_count += 1;
			$("#desigination_error").html('Enter Desigination.');
		}
		if(error_count != 0)
		{
			return false;
		}
		$("#preloader").show();
		$.ajax
		({
			url: base_url+"/Admin/Addourteam/submitDetails",
			type: "POST",             
			data: new FormData(this),
			contentType: false,       
			cache: false,             
			processData:false,  
			success: function(retval)
			{
				// alert(retval);$("#preloader").hide();return false;
				if(retval != 0)
				{
					if(role_id != 0)
					{
						alert('Team Updated Successfully.');
					}
					if(role_id == 0)
					{
						alert('Team Created Successfully.');
					}
					window.location.href = base_url+'/Admin/ourteam';
				}
				else
				{
					alert('Some thing went wrong, please try again.');
					$("#preloader").hide();
				}
			}
		});
		return false;
	});
});
function clearError(id)
{
	$("#"+id+"_error").html('');
}
function checkIsExist()
{
	var base_url = $("#base_url").val();
	var title = $("#title").val().trim().toLowerCase();
	var hid_val = $("#hid_val").val().toLowerCase();
	if(title == '')
	{
		return false;
	}
	if(title != hid_val)
	{
		//alert("Hi");
		$.ajax
		({
			type: "POST",
	 		url: base_url+"/Admin/Addtitle/checkIsExist",
	 		data: {title:title},
			success: function(retval)
	 		{ 
				// alert(retval);
	 			if(retval == 1)
	 			{
					$("#title_error").html('Department Already Exist.');
	 				$("#aval").val(retval);return false;
	 			}
	 			else
	 			{
					$("#title_error").html('');
	 				$("#aval").val(retval);return false;
	 			}
	 		}
		});
	}
	else
	{
		//stop_loading();
		$("#aval").val(0);
	}
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script type="text/javascript">
$("#datetime").datetimepicker({
    dateFormat: "yy-mm-dd",
    timeFormat: "HH:mm:ss",
    changeMonth: true,
    changeYear: true,
    yearRange: "2020:2035"
});
</script>
<?php $this->endSection();?>
