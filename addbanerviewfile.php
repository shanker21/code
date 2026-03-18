<?php $this->extend('templateadmin');?>
<?php $this->section('content');?>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/tinymce.dev.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/plugins/table/plugin.dev.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/plugins/paste/plugin.dev.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/tinymce/plugins/spellchecker/plugin.dev.js"></script>
<script>
tinymce.init({
	selector: "textarea#vision,textarea#mission,textarea#program_educational_objectives,textarea#program_specific_outcomes,textarea#program_outcomes",
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
    right: 59% !important;
    top: 5% !important;
	float: left;
}
.pl-0{
    padding-left: 0px;
}
.row.mtb-10{
	margin-top: 15px;
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
                    <a class="active-trail active" href="<?php echo base_url();?>/Admin/Banners">Banners</a>
                </li>
                <li>
                    <a class="active-trail active" href="#">Manage Banners</a>
                </li>
            </ul>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Manage Banners
					<span class="pull-right">
						<a class="btn btn-primary btn-xs" href="<?php echo base_url();?>/Admin/Banners">Banners List</a>
					</span>
				</header>
				<div class="panel-body">
					<form id="content-form" action="" method="post">
                        <?php 
                        $banner_id = 0;$image = '';$btnval = 'Submit';$title =''; $description =''; $button_text = ''; $button_url = '';$service_id = 0;
                        if(!empty($result))
                        {
                            $banner_id = $result[0]['banner_id'];
                            $image = $result[0]['image'];
                            $btnval = 'Update';
							$title = $result[0]['title'];
							$description = $result[0]['description'];
							$button_text = $result[0]['button_text'];
							$button_url = $result[0]['button_url'];
							$service_id = $result[0]['service_id'];
                        }
                        ?>
                        <input type="hidden" id="banner_id" name="banner_id" value="<?php echo $banner_id;?>" />
                        <input type="hidden" id="aval" name="aval" value="0" />
                       <div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Banner Image (1024 x 380)</label>
									<input type="file" name="image" class="form-control" accept="image/*">
							
									<?php if ($image != '') { ?>
										<br>
										<img src="<?php echo base_url().'/public/assets/img/banner/'.$image; ?>" 
											style="max-width:300px; display:block;">
										<br>
										<a href="javascript:void(0);" class="btn btn-danger btn-sm delete_img_btn">
											Delete Image
										</a>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="row mtb-10">
							<div class="form-group">
								<div class="col-sm-12">
									<label>Title</label>
									<div class="form-line">
										<input type="text" class="form-control" placeholder="Enter Banner Title" id="title" name="title" value="<?php echo $title;?>"  onkeyup="clearError(this.id); validateInput(this);" />
									</div>
									<span class="error" id="title_error"></span>
								</div>
							</div>
						</div>
                        <div class="row mtb-10">
							<div class="form-group">
								<div class="col-sm-12">
									<label>Description</label>
									<div class="form-line">
										<textarea class="form-control" placeholder="Enter Banner Description" id="description" name="description" value="<?php echo $description;?>" onkeyup="clearError(this.id);"><?php echo strip_tags($description);?></textarea>
									</div>
									<span class="error" id="description_error"></span>
								</div>
							</div>
						</div>
						<div class="row mtb-10">
							<div class="form-group">
								<div class="col-sm-3">
									<label>Button Text</label>
									<div class="form-line">
										<input type="text" class="form-control" placeholder="Enter Button Name" id="button_text" name="button_text" value="<?php echo $button_text;?>" onkeyup="clearError(this.id);" />
									</div>
									<span class="error" id="button_text_error"></span>
								</div>
								<div class="col-sm-9">
									<label>Button URL</label>
									<div class="form-line">
										<input type="text" class="form-control" placeholder="Enter Button Url" id="button_url" name="button_url" value="<?php echo $button_url;?>" onkeyup="clearError(this.id);" />
									</div>
									<span class="error" id="button_url_error"></span>
								</div>
							</div>
						</div>
						<div class="col-md-4">
								<div class="form-group">
                                    <label>service name</label>
                                    <select name="service_id" id="service_id" class="form-control">
                                        <option value="">-- Select Category --</option>
                                        <?php foreach ($servicelist as $service): ?>
                                            <option value="<?= $service['service_id']; ?>"
    											<?= ($service['service_id'] == $service_id) ? 'selected' : ''; ?>>
                                                <?= $service['title']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
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
<input type="hidden" id="main_category_id" name="main_category_id" value="<?php echo $id;?>" />
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
		var banner_id = $("#banner_id").val();
		var error_count = 0;
		if(error_count != 0)
		{
			return false;
		}
		$("#preloader").show();
		$.ajax
		({
			url: base_url+"/Admin/Addbanners/submitDetails",
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
					if(banner_id != 0)
					{
						alert('Banner Updated Successfully.');
					}
					if(banner_id == 0)
					{
						alert('Banner Created Successfully.');
					}
					window.location.href = base_url+'/Admin/Banners';
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
	var department = $("#department").val().trim().toLowerCase();
	var hid_val = $("#hid_val").val().toLowerCase();
	if(department == '')
	{
		return false;
	}
	if(department != hid_val)
	{
		//alert("Hi");
		$.ajax
		({
			type: "POST",
	 		url: base_url+"/Admin/Addbanners/checkIsExist",
	 		data: {department:department},
			success: function(retval)
	 		{ 
				// alert(retval);
	 			if(retval == 1)
	 			{
					$("#department_error").html('Banner Already Exist.');
	 				$("#aval").val(retval);return false;
	 			}
	 			else
	 			{
					$("#department_error").html('');
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
<?php $this->endSection();?>
