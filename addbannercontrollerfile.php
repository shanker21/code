<?php namespace App\Controllers\Admin;
use App\Models\BannerModel;
use App\Libraries\Myfunctions;

class Addbanners extends BaseController
{
	public function index($id='')
    {
		return $this->manage($id);
	}
	public function manage($id='')
    {
		$session = session();
		if(! $session->has('seslms_user_id'))
		{
			return redirect()->to(base_url().'/Admin/Index');
		}
		$data = [];
		helper(['form','filesystem']);
		$data['title'] = "IIQA || Add Banners";
		$data['page'] = "Addbanners";
        $data['result'] = Myfunctions::getBannerInfo($id);
		$data['servicelist'] = Myfunctions::getServicesList();
		return view("Admin/addbanners", $data);
    }
	public function directfile($id='')
    {
		$session = session();
		if(! $session->has('seslms_user_id'))
		{
			return redirect()->to(base_url().'/Admin/Index');
		}
		$data = [];
		helper(['form','filesystem']);
		$data['title'] = "IIQA || Add Banners";
		$data['page'] = "Addbanners";
        $data['result'] = Myfunctions::getBannerInfo($id);
		$data['servicelist'] = Myfunctions::getServicesList();
		return view("Admin/addbannerfiles", $data);
    }
	function submitDetails()
	{
		// echo '<pre>';print_r($_POST);print_r($_FILES);echo '</pre>';exit;
		helper(['form','filesystem']);
		$session = session();
		$created_by = $session->get('seslms_mid');
		$model = new BannerModel();
		$created_on = time();
		if($this->request->getVar('banner_id') == 0)
		{
            $image_title = 'banner-'.time(); // image title with out extension.
            $image_data = trim ( $this->request->getVar('item_pic') );
			$title = $this->request->getVar('title');
			$description = $this->request->getVar('description');
			$button_text = $this->request->getVar('button_text');
			$button_url = $this->request->getVar('button_url');
			$serviceid = $this->request->getVar('service_id');
            $tmp = explode ( ',', $image_data );
            $img_info = explode ( ";", $tmp [0] );
            $extension_arr = explode ( "/", $img_info [0] );
            if (isset ( $extension_arr [1] ) && $extension_arr [1] != "")
            {
                $extension = $extension_arr [1];
            }
            else
            {
                $extension = "jpg";
            }
            $image = $image_title . "." . $extension;
            $image_content = $tmp [1];
            $imageContent = base64_decode($image_content);
            write_file(ROOTPATH.'public/assets/img/banner/'.$image,$imageContent);
			$newData = [
				'image' => $image,
				'title' => $title,
				'description' => $description,
				'button_text' => $button_text,
				'button_url' => $button_url,
				'service_id' => $serviceid,
				'created_by' => $created_by,
				'created_on' => $created_on,
				'status' => 1,
			];
			$model->save($newData);
			$banner_id = $model->getInsertID();
			echo $banner_id;
		}
		else
		{
			$banner_id = $this->request->getVar('banner_id');
			$image_title = 'banner-'.time(); // image title with out extension.
            $image_data = trim ( $this->request->getVar('item_pic') );
			
			$title = $this->request->getVar('title');
			$description = $this->request->getVar('description');
			$button_text = $this->request->getVar('button_text');
			$button_url = $this->request->getVar('button_url');
			$serviceid = $this->request->getVar('service_id');
            $tmp = explode ( ',', $image_data );
            $img_info = explode ( ";", $tmp [0] );
            $extension_arr = explode ( "/", $img_info [0] );
            if (isset ( $extension_arr [1] ) && $extension_arr [1] != "")
            {
                $extension = $extension_arr [1];
            }
            else
            {
                $extension = "jpg";
            }
            $image = $image_title . "." . $extension;
            $image_content = $tmp [1];
            $imageContent = base64_decode($image_content);
            write_file(ROOTPATH.'public/assets/img/banner/'.$image,$imageContent);
			$newData = [
				'image' => $image,
				'title' => $title,
				'description' => $description,
				'button_text' => $button_text,
				'button_url' => $button_url,
				'service_id' => $serviceid,
				'updated_by' => $created_by,
				'updated_on' => $created_on,
				'status' => 1,
			];
			// echo '<pre>';print_r($newData);echo '</pre>';exit;
			$updateStatus = $model->set($newData)->where('banner_id', $banner_id)->update();
			echo $updateStatus;
		}
    }
	function submitDirectFileDetails()
	{
		// echo '<pre>';print_r($_POST);print_r($_FILES);echo '</pre>';exit;
		helper(['form','filesystem']);
		$session = session();
		$created_by = $session->get('seslms_mid');
		$model = new BannerModel();
		$created_on = time();
		if($this->request->getVar('banner_id') == 0)
		{
			$filename = $_FILES['banner']['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$image = 'benner-'.time().".".$ext;
			move_uploaded_file($_FILES["banner"]["tmp_name"],"public/assets/uploads/banners/" . $image);
			$newData = [
				'image' => $image,
				'created_by' => $created_by,
				'created_on' => $created_on,
				'status' => 1,
			];
			$model->save($newData);
			$banner_id = $model->getInsertID();
			echo $banner_id;
		}
		else
		{
			$banner_id = $this->request->getVar('banner_id');
			$filename = $_FILES['banner']['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$image = 'benner-'.time().".".$ext;
			move_uploaded_file($_FILES["banner"]["tmp_name"],"public/assets/uploads/banners/" . $image);
			$newData = [
				'image' => $image,
				'updated_by' => $created_by,
				'updated_on' => $created_on,
				'status' => 1,
			];
			// echo '<pre>';print_r($newData);echo '</pre>';exit;
			$updateStatus = $model->set($newData)->where('banner_id', $banner_id)->update();
			echo $updateStatus;
		}
    }

    function checkIsExist()
	{
		$model = new BannerModel();
		$department = $this->request->getVar('department');
		$result = $model->select('*')->where('department', $department)->findAll();
		if(!empty($result))
		{
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
}
