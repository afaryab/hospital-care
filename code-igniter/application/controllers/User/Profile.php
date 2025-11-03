<?php

class Profile extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index($userId = 0){

        if($this->isLoggedIn()) {

            $userId = $userId == 0 ? FALSE : $userId;

            $user = $this->getUser($userId);

            if ($this->havePost()) {

                // Allowed Feilds to be edited
                $feildsToBeEdit = [
                    'name',
                    'phone',
                    'city',
                    'state',
                    'country',
                    'short_story'
                ];

                //DB ARRAY
                $userUpdateArray = [];

                //CHECK VALIDATE AND POST DATA
                foreach ($feildsToBeEdit as $key => $val) {
                    if (
                        array_key_exists($val, $_POST) &&
                        $_POST[$val] != ''
                    ) {
                        $userUpdateArray[$val] = $_POST[$val];
                    }
                }

                //Start Updating Using Info

                $user = $this->getUser();

                $this->load->model('commonModel', 'myModel');

                $this->myModel->setTableName('aauth_users');

                if ($this->myModel->updateRecord(
                    $user->id,
                    $userUpdateArray
                )) {
                    $this->activityLog($this->getUser()->name.' has updated his profile.');
                    $this->session->set_flashdata('success', 'Account Updated Successfully');
                } else {
                    $this->session->set_flashdata('error', 'Unknown Error Occurred');
                }
            }
            $user = $this->getUser($userId);
            $this->_pageData['title'] = $user->name . ' Profile';
            $this->_pageData['user'] = (array) $user;
            $this->_pageData['module'] = 'user';
            $html = $this->load->makeViewWithOutTemplate('profile', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function changePicture(){
        if($this->isLoggedIn()) {

            $user = $this->getUser();

            $uploaddir = __DIR__ . '/../../../web/usr/' . $user->id . '/';

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir, 0777, true);
            }

            $identifier = generateRandomString(10);

            $uploadfile = $uploaddir . $identifier .basename($_FILES['file']['name']);

            $pathToDB = 'web/usr/' . $user->id . '/' . $identifier .basename($_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {

                $this->load->model('commonModel','myModel');

                $this->myModel->setTableName('images');

                $imageID = $this->myModel->addNew(
                    [
                        'path' => $pathToDB
                    ]
                );

                $this->myModel->setTableName('aauth_users');

                $this->myModel->updateRecord(
                    $user->id,
                    [
                        'profile_img_path' => $pathToDB,
                        'profile_img_id' => $imageID
                    ]
                );
                $this->activityLog($this->getUser()->name.' has updated his profile.');
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(
                            array(
                                'success' => '1',
                                'message' => 'Profile picture changed!',
                                'action_code' => '200',
                                'data' => (object) [
                                    'path' => base_url($pathToDB),
                                    'id' => 0
                                ]
                            )
                        )
                    );
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(
                        json_encode(
                            array(
                                'success' => '0',
                                'message' => 'Unknown error occurred!',
                                'action_code' => '505',
                                'data' => (object) []
                            )
                        )
                    );
            }

        }else{
            $this->output
                ->set_content_type('application/json')
                ->set_output(
                    json_encode(
                        array(
                            'success' => '0',
                            'message' => 'Your Session is expired please login again',
                            'action_code' => '401',
                            'data' => (object) []
                        )
                    )
                );
        }
    }
    
}

