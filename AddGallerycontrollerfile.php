<?php namespace App\Controllers\Admin;
use App\Models\CategoryModel;
use App\Models\GalleryModel;
use App\Models\ImagesModel;
use App\Libraries\Myfunctions;

class AddMediaGallery extends BaseController
{
	public function index()
    {
        $session = session();
		if(! $session->has('seslms_user_id'))
		{
			return redirect()->to(base_url().'/Admin/Index');
		}
		$data = [];
		helper(['form','filesystem']);
		$data['title'] = "IIQA || Add Categories";
		$data['page'] = "Addcategories";
        $data['categories'] = Myfunctions::getgalleryCategoriesList();
        // var_dump($data['result']);
        // exit();
		return view("Admin/addmediagallery", $data);
		
	}
  /**
  * Here, the code is written to edit the value using an ID and fetch the gallery details
  */
	public function manage($id='')
    {
		$session = session();
		if(! $session->has('seslms_user_id'))
		{
			return redirect()->to(base_url().'/Admin/Index');
		}
		$data = [];
		helper(['form','filesystem']);
		$data['title'] = "IIQA || Add Categories";
		$data['page'] = "MediaGallery";
        //$data['result'] = Myfunctions::getgallerymediaInfo($id);
        $data['gallery'] = Myfunctions::getgallery($id);
        $data['result'] = Myfunctions::getgallerycategory($id);
        // var_dump($data['result']);
        // exit();
		return view("Admin/addgallery", $data);
    }

   /**
  * Here,submit the category details into gallerymodel 
  */
    public function submitDetails()
    {
        helper(['form','filesystem']);

        $session = session();
        $created_by = $session->get('seslms_mid');

        $category_id = $this->request->getPost('category_id');

        $galleryModel = new GalleryModel();
        $imageModel   = new ImagesModel();

        $galleryData = [
            'gallery_id' => $category_id,
            'created_on' => time(),
            'created_by' => $created_by,
            'status'     => 1
        ];

        $galleryModel->insert($galleryData);
        $galleryInsertId = $galleryModel->getInsertID();

        if (!$galleryInsertId) {
            echo 0;
            exit;
        }
        echo 1;
        exit;
    }
  
 /**
  * Here, submit the category details into the image model 
  */
    public function submitgallery()
    {
        helper(['form','filesystem']);
        $galleryId = $this->request->getPost('gallery_id');
        if (empty($galleryId)) {
            echo 0;
            exit;
        }
        $model = new ImagesModel();
        $imgFiles = $this->request->getFiles();
        if (!empty($imgFiles['images'])) {
            foreach ($imgFiles['images'] as $img) {
                if ($img && $img->isValid() && !$img->hasMoved()) {
                    $imageName = $img->getRandomName();
                    $img->move(FCPATH . 'public/assets/uploads/gallery', $imageName);
                    $imageData = [
                        'id' => $galleryId,   // parent gallery id
                        'image_path' => $imageName
                    ];
                    $model->insert($imageData);
                }
            }
            echo 1;
        } else {
            echo 0;
        }

        exit;
    }
  /**
  * Here, we can delete the gallery image record
  */
  function deleteGalleryImageRecord()
	{
		$session = session();
		$model = new ImagesModel();
		$mid = $session->get('sesass_mid');
		$image_id = $_POST['image_id'];
		$delete_status = $model->where('image_id', $image_id)->delete();
		echo 1;
	}

   /**
  * Here, we can submit the order details
  */
	function submitOrderDetails()
	{
		$model = new ImagesModel();
		$ids = explode(",",$_POST['ids']);
		$order = 1;
		for($i=0;$i<count($ids);$i++)
		{
			$id = $ids[$i];
			$update = array(
				'displayOrder'=>$order
			);
			$update_status = $model->set($update)->where('image_id', $id)->update();
			$order ++;	
		}
		echo 1;exit;
	}

}
