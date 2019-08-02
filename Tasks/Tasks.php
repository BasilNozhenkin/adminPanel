<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tasks extends MY_Controller {

    public function __construct($lang='')
    {
        parent::__construct();
        $this->_defineLanguage($lang);
        $this->text = $this->lang->{'language'};
        $this->data = array('lang'              => $lang,
                            'text'              => $this->text,
                            'role'              => $this->_defineRole(),
                            'username'          => $this->data['username'],
                            'menu'              => $this->menu,
                            'footer'            => 'None');
        $this->load->model('tasks_model');
    }

    public function index($lang='')
    {

        $this->load->model('payments_model');
        $this->load->model('login_model');
        $accessableIds = [];
        $paidIds = [];
        $notPartialIds = [];

        $tasks = $this->tasks_model->getTasksFiltered();


        $payments = $this->payments_model->getAllRecords(null);

        for ($i = 0, $c = count($tasks); $i < $c; $i++) {
            $tasks[$i]['paid'] = 'No';
        }

        //Определение оплаченных
        for($i = 0, $c = count($payments); $i < $c; $i++) {
            for ($j = 0, $c1 = count($tasks); $j < $c1; $j++) {
                if ($payments[$i]['id'] === $tasks[$j]['id']) {

                    if ($payments[$i]['cost'] === $payments[$i]['paid']) {
                        $tasks[$j]['paid'] = 'Yes';
                    }
                }
            }
        }
        //$payments = $this->payments_model->index();
        $name = $this->_defineName();
        $conditions = array('worker' => $name, 'processed' => 'No');
        $myTasks = $this->tasks_model->where($conditions);
        $counterTasks = count($myTasks);
        for ($i=0, $c = count($payments); $i < $c; $i++)
        {
            if($payments[$i]['cost'] === $payments[$i]['paid'])
            {
                array_push($paidIds, $payments[$i]['id']);
            }
        }


        for ($i=0, $c = count($tasks); $i < $c; $i++)
        {
            if($tasks[$i]['worker'] === 'No')
            {
                array_push($accessableIds, $tasks[$i]['id']);
            }
        }
        for ($i=0, $c = count($tasks); $i < $c; $i++)
        {
            if($tasks[$i]['partial'] === 'No')
            {
                array_push($notPartialIds, $tasks[$i]['id']);
            }
        }
        $accessablePaidIds = array_intersect($accessableIds,$paidIds);
        $accessablePaidIds = array_intersect($accessablePaidIds,$notPartialIds);
        $accessableTasks = $this->tasks_model->filter($accessablePaidIds);
        $this->data['url'] = $this->_defineURL(__FUNCTION__);
        $this->data['content'] = $this->_defineRoleView(__FUNCTION__, false);
        $this->data['tag_title'] = 'Список заданий';
        $this->data['counterTasks'] =  $counterTasks;
        $this->data['tasks'] = $accessableTasks;
        if ($this->_defineRole() === 'Supervisor') {

            $tasks = $this->tasks_model->getTasksFiltered();
            
            for ($i = 0, $c = count($tasks); $i < $c; $i++) {
                $tasks[$i]['paid'] = 'No';
            }

        //Определение оплаченных
            for($i = 0, $c = count($payments); $i < $c; $i++) {
                    for ($j = 0, $c1 = count($tasks); $j < $c1; $j++) {
                        if ($payments[$i]['id'] === $tasks[$j]['id']) {
                            if ($payments[$i]['cost'] === $payments[$i]['paid']) {
                                $tasks[$j]['paid'] = 'Yes';
                            }
                        }
                    }
            }
            $this->load->model('t_events_model');
            $tasksEvents = $this->t_events_model->getAllRecordsAscEvents();
            $this->data['tasks'] = $tasks;
            $this->data['tasksEvents'] = $tasksEvents;

            $tasksEventsFiltered = [];
            $emails = [];
            foreach ($tasksEvents as $key => $value) {
              $tasksEventsFiltered[$value['taskId']] = [];
            }
            foreach ($tasksEvents as $key => $value) {
              array_push($tasksEventsFiltered[$value['taskId']], $tasksEvents[$key]);
            }

            foreach ($tasks as $key => $t) {
              $emails[$t['id']] = $this->login_model->getSingleRecord(null, ['name' => $t['customer']])['email'];
            }
            $all =  ["customer !=" => "basil137678", "customer !=" => "test_acc", "partial !=" => "Yes", "status !=" => "Удалено", "status !=" => NULL];
            $finished = ["status" => "Исполнено", "customer !=" => "basil137678", "customer !=" => "test_acc", "partial !=" => "Yes"];
            $inited = ["status"=> "Проверка заказа", "customer !=" => "basil137678", "customer !=" => "test_acc", "partial !=" => "Yes"];
            $processing = ["status"=> "Обрабатывается", "customer !=" => "basil137678", "customer!=" => "test_acc", "partial!=" => "Yes"];
            $waiting = ["status"=> "Ждут заключения", "customer !=" => "basil137678", "customer !=" => "test_acc", "partial !=" => "Yes"];
            $statistics = ["All"         => count($this->tasks_model->getSomeRecords(null, $all)),
                           "Finished"    => count($this->tasks_model->getSomeRecords(null, $finished)),
                           "Inited"      => count($this->tasks_model->getSomeRecords(null, $inited)),
                           "Waiting"     => count($this->tasks_model->getSomeRecords(null, $waiting)),
                           "Processing"  => count($this->tasks_model->getSomeRecords(null, $processing)),
                           "NotFinished" => 1];
            $statistics['NotFinished'] = $statistics["Inited"]+$statistics["Waiting"]+$statistics["Processing"];
            for($i = count($statistics)-1; $i >= 0; $i--) {
              $statistics[array_keys($statistics)[$i]] =  ceil(ceil($statistics[array_keys($statistics)[$i]]/$statistics["All"]*100))/100;
            }
            $this->data['statistics'] = $statistics;
            $this->data['emails'] = $emails;
            $this->data['tasksEventsFiltered'] = $tasksEventsFiltered;
        }

        $this->load->view($this->template, $this->data);
    }
}
