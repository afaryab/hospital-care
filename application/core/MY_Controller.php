<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Faryab Kokab
 * Date: 8/16/2017
 * Time: 9:54 PM
 */

class MY_Controller extends CI_Controller
{

    protected $_user = NULL;
    
    protected $_pageData = [];

    protected $_configInfo = [];

    public $clientArray = [];

    public $receptionClosingArray = [];

    public $recptionArray = [];

    protected $_modules = ['Home|<i class="icon icon-home"></i>','Reception|REC','OPD Reception |OPD-REC','Inpatient Reception |INP-REC', 'Emergency Reception | EMER-REC','OPD Doctor |OPD-DR','Inpatient Doctor |INP-DR','Accounts|AC','Appointments|APP','Reports|RP','Tablet|TAB','Marketing|MKG','Users Management | <i class="fas fa-users"></i>', 'Advance|ADV'];


    public function __construct()
    {
        parent::__construct();

        $this->initializePageData();

        $this->initializeClinet();

        if(!isset($this->aauth)){ $this->load->library('aauth'); }
        if($this->isLoggedIn()){

            $this->_user = $this->aauth->get_user();
            $this->_pageData['logininUser'] = $this->_user;
            if($this->_user->change_password == 1){
                redirect('Auth/ChangePassword');
            }
            if($this->_user->is_receptionist == 1){
                $this->inlitializeReception();
            }
            $appointments = [
                'today' => [1,2,3],
                'upcoming' => [1,2,3]
            ];
//            print_array($this->getAppointments(),1);
            $this->_pageData['my_appointments'] = $this->getAppointments();
            $this->_pageData['my_notification'] = $appointments;
            

        }else{
            $this->_user = (object) [];
        }
        $key = rand(1000,9999);
        $this->_pageData['destructionKey'] = $key;
        $this->setMessage('destructionKey',$key,TRUE);

        $this->checkforChangePAssword();

    }

    protected function getAppointments(){
        if($this->isLoggedIn()){
            if($this->_user->is_admin = 1){
                $this->load->model('commonModel','appointments');
                $this->appointments->setTableName('appointments');
                $date = [
                    'start' => date("Y-m-d h:i:s a"),
                    'end' => date("Y-m-d h:i:s a"),
                ];

                $app = $this->appointments->getAppointmentsByIdNDate(0,$date);
				
                $patientids = [];
                $patients = [];
                $finalArray = [];
                foreach ($app as $a){
                    $finalArray[$a['id']] = $a;
                    $patientids[] = $a['patient_id'];
                }
                if(!empty($patientids)) {
                    $this->appointments->setTableName('patients');
                    $p = $this->appointments->findBy(['id' => $patientids]);
                    foreach ($p as $pat) {
                        $patients[$pat['id']] = $pat;
                    }
                    foreach ($finalArray as $key => $row) {
                        $finalArray[$key]['patient'] = $patients[$row['patient_id']];
                        $finalArray[$key]['doctor'] = $this->getUser($row['doctor_id']);
                    }
                }
                $appointments['today'] = $finalArray;
                $date = [
                    'start' => date("Y-m-d h:i:s a",strtotime(date("Y-m-d") . " + 1 day")),
                    'end' => date("Y-m-d h:i:s a",strtotime(date("Y-m-d") . " + 500 day")),
                ];
                $this->appointments->setTableName('appointments');
                $app = $this->appointments->getAppointmentsByIdNDate(0,$date,[10,0]);

                $patientids = [];
                $patients = [];
                $finalArray = [];
                foreach ($app as $a){
                    $finalArray[$a['id']] = $a;
                    $patientids[] = $a['patient_id'];
                }
				
                if(!empty($patientids)) {
                    $this->appointments->setTableName('patients');
                    $p = $this->appointments->findBy(['id' => $patientids]);
                    foreach ($p as $pat) {
                        $patients[$pat['id']] = $pat;
                    }
                    foreach ($finalArray as $key => $row) {
                        $finalArray[$key]['patient'] = $patients[$row['patient_id']];
                        $finalArray[$key]['doctor'] = $this->getUser($row['doctor_id']);
                    }
                }
				
                $appointments['upcoming'] = $finalArray;
                return $appointments;
            }else{

                $this->load->model('commonModel','appointments');
                $this->appointments->setTableName('appointments');
                $date = [
                    'start' => date("Y-m-d h:i:s a"),
                    'end' => date("Y-m-d h:i:s a"),
                ];

                $app = $this->appointments->getAppointmentsByIdNDate($this->_user->id,$date);
                $patientids = [];
                $patients = [];
                $finalArray = [];
                foreach ($app as $a){
                    $finalArray[$a['id']] = $a;
                    $patientids[] = $a['patient_id'];
                }
                if(!empty($patientids)) {
                    $this->appointments->setTableName('patients');
                    $p = $this->appointments->findBy(['id' => $patientids]);
                    foreach ($p as $pat) {
                        $patients[$pat['id']] = $pat;
                    }
                    foreach ($finalArray as $key => $row) {
                        $finalArray[$key]['patient'] = $patients[$row['patient_id']];
                        $finalArray[$key]['doctor'] = $this->getUser($row['doctor_id']);
                    }
                }
                $appointments['today'] = $finalArray;
                $date = [
                    'start' => date("Y-m-d h:i:s a"),
                    'end' => date("Y-m-d h:i:s a",strtotime(date("Y-m-d") . " + 500 day")),
                ];

                $app = $this->appointments->getAppointmentsByIdNDate($this->_user->id,$date,[10,0]);

                $patientids = [];
                $patients = [];
                $finalArray = [];
                foreach ($app as $a){
                    $finalArray[$a['id']] = $a;
                    $patientids[] = $a['patient_id'];
                }
                if(!empty($patientids)) {
                    $this->appointments->setTableName('patients');
                    $p = $this->appointments->findBy(['id' => $patientids]);
                    foreach ($p as $pat) {
                        $patients[$pat['id']] = $pat;
                    }
                    foreach ($finalArray as $key => $row) {
                        $finalArray[$key]['patient'] = $patients[$row['patient_id']];
                        $finalArray[$key]['doctor'] = $this->getUser($row['doctor_id']);
                    }
                }
                $appointments['upcoming'] = $finalArray;
                return $appointments;
            }
        }
    }

    public function checkforChangePAssword(){
        $class = $this->router->fetch_class();
        if($class != 'ChangePassword') {
            $user = $this->aauth->get_user();

            if ($user->change_password == 1) {
                $this->setMessage('error', 'You Must Change your password first in order to comply Security Standards.');
                redirect('Auth/ChangePassword');
            }
        }
    }

    public function allOk(){

        if(
            defined('REFERENCE_NUMBER') &&
            defined('LICENCE_KEY') &&
            REFERENCE_NUMBER != '' &&
            LICENCE_KEY != ''
        ){
            return true;
        }else{
            return false;
        }

    }

    public function getUser($userId = 0)
    {
        if(!$this->isLoggedIn ()){
            redirect('Auth/Login');
        }
        if($userId != 0){
            return $this->aauth->get_user($userId);
        }
        if(empty($this->_user) || $this->_user == NULL){

            return $this->_user;
        }else{

            if($this->isLoggedIn()){
                $this->_user = $this->aauth->get_user();
                return $this->_user;
            }else{
                $this->_user = (object) [];
                return $this->_user;
            }
        }
    }

    function initializePageData(){

        if(!file_exists(__DIR__ . '/../../config.bin')){
            

            file_put_contents(__DIR__ . '/../../config.bin', json_encode([]));

        }

        $this->_configInfo = (array) json_decode(file_get_contents(__DIR__ . '/../../config.bin'));

        if(!file_exists(__DIR__ . '/../../licence.bin')){
            redirect('Config/LicenceKey');
        }else{
            
            $options = [];
            $licenceInfo = json_decode(file_get_contents(__DIR__ . '/../../licence.bin'));
            $configInfo = json_decode(file_get_contents(__DIR__ . '/../../config.bin'));
            
            foreach ($licenceInfo as $parent=>$row){
                if(is_object($row) || is_array($row)){
                    foreach($row as $key=>$val){

                        define($parent.'_'.$key, $val);
                        $options[$parent.'_'.$key] = $val;

                    }
                }else{
                    define($parent, $row);
                    $options[$parent] = $row;
                }

            }
            
            foreach ($configInfo as $key=>$row){

                define($key, $row->value);
                $options[$key] = $row->value;

            }

            //If Configured on Server then These Will be missed.
            $defaultImage = '/logo.png';
            $appImage = $defaultImage;
            $printImage = $defaultImage;
            $mustKeys = [
                'OPEN_ENTRY_POINT' => 0,
                'OPEN_ENTRY_POINT_LINK' => 'Tablet/LoadView',
                'OPEN_ENTRY_POINT_TITLE' => 'Tablet',
            ];
            foreach ($mustKeys as $key => $val) {

                if (!array_key_exists($key, $options)) {
                    $options[$key] = $val;
                    define($key, $val);
                }
            }

            $mustKeys = [
                'BUSINESS_LOGO_IMAGE_64' => $defaultImage,
                'APP_LOGO_IMAGE_64' => $appImage,
                'PRINT_IMAGE_64' => $printImage,
                'DATETIMEZONE' => 'Asia/Karachi'
            ];
            foreach ($mustKeys as $key => $val) {

                if (array_key_exists($key, $options)) {
                    $options[$key] = $options[$key];
                }else{
                    $options[$key] = $val;
                }
                define($key,$options[$key]);
            }

            $scanFolder = __DIR__.'/../controllers/*';
            $files = glob($scanFolder);

            $options1 = [];
            $constants = [];
            foreach($files as $file) {
                //do your work here
                if(is_dir($file)){
                    $foldername = basename ($file);
                    require_once $file.'/_'.$foldername.'.class.php';

                    $loader = new $foldername();
                    $navigation = $loader->getNavigation();

                    $array1 = array_key_exists('top_nav', $navigation) ? $navigation['top_nav'] : [];
                    $array2 = array_key_exists('navigations', $navigation) ? $navigation['navigations'] : [];

                    if(array_key_exists('urlsToRemember', $navigation)){
                        foreach ($navigation['urlsToRemember'] as $key => $urls){

                            $constants[$key] = $urls;

                        }
                    }
                    $options1 = array_merge_recursive($options1,$navigation);


                }
            }


            $this->_pageData = array_merge_recursive($options,$options1,$constants);

            $this->_pageData['success'] = $this->getMessage('success');
            $this->_pageData['error'] = $this->getMessage('error');
            $this->_pageData['warning'] = $this->getMessage('warning');

            $this->_pageData['notifications'] = $this->getIncomingNotifications();


            $this->_pageData['currentUser'] = (object) [];

            $this->_pageData['models'] = $this->isLoggedIn() ? $this->getModels() : [];

            if(!array_key_exists('business_logo',$this->_pageData)){
                $this->_pageData['business_logo'] = 'assets/logo.png';
            }
            if($this->isLoggedIn()){
                $this->_pageData['currentUser'] = $this->getUser();
            }
        

        }
    }


    function initializeClinet(){
        $this->load->helper('cookie');
        
        $clientKey = get_cookie('processtonclient_client_key');

        if($clientKey == null){
            $clientKey = generateRandomString(12);  
        }
        set_cookie(
            "processtonclient_client_key",
            $clientKey,
            time() + (10 * 365 * 24 * 60 * 60)
        );

        $this->load->model('commonModel', 'clientModel');
        $this->clientModel->setTableName('clients');
        $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);

        if(empty($this->clientArray)){
            $this->clientModel->addNew([
                'machine_name' => $_SERVER["REMOTE_ADDR"],
                'machine_unique_key' => $clientKey
            ]);
            $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);
        }
        $updateClientArray = [];
        $updateClientArray['current_user_login_id'] = null;
        if($this->isLoggedIn() && $this->clientArray['current_user_login_id'] != $this->_user->id){
            $updateClientArray['current_user_login_id'] = $this->_user->id;
        }

        if(!empty($updateClientArray)){
            $this->clientModel->updateRecord($this->clientArray['id'],$updateClientArray);
            $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);
        }

        
        
    }

    function inlitializeReception(){

        if($this->_user->is_receptionist == 1){

            $this->load->model('commonModel', 'receptionModel');
            $this->receptionModel->setTableName('reception_counters');
            $this->recptionArray = $this->receptionModel->findOneBy(['id' => $this->_user->reception_id]);

            $this->load->model('commonModel', 'receptionClosingModel');
            $this->receptionClosingModel->setTableName('reception_counters_closings');
            $this->receptionClosingArray = $this->receptionClosingModel->findOneBy(['user_id' => $this->_user->id,'reception_id' => $this->_user->reception_id,'status' => 'OPEN']);
            
            if(empty($this->receptionClosingArray)){
                
                redirect('Reception/OpenCounter');
            }else{
                $this->_pageData['counter'] = $this->receptionClosingArray;

                $this->load->model('commonModel', 'receptionClosingModelTransactions');
                $this->receptionClosingModelTransactions->setTableName('reception_counters_closings_transactions');
                $this->_pageData['counter_transactions'] = $this->receptionClosingModelTransactions->findBy([
                    'counter_id' => $this->_pageData['counter']['id'],
                    'user_id' => $this->_user->id
                ]);
                
            }

            
        }


    }

    function getModels(){

        $this->load->model('commonModel');
        $this->commonModel->setTableName('models_popups');
        $models = $this->commonModel->findBy(['status' => 1]);
        $ref = array_key_exists('HTTP_REFERER',$_SERVER) ? $_SERVER['HTTP_REFERER'] : site_url();

        $finalArray = [];
        foreach ($models as $model){

            if($model['type'] == 3){
                $finalArray[] = $model;
            }
            if(
                (
                    strtolower($ref) == strtolower(site_url($this->_pageData['LOGIN'])) ||
                    strtolower($ref) == strtolower(site_url($this->_pageData['INDEX_PATH']))
                ) &&
                $model['type'] == 2
                ){
                $finalArray[] = $model;
            }

            if($model['type'] == 1){
                $this->commonModel->setTableName('models_popups_views');
                $raw = $this->commonModel->findBy(['model_id' => $model['id'] , 'user_id' => $this->getUser()->id]);

                if(empty($raw)){
                    $finalArray[] = $model;
                    $this->commonModel->addNew(['model_id' => $model['id'] , 'user_id' => $this->getUser()->id]);
                }

            }

        }

        return $finalArray;


    }

    function makeView($html,$pageData = [], $customView = NULL){
        
        $this->_pageData = array_merge_recursive($this->_pageData,$pageData);
        $this->_pageData['html'] = $html;
        $tempNav = $this->_modules;
        $newNav = [];
        foreach ($tempNav as $k=>$g){
            if(array_key_exists($g,$this->_pageData['navigations'])){
                $sortedArray = $this->_pageData['navigations'][$g];
                usort($sortedArray, 'sortArrayByOrder');
                $newNav[$g] = $sortedArray;
            }
        }

        
        $clearedNav = [];
        foreach ($newNav as $group => $nav){
            if(count($nav) > 0){ 
                foreach ($nav as $link){
                    $perGroup = array_key_exists('perm_group', $link) ? $link['perm_group'] : 'ANONYMOUS';
                    
                    $show = FALSE;
                    if ($link['perm'] == '' || $link['perm'] == 'all' ||$this->aauth->is_allowed($link['perm'], $perGroup)) {
                        $show = TRUE;
                    }
                    if($show == TRUE){
                        $clearedNav[$group][] = $link;
                    }
                    
                }
            }
        }

        $this->_pageData['navigations'] = $clearedNav;
        if($customView == NULL) {
            $this->load->view('view', $this->_pageData);
        }else{
            $this->load->view($customView, $this->_pageData);
        }
    }
    
    function setMessage($cause,$message,$FLUSH_PREV = FALSE ){
        
        $causeArray = [];

        $return = $this->session->flashdata($cause);
        if($FLUSH_PREV == true){
            $return = '';
        }
        if($return == ''){
            $causeArray = [];
        }else{
            $causeArray = json_decode($return);
        }
        $causeArray[] = $message;

        if($cause == "error"){
            $this->_pageData['error'][] = $message;
        }
        if($cause == "success"){
            $this->_pageData['success'][] = $message;
        }
        if($cause == "warning"){
            $this->_pageData['warning'][] = $message;
        }
        
        $this->session->set_flashdata($cause, json_encode($causeArray));
        
    }
    
    function getMessage($cause){
        
        $causeArray = [];
        $return = $this->session->flashdata($cause);
        
        if($return == ''){
            $causeArray = [];
        }else{
            $causeArray = json_decode($return);
        }
        return $causeArray;
    }

    function getIncomingNotifications(){
        $this->load->model('commonModel', 'myModel');

        $this->myModel->setTableName('notifications');
        if($this->aauth->is_admin()){
            $notifications = $this->myModel->findBy(['user_id !=' => NULL],[4,0]);
            $finalArray = [];
            $userProfiles = [];
            foreach ($notifications as $key=>$notification){
                $userProfiles[$notification['user_id']] = $this->getUser($notification['user_id']);
            }
            foreach ($notifications as $notification){
                $finalArray[$notification['id']] = $notification;
                $finalArray[$notification['id']]['user'] = $userProfiles[$notification['user_id']];
            }

            return $finalArray;
        }else{
            $notifications = $this->myModel->findBy(['user_id' => $this->getUser()->id],[4,0]);

            $finalArray = [];
            $userProfiles = [];
            foreach ($notifications as $key=>$notification){
                $userProfiles[$notification['user_id']] = $this->getUser($notification['user_id']);
            }
            foreach ($notifications as $notification){
                $finalArray[$notification['id']] = $notification;
                $finalArray[$notification['id']]['user'] = $userProfiles[$notification['user_id']];
            }

            return $finalArray;
        }

    }
    
    

    function isLoggedIn(){

        $this->aauth->is_loggedin();
        $this->_user = $this->aauth->get_user();

        $notEmptyUser = (!empty($this->_user));
        $notNullUser = ($this->_user != NULL);
        $notFalseUser = ($this->_user != FALSE);

        if($notEmptyUser && $notNullUser && $notFalseUser){
            return true;

        }else{
            return false;
        }
    }

    function havePost(){

        if (!empty($_POST)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Redirecting Unauthorized User
     */
    function redirectUnauthorized(){
        redirect('Auth/Login');
    }

    function redirectAutnorized(){

        $user = $this->aauth->get_user();
        
        // if($user->is_receptionist == 1){
        //     echo "check For Reception";
        // }
        $groups = $this->aauth->get_groups();

        foreach ($groups as $group){
            if ($this->aauth->is_member($group->name)) {
                if($group->url != '') {
                    redirect($group->url);
                }
            }
        }
        redirect('Dashboard/ViewDashboard');

    }

    function activityLog($message){

        $array = [
            'message' => $message
        ];
        if($this->isLoggedIn()){
            $array['user_id'] = $this->getUser() ? $this->getUser()->id : NULL;
        }

        $this->load->model('commonModel', 'myModel');

        $this->myModel->setTableName(ACTIVITY_LOGS_TABLE);

        $this->myModel->addNew($array);

    }

    function hasAccessTo($group){
        return $this->aauth->is_member($group);
    }

    protected function getAccounts(){

        $this->load->model('commonModel');
        $this->commonModel->setTableName('accounts');
        $accountsRaw = $this->commonModel->getAll();

        $accounts = [];
        foreach ($accountsRaw as $account){
            $accounts[$account['id']] = $account;
        }
        return $accounts;
    }

    protected function getExpReasons(){

        $this->load->model('commonModel');
        $this->commonModel->setTableName('expence_reasons');
        $reasonsRaw = $this->commonModel->getAll();
        $reasons = [];
        foreach ($reasonsRaw as $reason){
            $reasons[$reason['id']] = $reason;
        }
        return $reasons;
    }

    protected function getVendors(){

        $this->load->model('commonModel');
        $this->commonModel->setTableName('vendors');
        $reasonsRaw = $this->commonModel->getAll();
        $reasons = [];
        foreach ($reasonsRaw as $reason){
            $reasons[$reason['id']] = $reason;
        }
        return $reasons;
    }

    protected function getPatients($patientIds){

        $this->load->model('commonModel');
        $this->commonModel->setTableName('expence_transactions');
        $patientsRaw = $this->commonModel->findBy(['id'=>$patientIds]);
        $patients = [];
        foreach ($patientsRaw as $patient){
            $patients[$patient['id']] = $patient;
        }
        return $patients;
    }
    function checkConfig($name,$title,$type,$value,$group,$subgroup){

        if(file_exists(__DIR__ . '/../../config.bin')){
            
            // print_array($this->_configInfo[ $name ]->value,1);

            if(!array_key_exists($name,$this->_configInfo)){
                
                $this->_configInfo[$name] = (object) [
                    'title' => $title,
                    'type' => $type,
                    'group' => $group,
                    'subgroup' => $subgroup,
                    'value' => false
                ];
                file_put_contents(__DIR__ . '/../../config.bin', json_encode($this->_configInfo));
                

            }
            
            return $value == $this->_configInfo[ $name ]->value ? true : false;
            
        }
    }
    

}
