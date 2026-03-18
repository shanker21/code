<?php namespace App\Controllers\Admin;
use App\Models\OurteamModel;
use App\Libraries\Myfunctions;
use App\Models\RoleModel;

class Addourteam extends BaseController
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
		$data['title'] = "IIQA || Add News";
		$data['page'] = "Addourteam";
		$roleModel = new RoleModel();
		$data['roles'] = $roleModel->where('status', 1)->findAll();
        $data['result'] = Myfunctions::getOurteamInfo($id);
		return view("Admin/addourteam", $data);
    }
	function submitDetails()
	{
		// echo '<pre>';print_r($_POST);print_r($_FILES);echo '</pre>';exit;
		helper(['form','filesystem']);
		$session = session();
		$created_by = $session->get('seslms_mid');
		$model = new OurteamModel();
		$title = $this->request->getVar('title');
		$description = $this->request->getVar('description');
		$role_id = $this->request->getVar('role_id');
		$mini_description = $this->request->getVar('mini_description');
		$desigination = $this->request->getVar('desigination');
		$slug = Myfunctions::RemoveSpecialChar($title);
		$created_on = time();
		if($this->request->getVar('team_id') == 0)
		{
			$newData = [
				'name' => $title,
				'slug' => $slug,
				'role_id' => $role_id,
				'desigination' => $desigination,
				'mini_description' => $mini_description,
				'description' => $description,
				'created_by' => $created_by,
				'created_on' => $created_on,
				'status' => 1,
			];
			$model->save($newData);
			$team_id = $model->getInsertID();
			if($team_id != 0)
			{
				$imageFile = $this->request->getFile('image');

				if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) 
				{
					$extension = $imageFile->getExtension();
					$imageName = $slug . '-' . time() . '-' . $team_id . '.' . $extension;
					$imageFile->move(ROOTPATH . 'public/assets/uploads/team/', $imageName);
					$model->set('image', $imageName)
						->where('team_id', $team_id)
						->update();
				}
				if (isset($_FILES['pdf']['name']) && $_FILES['pdf']['name'] != '') 
				{
					$pdfFile = $this->request->getFile('pdf');
					$pdfName = $_FILES['pdf']['name'];
					$pdfNameArr = explode('.', $pdfName);
					$pdfExt = end($pdfNameArr);
					// Correct file name format
					$pdffile_path = $team_id . "-pdf-" . $created_on . "." . $pdfExt;
					// Folder
					$uploadPath = "public/assets/uploads/team/pdf";
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath, 0777, true);
					}
					// Move uploaded file
					move_uploaded_file($_FILES["pdffile"]["tmp_name"], $uploadPath . $pdffile_path);
					$model->set('pdf', $uploadPath)
							->where('team_id', $team_id)
							->update();
				}
			}
			echo $team_id;
		}
		else
		{
			$team_id = $this->request->getVar('team_id');
			$newData = array(
				'name' => $title,
				'slug' => $slug,
				'role_id' => $role_id,
				'pdf' => $pdf,
				'desigination' => $desigination,
				'mini_description' => $mini_description,
				'description' => $description,
				'created_by' => $created_by,
				'created_on' => $created_on,
			);
			// echo '<pre>';print_r($newData);echo '</pre>';exit;
			$updateStatus = $model->set($newData)->where('team_id', $team_id)->update();
			// echo $updateStatus;exit;
			// echo '<pre>';print_r($_FILES['image']);echo '</pre>';exit;
			if($updateStatus != 0)
			{
				$imageFile = $this->request->getFile('image');

				if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {

					$extension = $imageFile->getExtension();
					$imageName = $slug . '-' . time() . '-' . $team_id . '.' . $extension;

					$imageFile->move(ROOTPATH . 'public/assets/uploads/team/', $imageName);

					$model->set('image', $imageName)
						->where('team_id', $team_id)
						->update();
				}
				if (isset($_FILES['pdf']['name']) && $_FILES['pdf']['name'] != '') 
				{
					$pdfFile = $this->request->getFile('pdf');
					$pdfName = $_FILES['pdf']['name'];
					$pdfNameArr = explode('.', $pdfName);
					$pdfExt = end($pdfNameArr);
					// Correct file name format
					$pdffile_path = $team_id . "-pdf-" . $created_on . "." . $pdfExt;
					// Folder
					$uploadPath = "public/assets/uploads/team/pdf";
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath, 0777, true);
					}
					// Move uploaded file
					move_uploaded_file($_FILES["pdffile"]["tmp_name"], $uploadPath . $pdffile_path);
					$model->set('pdf', $uploadPath)
							->where('team_id', $team_id)
							->update();
				}
			}
			echo $updateStatus;
		}
    }
    function checkIsExist()
	{
		$model = new NewsModel();
		$title = $this->request->getVar('title');
		$result = $model->select('*')->where('name', $title)->findAll();
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
