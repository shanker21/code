/**
* adding the code for reorder and form for adding images, and also video code
*/
<?php $this->extend('templateadmin');?>
<?php $this->section('content');?>
<style>
.no-pd-left{
    padding-left: unset;
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
                    <a class="active-trail active" href="<?php echo base_url();?>/Admin/Gallerycategories">Gallery Categories</a>
                </li>
                <li>
                    <a class="active-trail active" href="#">Manage Categories</a>
                </li>
            </ul>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Manage Media Gallery
					<span class="pull-right">
						<a class="btn btn-primary btn-xs" href="<?php echo base_url();?>/Admin/MediaGallery">gallery Categories List</a>
					</span>
				</header>
				<div class="panel-body">
					<form id="content-form" method="post">
                        <?php 
                        $gallery_id = 0;$gallerycategory = '';$btnval = 'Submit';
                        if(!empty($result))
                        {
                            $gallery_id = $result['gallery_id'];
                            $btnval = 'Update';
                        }
                        ?>
                        <input type="hidden" id="gallery_id" name="gallery_id" value="<?php echo $gallery_id;?>" />
                        <input type="hidden" id="hid_val" name="hid_val" value="<?php echo $gallerycategory;?>" />
                        <input type="hidden" id="aval" name="aval" value="0" />
                        <input type="hidden" name="id" value="<?= $result[0]['id'] ?? '' ?>">
                        <input type="hidden" name="old_image" value="<?= $result[0]['image_path'] ?? '' ?>">
                        <div class="row">
                            <div class="col-md-6"> 
                                <div class="form-group mt-3">
                                    <label>Upload Images</label>
                                    <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
                                </div>
                            </div>
                           <div class="col-md-6"> 
              								<div class="form-group">
              									<label for="video_code">YouTube Video Code</label>
              									<input type="text" id="video_code" name="video_code" class="form-control" value="<?php echo $video_code;?>"/>
              									<span class="error" id="video_code_error"></span>
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
    <div class="row">
        <div class="col-md-12">
            <section class="panel">
                <header class="panel-heading">
                    Manage Category Gallery Order
                </header>
                <div class="panel-body">
                    <?php 
                    // echo '<pre>';print_r($gallery);echo '</pre>';exit;
                    if(!empty($gallery))
                    {
                        ?>
                        <div class="form-group" style="display:none;">
                            <div class="col-lg-12">
                                <?php for($i=0;$i<count($gallery);$i++){?>
                                <div class="col-lg-3" style="padding-bottom: 30px;" id="banner-image-<?php echo $gallery[$i]['image_id'];?>">
                                    <img src="<?= base_url('public/assets/uploads/gallery/'.$gallery[$i]['image_path']); ?>" />
                                </div>
                                <?php }?>
                            </div>
                        </div> 
                        <div class="col-lg-12">
                            <a href="javascript:void(0);" class="btn outlined mleft_no reorder_link" id="save_reorder">reorder photos</a>
                        </div>
                        <div class="col-lg-12">
                            <div id="reorder-helper" class="light_box" style="display:none;">1. Drag photos to reorder.<br>2. Click 'Save Reordering' when finished.</div>
                            <div class="gallery">
                                <ul class="reorder_ul reorder-photos-list">
                                <?php 
                                //Fetch all images from database
                                // $gallery = $db->getRows();
                                if(!empty($gallery))
                                {
                                    for($i=0;$i<count($gallery);$i++)
                                    {
                                        ?>
                                        <li id="image_li_<?php echo $gallery[$i]['image_id']; ?>" class="ui-sortable-handle">
                                            <a href="javascript:void(0);" style="float:none;" class="image_link">
                                                <img src="<?= base_url('public/assets/uploads/gallery/'.$gallery[$i]['image_path']); ?>" />
                                                <a class="delete-img" id="<?php echo $gallery[$i]['image_id']; ?>" onclick="deleteImage(this.id);">X</a>
                                            </a>
                                        </li>
                                        <?php 
                                    } 
                                } 
                                ?>
                                </ul>
                            </div>
                        </div>
                        <?php 
                    }
                    else
                    {
                        ?>
                        <p class="text-center">Gallery Images Not Uploaded</p>
                        <?php 
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>
</section>
<input type="hidden" id="main_gallery_id" name="main_gallery_id" value="<?php echo $id;?>" />
<script src="<?php echo base_url();?>/public/assets/admin/js/iCheck/jquery.icheck.js"></script>
<script src="<?php echo base_url();?>/public/assets/admin/js/icheck-init.js"></script>
<script>
$(document).ready(function()
{
	// alert('Ready');
	$("#content-form").submit(function()
	{
		var base_url = $("#base_url").val();
		var category_id = $("#category_id").val();
		var hid_val = $("#hid_val").val();
        var images = $("#images")[0].files;
		var error_count = 0;
		$("#preloader").show();
		$.ajax
		({
			url: base_url+"/Admin/AddMediaGallery/submitgallery",
			type: "POST",             
			data: new FormData(this),
			contentType: false,       
			cache: false,             
			processData:false,  
			success: function(retval)
            {
                if(retval == 1)
                {
                    alert('Images Uploaded Successfully.');
                    window.location.reload(); // reload page
                }
                else
                {
                    alert('Something went wrong.');
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
</script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.reorder_link').on('click',function(){
        
		$("ul.reorder-photos-list").sortable({ tolerance: 'pointer' });
		$('.reorder_link').html('save reordering');
		$('.reorder_link').attr("id","save_reorder");
		$('#reorder-helper').slideDown('slow');
		$('.image_link').attr("href","javascript:void(0);");
		$('.image_link').css("cursor","move");
		$("#save_reorder").click(function( e ){
            debugger;
			if( !$("#save_reorder i").length ){
				$("ul.reorder-photos-list").sortable('destroy');
				$("#reorder-helper").html( "Reordering Photos - This could take a moment. Please don't navigate away from this page." ).removeClass('light_box').addClass('notice notice_error');
	
				var h = [];
				$("ul.reorder-photos-list li").each(function() {  h.push($(this).attr('id').substr(9));  });
				var base_url = $("#base_url").val();
				// alert(base_url);//return false;
				// $("#preloader").show();
				$.ajax({
					type: "POST",
					url: base_url+"/Admin/AddMediaGallery/submitOrderDetails",
					data: {ids: " " + h + ""},
					success: function(retval){
						// alert(retval);return false;
						window.location.reload();
					}
				});	
				return false;
			}	
			e.preventDefault();		
		});
	});
});
function deleteImage(image_id)
{
	var base_url = $("#base_url").val();
	$.ajax({
		type: "POST",
		url: base_url+"/Admin/AddMediaGallery/deleteGalleryImageRecord",
		data: {image_id:image_id},
		success: function(retval){
			// alert(retval);return false;
			window.location.reload();
		}
	});	
}
</script>
<style>
    .ui-sortable-handle a{
	float: none;
    cursor: move;
    text-decoration: none;
    color: #000;
    font-weight: 900;
}
.gallery {
    width: 100%;
    float: left;
    margin-top: 0px;
}
.gallery img {
    max-width: 155px;
    z-index: 100;
    height: 120px;
}
.delete-img {
    background: none repeat scroll 0 0 #fff;
    border: 1px solid #aaa;
    opacity: 0.8;
    padding: 0 5px;
    position: absolute;
    z-index: 5;
    right: 1% !important;
    top: 1% !important;
    background: red;
	cursor: pointer !important;
}
.gallery ul {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 30px;
    list-style: none;
    padding: 0;
}
.gallery ul li {
    background: #f8f8f8;
    border: 2px solid #ccc;
    padding: 20px;
    position: relative;
    text-align: center;
}
.btn-top {
    margin-top: 24px;
}
.emp-img{
    height: 55px;
}
.pl-0{
    padding-left: 0px;
}
.card .body .col-sm-9{
    margin-bottom: unset;
}
.reorder_link:hover {
    color: #fff;
    border: solid 2px #3675B4;
    background: #3675B4;
    box-shadow: none;
}
.reorder_link {
    color: #3675B4;
    border: solid 2px #3675B4;
    border-radius: 3px;
    text-transform: uppercase;
    background: #fff;
    font-size: 18px;
    padding: 10px 20px;
    margin: 15px 15px 15px 0px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.35s;
    -moz-transition: all 0.35s;
    -webkit-transition: all 0.35s;
    -o-transition: all 0.35s;
    white-space: nowrap;
}
</style>
<?php $this->endSection();?>
