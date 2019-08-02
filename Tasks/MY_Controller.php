<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	  public $template;

    public function __construct() 
    {
        parent::__construct();
        $this->template = 'templates/master';
        if ($this->load->library('session')) {
            $this->data['username'] = $this->session->userdata('username');
            $this->session->set_userdata('username', $this->data['username']);
        }
    }
    public function _defineLanguage($lang)
    {
        if ($lang === '') {
            $lang = 'russian';
        }
        $this->lang->load('content', $lang);
        $this->config->set_item('language', $lang);     
    }
    public function _defineUrl($fn)
    {
         $className = get_class($this);
         if ($fn === 'index') 
         {
             return $className;
         }
         else 
         {
             return $className.'/'.$fn;
         }
         
    }
    public function _defineRole() 
    {
        $this->load->model('login_model');
        if (isset($this->data['username'])) 
        {
            $res = $this->login_model->verify_user($this->input->post('email'), $this->input->post('password'));
            $user = $this->login_model->showByEmail($this->data['username']);
            return $user['role'];
        }
        else {
            return 'Guest';  
        }
    }
    public function _defineName() 
    {
        $this->load->model('login_model');
        if (isset($this->data['username'])) 
        {
            $res = $this->login_model->verify_user($this->input->post('email'), $this->input->post('password'));
            $user = $this->login_model->showByEmail($this->data['username']);
            return $user['name'];
        }
    }
    public function _defineId() 
    {
        $this->load->model('login_model');
        if (isset($this->data['username'])) 
        {
            $res = $this->login_model->verify_user($this->input->post('email'), $this->input->post('password'));
            $user = $this->login_model->showByEmail($this->data['username']);
            return $user['UserID'];
        }
    }
    public function _defineRoleView($fn, $common) 
    {
        $public = ['index', 'show', 'organizations', 'request', 'instruction', 'services', 'success', 'calendar', 'science', 'findCouncil', 'council', 'addModeratorData', 'getStructure', 'addDoctor', 'getStatistics', 'thirdpartyPayment'];
        $publicRestrictions = ['Program_index', 'Profile_index', 'Tasks_index', 'Tasks_show', 'Services_index', 'Articles_index', 'Moderators_index', 'Doctors_index'];
        $private = ['Guest'        => ['None'],
                    'Worker'       => ['Articles_index', 'Tasks_received', 'Tasks_index', 'Tasks_show', 'Tasks_addComment'],
                    'Individual'   => ['Program_index', 'Articles_index','Tasks_provided', 'Tasks_delete', 'Services_index', 'Tasks_provideAttachments'],
                    'Organization' => ['Program_index', 'Articles_index','Tasks_provided', 'Tasks_delete', 'License_create', 'License_update', 'Services_index'],
                    'Supervisor'   => ['Program_index', 'Articles_index','Tasks_index', 'Users_createWorker', 'Services_index', 'Tasks_merge', 'Tasks_changePrice', 'Tasks_changeOrgan', 'Tasks_changeStatus', 'Tasks_provideAttachments'],
                    'Admin'        => ['Moderators_index','Moderators_create', 'Moderators_statistics', 'Doctors_create','Tasks_provided', 'Tasks_changePrice', 'Doctors_index'],
                    'Doctor'       => ['Doctors_cabinet','Tasks_provided', 'Services_VizualizationAndProcessing'],
                    'Moderator'    => ['Tasks_provided', 'Services_VizualizationAndProcessing']
                    ];

          $className = get_class($this);
          $role = $this->_defineRole();
          $url = $className.'_'.$fn;
          if(in_array($url, $private[$role]))
          {
              if ($common === true) 
              {
                  $content = $className.'_'.$fn.'_view';
              }
              else 
              {
                  $role = strtolower($this->_defineRole());
                  $content = $className.'_'.$fn.'_view_'.$role;
              } 
          }      
          elseif (in_array($fn, $public))
          {
              if(!in_array($url, $publicRestrictions))
              {
                  if ($common === true) 
                  {
                     $content = $className.'_'.$fn.'_view';
                  }
                  else 
                  {
                    $role = strtolower($this->_defineRole());
                    $content = $className.'_'.$fn.'_view_'.$role;
                  } 
              }
              else 
              {
                  set_status_header(401);
                  $content = 'Error_401'; 
              }
          }
          else {
              set_status_header(401);
              $content = 'Error_401';
          }
        return $content;
    }

    public function _sendRegisterNotification($data)
    {
        $user = $data['email'];
        $registerSupervisors = ['Gornushkin75@mail.ru', 'nshapovalovn@yandex.ru', 'mae664128@gmail.com', 'vladaurosova14@gmail.com', 'ae.ermakov995@gmail.com'];
        $subject = 'Регистрация на сайте www.medinnovations.com';

        $message = '<div>Добро пожаловать!</div>
                    <div>Ваши персональные данные для входа:</div>
                    <div>Почтовый адрес:'. $data['email'].'</div>
                    <div>Пароль:'. $data['password'].'</div>
                    <div>C уважением, ваш http://medinnovations.ru</div>';
        $messageForSupervisors = '<div>На сайте произошла регистрация c почты '.$data['email'].' и логина '.$data['name'].'</div>';   ;            
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 
        
        for ($i=0, $l = count($registerSupervisors); $i < $l; $i++) { 
            mail($registerSupervisors[$i], $subject, $messageForSupervisors, $headers);
        }

        mail($user, $subject, $message, $headers);
    } 
    public function _sendEndTaskNote($data, $id)
    {
        $user = $data['email'];
        $subject = 'Задание № '.$id.' выполнено';
        $message = '<div>Рады сообщить вам, что ваш заказ с номером'.$id.' выполнен.</div>
                    <div>C уважением, ваш http://medinnovations.ru</div>';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 

        mail($user, $subject, $message, $headers);
    }
    public function _sendPriceSetNote($email, $id)
    {
        $subject = 'Цена на задание № '.$id.' выставлена';
        $message = '<div>Рады сообщить вам, что цена на заказ с номером'.$id.' выставлена.</div>
                    <div>C уважением, ваш http://medinnovations.ru</div>';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 

        mail($email, $subject, $message, $headers);
    }
    public function _sendUploadNote($username, $email, $role)
    {
        $Supervisors = ['Gornushkin75@mail.ru','mae664128@gmail.com', 'nshapovalovn@yandex.ru', 'vladaurosova14@gmail.com', 'ae.ermakov995@gmail.com', 'nozhenkin.vasiliy@mail.ru'];
        $subject = 'Загрузка снимков на сайте www.medinnovations.ru';

        if ($role == 'Individual') {
        	$role = 'Физическое лицо';
        }
        else if($role == 'Organization') {
        	$role = 'Юридическое лицо';
        }
        else if($role == 'Supervisor') {
        	$role = 'Администратор';
        }
      	$date = new DateTime("now");
      	$currDate = $date->format('Y-m-d');
      	$subject = 'Загрузка снимков на сайте www.medinnovations.ru';
      	$data['name'] = "medinnovations.ru "; 
      	$data['topic'] = $subject;
      	$data['text'] = "<div> Почта: ".$email."</div>".
      					"<div> Логин заказчика: ".$username."</div>".
      					"<div> Тип пользователя: ".$role."</div>";
      	$data['date'] = $currDate;
      	$data['phone'] = "8(912)6952728";
      	$headers = "MIME-Version: 1.0\r\n";
      	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 
	
	    $message = $this->load->view('Mail_template.php', $data, TRUE); 
      	for ($i=0, $l = count($Supervisors); $i < $l; $i++) { 
            mail($Supervisors[$i], $subject, $message, $headers);
        }
        
    }
    public function _sendPaymentNote($data)
    {
        $Supervisors = ['mae664128@gmail.com'];
        $subject = 'Оплата снимков на сайте www.medinnovations.com';

        $message = '<div>Добого времени суток, уважаемый.</div>
                    <div>Произошла оплата снимков.</div>
                    <div>C уважением, ваш http://medinnovations.ru</div>';
        $messageForSupervisors = '<div>Добого времени суток, уважаемый.</div>'.
                                 '<div>На сайте произошла оплата c логина'.$data['name'].'</div>'.
                                 '<div>C уважением, ваш http://medinnovations.ru</div>';            
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 
        
        for ($i=0, $l = count($Supervisors); $i < $l; $i++) { 
            mail($Supervisors[$i], $subject, $messageForSupervisors, $headers);
        }
    }
    public function _sendReportTasks($numTasks) 
    {
       
    }
    public function _sendReportSalary($workers)
    {
       
    }
    public function _testSend() 
    {
      $date = new DateTime("now");
      $user = 'nozhenkin.vasiliy@mail.ru';
      $currDate = $date->format('Y-m-d');
      $data['name'] = "medinnovations.ru"; 
      $data['topic'] = 'Обработка снимков';
      $subject = $data['topic'];
      $data['text'] = "Основной текст сообщения";
      $data['date'] = $currDate;
      $data['email'] = "наш e-mail";
      $data['phone'] = "8(912)6952728";
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 

      $message = $this->load->view('Mail_template.php', $data, TRUE); 
      mail($user, $subject, $message, $headers);
    }
    public function _sendQuestionToAdministrators($question, $mail, $taskId) 
    {
      $date = new DateTime("now");
      $user = 'mae664128@gmail.com';
      $currDate = $date->format('Y-m-d');
      
      $data['name'] = "medinnovations.ru "; 
      $data['topic'] = 'Поступил вопрос от пользователя c e-mail-ом: '.$mail." по заданию ".$taskId;
      $subject = $data['topic'];
      $data['text'] =  $question;
      $data['date'] = $currDate;
      $data['phone'] = "8(912)6952728";
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 

      $message = $this->load->view('Mail_template.php', $data, TRUE); 
      mail($user, $subject, $message, $headers);
    }
    public function _sendStatusChangeNotificationCustomers($user, $status, $text, $taskId) 
    {
      $date = new DateTime("now");
      $currDate = $date->format('Y-m-d');
      $data['name'] = "medinnovations.ru "; 
      $data['topic'] = 'Статус заявки № '.$taskId.'был  изменен "'.$status.' "';
      $subject = $data['topic'];
      $data['text'] =  $text;
      $data['date'] = $currDate;
      $data['phone'] = "8(912)6952728";
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 

      $message = $this->load->view('Mail_template.php', $data, TRUE); 
      mail($user, $subject, $message, $headers);
    }
    public function _sendStatusChangeNotificationWorkers($status, $text, $taskId) 
    {
      $date = new DateTime("now");
      $user = 'nozhenkin.vasiliy@mail.ru';
      $currDate = $date->format('Y-m-d');
      $subject = 'Отчёт за '.$currDate;
      $data['name'] = "medinnovations.ru "; 
      $data['topic'] = 'Админам: Статус заявки № '.$taskId.' был  изменен на "'.$status.' "';
      $subject = $data['topic'];
      $data['text'] =  $text;
      $data['date'] = $currDate;
      $data['phone'] = "8(912)6952728";
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 

      $message = $this->load->view('Mail_template.php', $data, TRUE); 
      mail($user, $subject, $message, $headers);
    }
    public function guid()
    {
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    public function _moveFilesToFolder($tmpFile, $uploadfile, $onErrorMessage)
    {
        if(move_uploaded_file($tmpFile, $uploadfile))
        {
            move_uploaded_file($tmpFile, $uploadfile);
            echo "<div class='alert alert-success'>Вы успешно загрузили файл</div>";
        }
        else 
        {
            echo "<div class='alert alert-danger'>".$onErrorMessage."</div>";
        }
    }
    public function _moveFileToFolder($uploaded, $folder)
    {
        $tmpFile = $_FILES[$uploaded]['tmp_name'];
        $uploadfile = $folder .'/'. basename($_FILES[$uploaded]['name']);       
        if(move_uploaded_file($tmpFile, $uploadfile))
        {
            move_uploaded_file($tmpFile, $uploadfile);
            echo "<div class='alert alert-success'>Вы успешно загрузили файл</div>";
        }
        else 
        {
            echo "<div class='alert alert-danger'>Произошел сбой при загрузке файлов</div>";
        }
    }

    public function _moveMultipleFilesToFolder($uploaded, $folder)
    {
        for ($i = 0, $c = count($_FILES[$uploaded]['tmp_name']); $i < $c; $i++)
        {
            $tmpFile = $_FILES[$uploaded]['tmp_name'][$i];
            $uploadfile = $folder .'/'. basename($_FILES[$uploaded]['name'][$i]);
            if(move_uploaded_file($tmpFile, $uploadfile))
            {
                move_uploaded_file($tmpFile, $uploadfile);
                echo "<div class='alert alert-success'>Вы успешно загрузили файл</div>";
            }
            else 
            {
                echo "<div class='alert alert-danger'>Произошел сбой при загрузке файлов</div>";
            }
        }
    }
}