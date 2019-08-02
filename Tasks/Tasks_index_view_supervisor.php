<script type="text/javascript" src="<?= base_url(); ?>assets/js/moment-with-locales.js"></script>
<script src="https://api.trello.com/1/client.js?key=d7d7a689ea01ebd8e691cde9d145383d" type="text/javascript"></script>
<script>
var postTrelloData = function(id, customer, time, organ) {
    var success = function() {
      var token = Trello.token();
      console.log(token);
          var newCard = {name: moment(time).format("DD.MM.")+"R-"+id,
                   desc: "**Области исследования**:"+ "\n\n"+"- "+organ+ "\n"+" **Заказчик**: "+customer,
                   pos: "top",
                   idList: "5b605d3ded170fc6fc39712d"
    };
    window.Trello.post('/cards/', newCard);
    };
    var error = function() {
      console.log("error");
    };

    var opts = {
        type: "popup",
        name: "контроль-снимков",
        success: success,
        error: error,
        expiration: 'never',
        scope: {
            read: 'true',
            write: 'true',
        }
    };
    window.Trello.authorize(opts);
}

</script>
<script src="http://d3js.org/d3.v3.min.js" language="JavaScript"></script>
<script src="http://www.medinnovations.ru/assets/js/liquidFillGauge.js" language="JavaScript"></script>

   <div class="row">
    <div class="col-md-12">
      <div class="row">
         <div class="col-md-2">
          <div class="text-center">
               <svg id="fillgauge2" height="150"></svg>
          </div>
         
          <div class="accordion"><a href="#" class="btn btn-sm animated-button victoria-three">Проверка заказа</a></div>
          <div class="panel">
<?php foreach($tasks as $t): ?>
<?php if ($t['status'] === 'Проверка заказа' && $t['partial'] !== 'Yes' && $t['customer'] !== 'basil137678' && $t['customer'] !== 'test_acc'): ?>
            <div id="modalTask<?=$t['id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabelTask<?=$t['id'];?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <div class="container"> 
                                <div class="row">
                                    <div class="col col-lg-5">
                                        <h4 id="modalLabelTask<?=$t['id'];?>">
                                            <i class="fa fa-list-alt" aria-hidden="true"> </i> Заявка № <?=$t['id'];?>
                                        </h4>
                                        <h6>
                                            <i class="fa fa-clock-o" aria-hidden="true"> </i> <?=$t['timeCreated'];?>
                                        </h6>
                                    </div>
                                    <div class="col col-lg-2">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success dropdown-toggle" id="btnGroupStatus" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Исполнено</button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupStatus">
                                                <a class="dropdown-item" href="#"><div>Отменен</div></a>
                                                <a class="dropdown-item" href="#"><div>Проверка заявки</div></a>
                                                <a class="dropdown-item" href="#"><div>В исполнении</div></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-lg-2">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    </div>
                                </div>
                            </div>
                <div class="row mt-10">
                    <div class="col-lg-6 col-xs-6">
                        <input type="text" class="form-control" value="<?= $t['customer']; ?>" readonly="readonly" style="color: black;">
                     </div>
                    <div class="col-lg-6 col-xs-6">
                        <input type="text" class="form-control" value="<?= $emails[$t['id']]; ?>" readonly="readonly" style="color: black;">
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-12 col-xs-12">
                        <button class="form-control" onclick='postTrelloData("<?= $t['id']; ?>","<?= $emails[$t['id']]; ?>","<?= $t['timeCreated']; ?>", "<?= $t['organ']; ?>");'>Создать в Trello <i class="fa fa-trello" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <?php $date = date_create($t['timeCreated']);
                              $timeMD = date_format($date, 'm-d');
                              $timeYMD =  date_format($date, 'Y-m-d');
                              $id = $timeMD."-R-".$t['id'];
                        ?>
                        <a href="<?= base_url().'Tasks/generateGooogleDoc/'.$id.'/'.$timeYMD;?>"><button class="form-control">Создать отчетный документ <i class="fa fa-file-text-o" aria-hidden="true"></i></button></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-xs-8">
                        <h4><i class="fa fa-file" aria-hidden="true"></i> Области исследования:</h4>
                    </div>

                    <div class="col-lg-4 col-xs-4">
<?php $organs = explode(',', $t['organ']); ?>
<?php foreach ($organs as $key => $o) :?>
                        <h4  style="font-size: 12px"><i class="fa fa-hashtag" aria-hidden="true"></i> <?= $o; ?></h4>
<?php endforeach; ?>
                    </div>
                    <div class="col-lg-4 col-xs-4"></div>
                </div>
                </div>
                <div class="row">
                <?php
                $attr = array('class' => 'form-horizontal',
                           'role' => 'form');
                echo form_open('Tasks/changeOrgan/'.$t['id'], $attr);
                ?>
                <div class="col-lg-6 col-lg-offset-1 col-xs-6">
                    <?php
                        $options = ['Голова и шейный отдел', 'Грудная клетка', 'Брюшная полость и забрюшинное пространство', 'Малый таз',  'Позвоночник',  'Кости свободных верхних и нижних конечностей'];
                        $excludedOrgans = $organs;
                        foreach($excludedOrgans as $key){
                            $keyToDelete = array_search($key, $options);

                            unset($options[$keyToDelete]);
                        }
                        foreach ($options as $key => $value) {
                            $options[$value] = $value;
                            unset($options[$key]);
                        }
                         $attr = array ('class'   => 'form-control');
                        if(count($options) !== 0) {
                            echo form_dropdown('organ', $options, array_keys($options)[0], $attr);
                        }
                        else {
                            echo "<h4>Уже выбраны все области</h4>";
                        }
                    ?>
                </div>
                <div class="col-lg-4 col-xs-4">
                <?php
                    $attr= array('class' => 'form-control');
                    if(count($options) !== 0) {
                       echo form_submit('submit', 'Добавить область', $attr);
                    }
                    else {
                    }
                ?>
                </div>
                <?= form_close(); ?>
                </div>
               
               
                <h4><i class="fa fa-money" aria-hidden="true"></i> Ценообразование</h4>
                <div class="row mt-10">
                    <div class="col-lg-6 col-lg-offset-1 col-xs-6 col-xs-offset-1">
                    <?php if(is_null($t['cost'])): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет цены]</button>
                    <?php else: ?>
                        <button type="button" class="form-control good" style="cursor: default;">[<?= $t['cost']; ?>]</button>
                    <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col-xs-4">
                    <?php if($t['paid'] === 'Yes'):?>
                        <button type="button" class="form-control good" style="cursor: default;">[Оплачен]</button>
                    <?php else: ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Не оплачен] </button>
                    <?php endif; ?>
                    </div>
                     <!--<div class="col-lg-4 col-xs-4">
                    <?php if($t['worker'] === 'No'): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет обработчика]</button>
                    <?php else: ?>
                       <button type="button" class="form-control good" style="cursor: default;">[<?= $t['worker']; ?>]</button>
                    <?php endif; ?>
                    </div>-->
                </div>
                          <div class="modal-body">
                             <div class="container-fluid bd-example-row">
                                <div class="row">
                                    <?php if(is_null($t['description'])):?>
                                    <h4> <i class="fa fa-book" aria-hidden="true"></i> Описание отсутствует</h4>
                                    <?php else:?>
                                    <h4><?= $t['description']; ?></h4>
                                    <?php endif;?>
                                    <?php if(isset($t['status'])):?>
                                    <h4>
                                        <?php
                                        $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                        echo form_open('Tasks/changeStatus/'.$t['id'], $attr);
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <h4>Текущий статус: </h4>
                                            </div>
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-5">
                                                <h4><?= $t['status']; ?></h4>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </div>

                                            <div class="col-lg-5 col-lg-offset-1 col-xs-5">
                                            <?php
                                            $options = ['Проверка заказа', 'Обрабатывается', 'Ждут заключения', 'Исполнено'];
                                            $keyToDelete = array_search($t['status'], $options);
                                            unset($options[$keyToDelete]);
                                            foreach ($options as $key => $value) {
                                                $options[$value] = $value;
                                                unset($options[$key]);
                                            }
                                                $attr = array ('class'   => 'form-control');
                                                echo form_dropdown('status', $options, array_keys($options)[0], $attr);
                                            ?>
                                            </div>

                                        </div>
                                        <?php
                                                $attr = array('class' => 'form-control');
                                                echo form_submit('submit', 'Изменить статус', $attr);
                                        ?>
                                        <?= form_close(); ?>
                                    </h4>
                                    <?php endif;?>
                                    <div class="scrollable-table">
                                    <table class="table table-striped table-header-rotated">
                                      <thead>
                                        <tr>
                                          <th></th>
                                          <th class="rotate-45" ><div><span>Снимки</span></div></th>
                                          <th class="rotate-45"><div><span>Анализы</span></div></th>
                                          <th class="rotate-45"><div><span>Анамнез</span></div></th>
                                          <th class="rotate-45"><div><span>Заключение</span></div></th>
                                          <th class="rotate-45"><div><span>Задача</span></div></th>
                                        </tr>
                                      </thead>
                                      <tbody>

                                        <tr>
                                          <th class="row-header"><h4><i class="fa fa-check-square-o" aria-hidden="true"></i> Чеклист</h4></th>
                                          <?php
                                            $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                            echo form_open('Tasks/changeReceipts/'.$t['id'], $attr);
                                            ?>
                                          <?php foreach (explode(",", $t['receipts']) as $val=>$r):?>
                                              <?php if($r == 1): ?>
                                                <td><?php echo form_checkbox("column".'[]', $val, true);?></td>
                                              <?php else: ?>
                                                <td><?php echo form_checkbox("column".'[]', $val, false);?></td>
                                              <?php endif; ?>
                                          <?php endforeach; ?>
                                        </tr>
                                      </tbody>
                                    </table>
                                    </div>
                                    <h4> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Отчет </h4>
                                    <h4> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Заключение </h4>
                                    <h4>
                                    <?php
                                        $attr = array('class' => 'form-control');
                                        echo form_submit('submit', 'Изменить чеклист', $attr);
                                    ?>
                                    </h4>
                                    <?= form_close(); ?>
                                    <h4> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Дополнительные вложения </h4>
                                    <div>
                                    <?php if(!empty($t['attachments'])):?>
                                    <?php if (count(explode(',', $t['attachments'])) > 1):?>
                                        <?php foreach (explode(',', $t['attachments']) as $key=>$atachment):?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$atachment; ?>"><h4>Скачать <?= $atachment; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/'.$key; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$t['attachments']; ?>"><h4>Скачать <?= $t['attachments']; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                           <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/0'; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    </div>
                                    <form enctype="multipart/form-data" action="http://medinnovations.ru/Tasks/provideAttachments/<?= $t['id']; ?>" method="POST" class="form-horizontal">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="700000000" />

                                    <div class="form-group">
                                        <div class="col-lg-7 col-lg-offset-1 col-xs-7">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-upload"></i></span>
                                                 <input  class="form-control"style='padding: 2px;' name="userfile" type="file"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-lg-offset-1 col-xs-3">
                                            <input type="submit" class="form-control" value="Отправить" />
                                        </div>

                                    </div>
                                    </form>
                                    <h4> <i class="fa fa-user-md" aria-hidden="true"></i> Снимки</h4>
<?php if($t['complex'] === 'No'): ?>
                                    <span><a href="<?= base_url().'Tasks/trace/'.$t['id'].'/downloaded'; ?>"><?= $text['DOWNLOAD_TASK'];?></a></span>
<?php else: ?>
<?php $ids = explode(',', $t['complex']); ?>
                                    <div class="container">
                                    <div class="row col-lg-10 col-xs-10">
<?php foreach($ids as $key=>$id): ?>
                                    <div class="col-lg-3 col-xs-3">
                                        <a href="<?= base_url().'Tasks/trace/'.$id.'/downloaded'; ?>"><?= 'Часть '. ($key+1); ?></a>
                                    </div>
<?php endforeach; ?>
                                    </div>
                                    </div>
                                    <?php endif; ?>
                                    <h4> <i class="fa fa-history" aria-hidden="true"></i>  История</h4>
                                    <div class="container">

                                    <div class="row col-lg-6">
<?php if (isset($tasksEventsFiltered[$t['id']])) :?>
<?php foreach ($tasksEventsFiltered[$t['id']] as $te): ?>
                                            <div class="col-lg-12 col-xs-12 ">
                                                <div><?= $te['eventDescription']; ?></div>
                                            </div>
                                    <?php endforeach ?>
                                    <?php else:?>
                                        <h4>История отсутствует</h4>
<?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                    </div>
                    </div>
                    <?php  $time = explode(" ", $t['timeCreated']);
                           $day = $time[0];
                           $h = $time[1];
                    ?>
                    <div class="container-fluid">
                        <div class="row check">
                            <div class="col-md-6 col-md-offset-5">
                                <input type="checkbox" class="ids" id="cbx<?= $t['id']; ?>" style="display:none" name="<?= $t['id']; ?>" />
                                <label for="cbx<?= $t['id']; ?>" class="toggle"><span></span>
                            </div>
                        </div>
                    </div>
                    <div class="card card-1" style="text-align: center;" data-toggle="modal" data-target="#modalTask<?=$t['id'];?>">
                        <span style="font-weight: 900;">[<?= $t['customer']; ?>]</span></br>
                        <span style="font-weight: 900;">[# <?= $t['id']; ?>]</span></br>
                        <span class="card-time" style="text-align: center; font-weight: 900;" data-time="<?= $t['timeCreated']; ?>"></span>
                    </div>
<?php endif; ?>
<?php endforeach;?>
        </div>
        </div>

            <div class="col-md-2">
                <div class="text-center">
                    <svg id="fillgauge3" height="150"></svg>
                </div>
                <div class="accordion"><a href="#" class="btn btn-sm animated-button victoria-three">Обрабатывается</a></div>
                <div class="panel">
<?php foreach($tasks as $t): ?>
<?php if ($t['status'] === 'Обрабатывается' && $t['partial'] !== 'Yes' && $t['customer'] !== 'basil137678' && $t['customer'] !== 'test_acc'): ?>
                        <div id="modalTask<?=$t['id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabelTask<?=$t['id'];?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalLabelTask<?=$t['id'];?>">
                                <i class="fa fa-list-alt" aria-hidden="true"> </i>Заказ № <?=$t['id'];?></br>
                                <i class="fa fa-clock-o" aria-hidden="true"> </i><?=$t['timeCreated'];?>
                            </h4>
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <button class="form-control" onclick='postTrelloData("<?= $t['id']; ?>","<?= $emails[$t['id']]; ?>","<?= $t['timeCreated']; ?>", "<?= $t['organ']; ?>");'>Создать в Trello <i class="fa fa-trello" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 col-xs-5">
                        <h4><i class="fa fa-file" aria-hidden="true"></i> Области исследования:</h4>
                    </div>

                    <div class="col-lg-7 col-xs-7">
<?php $organs = explode(',', $t['organ']); ?>
<?php foreach ($organs as $key => $o) :?>
                        <h4  style="font-size: 12px"><i class="fa fa-hashtag" aria-hidden="true"></i> <?= $o; ?></h4>
<?php endforeach; ?>
                    </div>
                </div>
                </div>
                <div class="row">
                <?php
                $attr = array('class' => 'form-horizontal',
                           'role' => 'form');
                echo form_open('Tasks/changeOrgan/'.$t['id'], $attr);
                ?>
                <div class="col-lg-5 col-xs-5">
                    <?php
                        $options = ['Голова и шейный отдел', 'Грудная клетка', 'Брюшная полость и забрюшинное пространство', 'Малый таз',  'Позвоночник',  'Кости свободных верхних и нижних конечностей'];
                        $excludedOrgans = $organs;
                        foreach($excludedOrgans as $key){
                            $keyToDelete = array_search($key, $options);

                            unset($options[$keyToDelete]);
                        }
                        foreach ($options as $key => $value) {
                            $options[$value] = $value;
                            unset($options[$key]);
                        }
                         $attr = array ('class'   => 'form-control');
                        if(count($options) !== 0) {
                            echo form_dropdown('organ', $options, array_keys($options)[0], $attr);
                        }
                        else {
                            echo "<h4>Уже выбраны все области</h4>";
                        }
                    ?>
                </div>
                <div class="col-lg-7 col-xs-7">
                <?php
                    $attr= array('class' => 'form-control');
                    if(count($options) !== 0) {
                       echo form_submit('submit', 'Добавить область', $attr);
                    }
                    else {
                    }
                ?>
                </div>
                <?= form_close(); ?>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-3 col-lg-offset-1 col-xs-3 col-xs-offset-1">
                    <?php if(is_null($t['cost'])): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет цены]</button>
                    <?php else: ?>
                        <button type="button" class="form-control good" style="cursor: default;">[<?= $t['cost']; ?>]</button>
                    <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-xs-3">
                    <?php if($t['paid'] === 'Yes'):?>
                        <button type="button" class="form-control good" style="cursor: default;">[Оплачен]</button>
                    <?php else: ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Не оплачен] </button>
                    <?php endif; ?>
                    </div>
                     <div class="col-lg-4 col-xs-4">
                    <?php if($t['worker'] === 'No'): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет обработчика]</button>
                    <?php else: ?>
                       <button type="button" class="form-control good" style="cursor: default;">[<?= $t['worker']; ?>]</button>
                    <?php endif; ?>
                    </div>
                     <div class="col-lg-3 col-lg-offset-1 col-xs-3 col-xs-offset-1">
                        <button type="button" class="form-control good" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: default;">[<?= $t['customer']; ?>]</button>
                    </div>
                    <div class="col-lg-7 col-xs-7">
                       <input type="text" class="form-control" value="<?= $emails[$t['id']]; ?>" readonly="readonly" style="color: black;">
                    </div>
                </div>
                          <div class="modal-body">
                             <div class="container-fluid bd-example-row">
                                <div class="row">
                                    <?php if(is_null($t['description'])):?>
                                    <h4> <i class="fa fa-book" aria-hidden="true"></i> Описание отсутствует</h4>
                                    <?php else:?>
                                    <h4><?= $t['description']; ?></h4>
                                    <?php endif;?>
                                    <?php if(isset($t['status'])):?>
                                    <h4>
                                        <?php
                                        $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                        echo form_open('Tasks/changeStatus/'.$t['id'], $attr);
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <h4>Текущий статус: </h4>
                                            </div>
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-5">
                                                <h4><?= $t['status']; ?></h4>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </div>

                                            <div class="col-lg-5 col-lg-offset-1 col-xs-5">
                                            <?php
                                             $options = ['Проверка заказа', 'Обрабатывается', 'Ждут заключения', 'Исполнено'];


                                            $keyToDelete = array_search($t['status'], $options);
                                            unset($options[$keyToDelete]);
                                            foreach ($options as $key => $value) {
                                                $options[$value] = $value;
                                                unset($options[$key]);
                                            }
                                                $attr = array ('class'   => 'form-control');
                                                echo form_dropdown('status', $options, array_keys($options)[0], $attr);
                                            ?>
                                            </div>

                                        </div>
                                        <?php
                                                $attr = array('class' => 'form-control');
                                                echo form_submit('submit', 'Изменить статус', $attr);
                                        ?>
                                        <?= form_close(); ?>
                                    </h4>
                                    <?php endif;?>
                                    <div class="scrollable-table">
                                    <table class="table table-striped table-header-rotated">
                                      <thead>
                                        <tr>
                                          <th></th>
                                          <th class="rotate-45" ><div><span>Снимки</span></div></th>
                                          <th class="rotate-45"><div><span>Анализы</span></div></th>
                                          <th class="rotate-45"><div><span>Анамнез</span></div></th>
                                          <th class="rotate-45"><div><span>Заключение</span></div></th>
                                          <th class="rotate-45"><div><span>Задача</span></div></th>
                                        </tr>
                                      </thead>
                                      <tbody>

                                        <tr>
                                          <th class="row-header"><h4><i class="fa fa-check-square-o" aria-hidden="true"></i> Чеклист</h4></th>
                                          <?php
                                            $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                            echo form_open('Tasks/changeReceipts/'.$t['id'], $attr);
                                            ?>
                                          <?php foreach (explode(",", $t['receipts']) as $val=>$r):?>
                                              <?php if($r == 1): ?>
                                                <td><?php echo form_checkbox("column".'[]', $val, true);?></td>
                                              <?php else: ?>
                                                <td><?php echo form_checkbox("column".'[]', $val, false);?></td>
                                              <?php endif; ?>
                                          <?php endforeach; ?>
                                        </tr>
                                      </tbody>
                                    </table>
                                    </div>

                                    <h4>
                                    <?php
                                        $attr = array('class' => 'form-control');
                                        echo form_submit('submit', 'Изменить чеклист', $attr);
                                    ?>
                                    </h4>
                                    <?= form_close(); ?>
                                    <h4> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Дополнительные вложения </h4>
                                    <div>
<?php if(!empty($t['attachments'])):?>
<?php if (count(explode(',', $t['attachments'])) > 1):?>
<?php foreach (explode(',', $t['attachments']) as $key=>$atachment):?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$atachment; ?>"><h4>Скачать <?= $atachment; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/'.$key; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
<?php endforeach;?>
<?php else: ?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$t['attachments']; ?>"><h4>Скачать <?= $t['attachments']; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                           <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/0'; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
<?php endif; ?>
<?php endif; ?>
                                    </div>
                                    <form enctype="multipart/form-data" action="http://medinnovations.ru/Tasks/provideAttachments/<?= $t['id']; ?>" method="POST" class="form-horizontal">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="700000000" />

                                    <div class="form-group">
                                        <div class="col-lg-7 col-lg-offset-1 col-xs-7">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-upload"></i></span>
                                                 <input  class="form-control"style='padding: 2px;' name="userfile" type="file"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-lg-offset-1 col-xs-3">
                                            <input type="submit" class="form-control" value="Отправить" />
                                        </div>

                                    </div>
                                    </form>
                                    <h4> <i class="fa fa-user-md" aria-hidden="true"></i> Снимки</h4>
                                    <?php if($t['complex'] === 'No'): ?>
                                    <span><a href="<?= base_url().'Tasks/trace/'.$t['id'].'/downloaded'; ?>"><?= $text['DOWNLOAD_TASK'];?></a></span>
                                    <?php else: ?>
                                    <?php $ids = explode(',', $t['complex']); ?>
                                    <span>
                                    <?php foreach($ids as $key=>$id): ?>
                                    <a href="<?= base_url().'Tasks/trace/'.$id.'/downloaded'; ?>"><?= 'Часть '. ($key+1); ?></a>
                                    <?php endforeach; ?>
                                    </span>
                                    <?php endif; ?>
                                    <h4> <i class="fa fa-history" aria-hidden="true"></i>  История</h4>
                                    <div class="container">

                                    <div class="row col-lg-6">

                                    <?php if (isset($tasksEventsFiltered[$t['id']])) :?>
                                    <?php foreach ($tasksEventsFiltered[$t['id']] as $te): ?>
                                            <div class="col-lg-12 col-xs-12 ">
                                                <div><?= $te['eventDescription']; ?></div>
                                            </div>
                                    <?php endforeach ?>
                                    <?php else:?>
                                        <h4>История отсутствует</h4>
                                    <?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                    </div>
                    </div>
                    <?php  $time = explode(" ", $t['timeCreated']);
                           $day = $time[0];
                           $h = $time[1];
                    ?>
                    <div class="container-fluid">
                        <div class="row check">
                            <div class="col-md-6 col-md-offset-5">
                                <input type="checkbox" class="ids" id="cbx<?= $t['id']; ?>" style="display:none" name="<?= $t['id']; ?>" />
                                <label for="cbx<?= $t['id']; ?>" class="toggle"><span></span>
                            </div>
                        </div>
                    </div>
                    <div class="card card-1" style="text-align: center;" data-toggle="modal" data-target="#modalTask<?=$t['id'];?>">
                        <span style="font-weight: 900;">[<?= $t['customer']; ?>]</span></br>
                        <span style="font-weight: 900;">[# <?= $t['id']; ?>]</span></br>
                        <span class="card-time" style="text-align: center; font-weight: 900;" data-time="<?= $t['timeCreated']; ?>"></span>
                    </div>

                    <?php endif; ?>
                <?php endforeach;?>
        </div>
        </div>

           <div class="col-md-2">
                <div class="text-center">
                    <svg id="fillgauge4" height="150"></svg>
                </div>
                <div class="accordion"><a href="#" class="btn btn-sm animated-button victoria-three">Ждут заключения</a></div>
                <div class="panel">
<?php foreach($tasks as $t): ?>
<?php if ($t['status'] === 'Ждут заключения' && $t['partial'] !== 'Yes' && $t['customer'] !== 'basil137678' && $t['customer'] !== 'test_acc'): ?>
                        <div id="modalTask<?=$t['id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabelTask<?=$t['id'];?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalLabelTask<?=$t['id'];?>">
                                <i class="fa fa-list-alt" aria-hidden="true"> </i>Заказ № <?=$t['id'];?></br>
                                <i class="fa fa-clock-o" aria-hidden="true"> </i><?=$t['timeCreated'];?>
                            </h4>
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <button class="form-control" onclick='postTrelloData("<?= $t['id']; ?>","<?= $emails[$t['id']]; ?>","<?= $t['timeCreated']; ?>", "<?= $t['organ']; ?>");'>Создать в Trello <i class="fa fa-trello" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-xs-4">
                        <h4 style="font-size: 12px"><i class="fa fa-file" aria-hidden="true"></i> Области исследования:</h4>
                    </div>

                    <div class="col-lg-8 col-xs-8">
<?php $organs = explode(',', $t['organ']); ?>
<?php foreach ($organs as $key => $o) :?>
                        <h4  style="font-size: 12px"><i class="fa fa-hashtag" aria-hidden="true"></i> <?= $o; ?></h4>
<?php endforeach; ?>
                    </div>
                    <div class="col-lg-4 col-xs-4"></div>
                </div>
                </div>
                <div class="row">
                <?php
                $attr = array('class' => 'form-horizontal',
                           'role' => 'form');
                echo form_open('Tasks/changeOrgan/'.$t['id'], $attr);
                ?>
                <div class="col-lg-6 col-lg-offset-1 col-xs-6">
                    <?php
                        $options = ['Голова и шейный отдел', 'Грудная клетка', 'Брюшная полость и забрюшинное пространство', 'Малый таз',  'Позвоночник',  'Кости свободных верхних и нижних конечностей'];
                        $excludedOrgans = $organs;
                        foreach($excludedOrgans as $key){
                            $keyToDelete = array_search($key, $options);

                            unset($options[$keyToDelete]);
                        }
                        foreach ($options as $key => $value) {
                            $options[$value] = $value;
                            unset($options[$key]);
                        }
                         $attr = array ('class'   => 'form-control');
                        if(count($options) !== 0) {
                            echo form_dropdown('organ', $options, array_keys($options)[0], $attr);
                        }
                        else {
                            echo "<h4>Уже выбраны все области</h4>";
                        }
                    ?>
                </div>
                <div class="col-lg-4 col-xs-4">
                <?php
                    $attr= array('class' => 'form-control');
                    if(count($options) !== 0) {
                       echo form_submit('submit', 'Добавить область', $attr);
                    }
                    else {
                    }
                ?>
                </div>
                <?= form_close(); ?>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-3 col-lg-offset-1 col-xs-3 col-xs-offset-1">
                    <?php if(is_null($t['cost'])): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет цены]</button>
                    <?php else: ?>
                        <button type="button" class="form-control good" style="cursor: default;">[<?= $t['cost']; ?>]</button>
                    <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-xs-3">
                    <?php if($t['paid'] === 'Yes'):?>
                        <button type="button" class="form-control good" style="cursor: default;">[Оплачен]</button>
                    <?php else: ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Не оплачен] </button>
                    <?php endif; ?>
                    </div>
                     <div class="col-lg-4 col-xs-4">
                    <?php if($t['worker'] === 'No'): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет обработчика]</button>
                    <?php else: ?>
                       <button type="button" class="form-control good" style="cursor: default;">[<?= $t['worker']; ?>]</button>
                    <?php endif; ?>
                    </div>
                     <div class="col-lg-3 col-lg-offset-1 col-xs-3 col-xs-offset-1">
                        <button type="button" class="form-control good" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: default;">[<?= $t['customer']; ?>]</button>
                    </div>
                    <div class="col-lg-7 col-xs-7">
                       <input type="text" class="form-control" value="<?= $emails[$t['id']]; ?>" readonly="readonly" style="color: black;">
                    </div>
                </div>
                          <div class="modal-body">
                             <div class="container-fluid bd-example-row">
                                <div class="row">
                                    <?php if(is_null($t['description'])):?>
                                    <h4> <i class="fa fa-book" aria-hidden="true"></i> Описание отсутствует</h4>
                                    <?php else:?>
                                    <h4><?= $t['description']; ?></h4>
                                    <?php endif;?>
                                    <?php if(isset($t['status'])):?>
                                    <h4>
                                        <?php
                                        $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                        echo form_open('Tasks/changeStatus/'.$t['id'], $attr);
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <h4>Текущий статус: </h4>
                                            </div>
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-5">
                                                <h4><?= $t['status']; ?></h4>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </div>

                                            <div class="col-lg-5 col-lg-offset-1 col-xs-5">
                                            <?php
                                             $options = ['Проверка заказа', 'Обрабатывается', 'Ждут заключения', 'Исполнено'];


                                            $keyToDelete = array_search($t['status'], $options);
                                            unset($options[$keyToDelete]);
                                            foreach ($options as $key => $value) {
                                                $options[$value] = $value;
                                                unset($options[$key]);
                                            }
                                                $attr = array ('class'   => 'form-control');
                                                echo form_dropdown('status', $options, array_keys($options)[0], $attr);
                                            ?>
                                            </div>

                                        </div>
                                        <?php
                                                $attr = array('class' => 'form-control');
                                                echo form_submit('submit', 'Изменить статус', $attr);
                                        ?>
                                        <?= form_close(); ?>
                                    </h4>
                                    <?php endif;?>
                                    <div class="scrollable-table">
                                    <table class="table table-striped table-header-rotated">
                                      <thead>
                                        <tr>
                                          <th></th>
                                          <th class="rotate-45" ><div><span>Снимки</span></div></th>
                                          <th class="rotate-45"><div><span>Анализы</span></div></th>
                                          <th class="rotate-45"><div><span>Анамнез</span></div></th>
                                          <th class="rotate-45"><div><span>Заключение</span></div></th>
                                          <th class="rotate-45"><div><span>Задача</span></div></th>
                                        </tr>
                                      </thead>
                                      <tbody>

                                        <tr>
                                          <th class="row-header"><h4><i class="fa fa-check-square-o" aria-hidden="true"></i> Чеклист</h4></th>
                                          <?php
                                            $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                            echo form_open('Tasks/changeReceipts/'.$t['id'], $attr);
                                            ?>
<?php foreach (explode(",", $t['receipts']) as $val=>$r):?>
<?php if($r == 1): ?>
                                            <td><?php echo form_checkbox("column".'[]', $val, true);?></td>
<?php else: ?>
                                            <td><?php echo form_checkbox("column".'[]', $val, false);?></td>
<?php endif; ?>
<?php endforeach; ?>
                                        </tr>
                                      </tbody>
                                    </table>
                                    </div>

                                    <h4>
                                    <?php
                                        $attr = array('class' => 'form-control');
                                        echo form_submit('submit', 'Изменить чеклист', $attr);
                                    ?>
                                    </h4>
                                    <?= form_close(); ?>
                                    <h4> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Вложения </h4>
                                    <div>
                                    <?php if(!empty($t['attachments'])):?>
                                    <?php if (count(explode(',', $t['attachments'])) > 1):?>
                                        <?php foreach (explode(',', $t['attachments']) as $key=>$atachment):?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$atachment; ?>"><h4>Скачать <?= $atachment; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/'.$key; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$t['attachments']; ?>"><h4>Скачать <?= $t['attachments']; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                           <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/0'; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    </div>
                                    <form enctype="multipart/form-data" action="http://medinnovations.ru/Tasks/provideAttachments/<?= $t['id']; ?>" method="POST" class="form-horizontal">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="700000000" />

                                    <div class="form-group">
                                        <div class="col-lg-7 col-lg-offset-1 col-xs-7">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-upload"></i></span>
                                                 <input  class="form-control"style='padding: 2px;' name="userfile" type="file"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-lg-offset-1 col-xs-3">
                                            <input type="submit" class="form-control" value="Отправить" />
                                        </div>

                                    </div>
                                    </form>
                                    <h4> <i class="fa fa-user-md" aria-hidden="true"></i> Снимки</h4>
                                    <?php if($t['complex'] === 'No'): ?>
                                    <span><a href="<?= base_url().'Tasks/trace/'.$t['id'].'/downloaded'; ?>"><?= $text['DOWNLOAD_TASK'];?></a></span>
                                    <?php else: ?>
                                    <?php $ids = explode(',', $t['complex']); ?>
                                    <span>
                                    <?php foreach($ids as $key=>$id): ?>
                                    <a href="<?= base_url().'Tasks/trace/'.$id.'/downloaded'; ?>"><?= 'Часть '. ($key+1); ?></a>
                                    <?php endforeach; ?>
                                    </span>
                                    <?php endif; ?>
                                    <h4> <i class="fa fa-history" aria-hidden="true"></i>  История</h4>
                                    <div class="container">

                                    <div class="row col-lg-6">

                                    <?php if (isset($tasksEventsFiltered[$t['id']])) :?>
                                    <?php foreach ($tasksEventsFiltered[$t['id']] as $te): ?>
                                            <div class="col-lg-12 col-xs-12 ">
                                                <div><?= $te['eventDescription']; ?></div>
                                            </div>
                                    <?php endforeach ?>
                                    <?php else:?>
                                        <h4>История отсутствует</h4>
                                    <?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                    </div>
                    </div>
                    <?php  $time = explode(" ", $t['timeCreated']);
                           $day = $time[0];
                           $h = $time[1];
                    ?>
                    <div class="container-fluid">
                        <div class="row check">
                            <div class="col-md-6 col-md-offset-5">
                                <input type="checkbox" class="ids" id="cbx<?= $t['id']; ?>" style="display:none" name="<?= $t['id']; ?>" />
                                <label for="cbx<?= $t['id']; ?>" class="toggle"><span></span>
                            </div>
                        </div>
                    </div>
                    <div class="card card-1" style="text-align: center;" data-toggle="modal" data-target="#modalTask<?=$t['id'];?>">
                        <span style="font-weight: 900;">[<?= $t['customer']; ?>]</span></br>
                        <span style="font-weight: 900;">[# <?= $t['id']; ?>]</span></br>
                        <span class="card-time" style="text-align: center; font-weight: 900;" data-time="<?= $t['timeCreated']; ?>"></span>
                    </div>

                    <?php endif; ?>
                <?php endforeach;?>
        </div>
        </div>


<div class="col-md-2">
                <div class="text-center">
                    <svg id="fillgauge5" height="150"></svg>
                </div>
                <div class="accordion"><a href="#" class="btn btn-sm animated-button victoria-three">Исполнено</a></div>
                <div class="panel">
<?php foreach($tasks as $t): ?>
<?php if ($t['status'] === 'Исполнено' && $t['partial'] !== 'Yes' && $t['customer'] !== 'basil137678' && $t['customer'] !== 'test_acc'): ?>
                        <div id="modalTask<?=$t['id'];?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabelTask<?=$t['id'];?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalLabelTask<?=$t['id'];?>">
                                <i class="fa fa-list-alt" aria-hidden="true"> </i>Заказ № <?=$t['id'];?></br>
                                <i class="fa fa-clock-o" aria-hidden="true"> </i><?=$t['timeCreated'];?>
                            </h4>
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <button class="form-control" onclick='postTrelloData("<?= $t['id']; ?>","<?= $emails[$t['id']]; ?>","<?= $t['timeCreated']; ?>", "<?= $t['organ']; ?>");'>Создать в Trello <i class="fa fa-trello" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-xs-4">
                        <h4 style="font-size: 12px"><i class="fa fa-file" aria-hidden="true"></i> Области исследования:</h4>
                    </div>

                    <div class="col-lg-8 col-xs-8">
<?php $organs = explode(',', $t['organ']); ?>
<?php foreach ($organs as $key => $o) :?>
                        <h4  style="font-size: 12px"><i class="fa fa-hashtag" aria-hidden="true"></i> <?= $o; ?></h4>
<?php endforeach; ?>
                    </div>
                    <div class="col-lg-4 col-xs-4"></div>
                </div>
                </div>
                <div class="row">
                <?php
                $attr = array('class' => 'form-horizontal',
                           'role' => 'form');
                echo form_open('Tasks/changeOrgan/'.$t['id'], $attr);
                ?>
                <div class="col-lg-6 col-lg-offset-1 col-xs-6">
                    <?php
                        $options = ['Голова и шейный отдел', 'Грудная клетка', 'Брюшная полость и забрюшинное пространство', 'Малый таз',  'Позвоночник',  'Кости свободных верхних и нижних конечностей'];
                        $excludedOrgans = $organs;
                        foreach($excludedOrgans as $key){
                            $keyToDelete = array_search($key, $options);

                            unset($options[$keyToDelete]);
                        }
                        foreach ($options as $key => $value) {
                            $options[$value] = $value;
                            unset($options[$key]);
                        }
                         $attr = array ('class'   => 'form-control');
                        if(count($options) !== 0) {
                            echo form_dropdown('organ', $options, array_keys($options)[0], $attr);
                        }
                        else {
                            echo "<h4>Уже выбраны все области</h4>";
                        }
                    ?>
                </div>
                <div class="col-lg-4 col-xs-4">
                <?php
                    $attr= array('class' => 'form-control');
                    if(count($options) !== 0) {
                       echo form_submit('submit', 'Добавить область', $attr);
                    }
                    else {
                    }
                ?>
                </div>
                <?= form_close(); ?>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-3 col-lg-offset-1 col-xs-3 col-xs-offset-1">
                    <?php if(is_null($t['cost'])): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет цены]</button>
                    <?php else: ?>
                        <button type="button" class="form-control good" style="cursor: default;">[<?= $t['cost']; ?>]</button>
                    <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-xs-3">
                    <?php if($t['paid'] === 'Yes'):?>
                        <button type="button" class="form-control good" style="cursor: default;">[Оплачен]</button>
                    <?php else: ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Не оплачен] </button>
                    <?php endif; ?>
                    </div>
                     <div class="col-lg-4 col-xs-4">
                    <?php if($t['worker'] === 'No'): ?>
                        <button type="button" class="form-control fail" style="cursor: default;">[Нет обработчика]</button>
                    <?php else: ?>
                       <button type="button" class="form-control good" style="cursor: default;">[<?= $t['worker']; ?>]</button>
                    <?php endif; ?>
                    </div>
                     <div class="col-lg-3 col-lg-offset-1 col-xs-3 col-xs-offset-1">
                        <button type="button" class="form-control good" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: default;">[<?= $t['customer']; ?>]</button>
                    </div>
                    <div class="col-lg-7 col-xs-7">
                       <input type="text" class="form-control" value="<?= $emails[$t['id']]; ?>" readonly="readonly" style="color: black;">
                    </div>
                </div>
                          <div class="modal-body">
                             <div class="container-fluid bd-example-row">
                                <div class="row">
                                    <?php if(is_null($t['description'])):?>
                                    <h4> <i class="fa fa-book" aria-hidden="true"></i> Описание отсутствует</h4>
                                    <?php else:?>
                                    <h4><?= $t['description']; ?></h4>
                                    <?php endif;?>
                                    <?php if(isset($t['status'])):?>
                                    <h4>
                                        <?php
                                        $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                        echo form_open('Tasks/changeStatus/'.$t['id'], $attr);
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <h4>Текущий статус: </h4>
                                            </div>
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-5">
                                                <h4><?= $t['status']; ?></h4>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-1 col-xs-4">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </div>

                                            <div class="col-lg-5 col-lg-offset-1 col-xs-5">
                                            <?php
                                             $options = ['Проверка заказа', 'Обрабатывается', 'Ждут заключения', 'Исполнено'];


                                            $keyToDelete = array_search($t['status'], $options);
                                            unset($options[$keyToDelete]);
                                            foreach ($options as $key => $value) {
                                                $options[$value] = $value;
                                                unset($options[$key]);
                                            }
                                                $attr = array ('class'   => 'form-control');
                                                echo form_dropdown('status', $options, array_keys($options)[0], $attr);
                                            ?>
                                            </div>

                                        </div>
                                        <?php
                                                $attr = array('class' => 'form-control');
                                                echo form_submit('submit', 'Изменить статус', $attr);
                                        ?>
                                        <?= form_close(); ?>
                                    </h4>
                                    <?php endif;?>
                                    <div class="scrollable-table">
                                    <table class="table table-striped table-header-rotated">
                                      <thead>
                                        <tr>
                                          <th></th>
                                          <th class="rotate-45" ><div><span>Снимки</span></div></th>
                                          <th class="rotate-45"><div><span>Анализы</span></div></th>
                                          <th class="rotate-45"><div><span>Анамнез</span></div></th>
                                          <th class="rotate-45"><div><span>Заключение</span></div></th>
                                          <th class="rotate-45"><div><span>Задача</span></div></th>
                                        </tr>
                                      </thead>
                                      <tbody>

                                        <tr>
                                          <th class="row-header"><h4><i class="fa fa-check-square-o" aria-hidden="true"></i> Чеклист</h4></th>
                                          <?php
                                            $attr = array('class' => 'form-horizontal',
                                                   'role' => 'form');
                                            echo form_open('Tasks/changeReceipts/'.$t['id'], $attr);
                                            ?>
                                          <?php foreach (explode(",", $t['receipts']) as $val=>$r):?>
                                              <?php if($r == 1): ?>
                                                <td><?php echo form_checkbox("column".'[]', $val, true);?></td>
                                              <?php else: ?>
                                                <td><?php echo form_checkbox("column".'[]', $val, false);?></td>
                                              <?php endif; ?>
                                          <?php endforeach; ?>
                                        </tr>
                                      </tbody>
                                    </table>
                                    </div>

                                    <h4>
                                    <?php
                                        $attr = array('class' => 'form-control');
                                        echo form_submit('submit', 'Изменить чеклист', $attr);
                                    ?>
                                    </h4>
                                    <?= form_close(); ?>
                                    <h4> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Вложения </h4>
                                    <div>
                                    <?php if(!empty($t['attachments'])):?>
                                    <?php if (count(explode(',', $t['attachments'])) > 1):?>
                                        <?php foreach (explode(',', $t['attachments']) as $key=>$atachment):?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$atachment; ?>"><h4>Скачать <?= $atachment; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/'.$key; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                        <div class="col-lg-8">
                                            <a href="<?= base_url().'assets/taskAtttachments/'.$t['id'].'/'.$t['attachments']; ?>"><h4>Скачать <?= $t['attachments']; ?></h4></a>
                                        </div>
                                        <div class="col-lg-4">
                                           <a href="<?= base_url().'/Tasks/deleteAttachments/'.$t['id'].'/0'; ?>"><h4><i class="fa fa-window-close" aria-hidden="true"></i></h4></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    </div>
                                    <form enctype="multipart/form-data" action="http://medinnovations.ru/Tasks/provideAttachments/<?= $t['id']; ?>" method="POST" class="form-horizontal">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="700000000" />

                                    <div class="form-group">
                                        <div class="col-lg-7 col-lg-offset-1 col-xs-7">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-upload"></i></span>
                                                 <input  class="form-control"style='padding: 2px;' name="userfile" type="file"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-lg-offset-1 col-xs-3">
                                            <input type="submit" class="form-control" value="Отправить" />
                                        </div>

                                    </div>
                                    </form>
                                    <h4> <i class="fa fa-user-md" aria-hidden="true"></i> Снимки</h4>
                                    <?php if($t['complex'] === 'No'): ?>
                                    <span><a href="<?= base_url().'Tasks/trace/'.$t['id'].'/downloaded'; ?>"><?= $text['DOWNLOAD_TASK'];?></a></span>
                                    <?php else: ?>
                                    <?php $ids = explode(',', $t['complex']); ?>
                                    <span>
                                    <?php foreach($ids as $key=>$id): ?>
                                    <a href="<?= base_url().'Tasks/trace/'.$id.'/downloaded'; ?>"><?= 'Часть '. ($key+1); ?></a>
                                    <?php endforeach; ?>
                                    </span>
                                    <?php endif; ?>
                                    <h4> <i class="fa fa-history" aria-hidden="true"></i>  История</h4>
                                    <div class="container">

                                    <div class="row col-lg-6">

                                    <?php if (isset($tasksEventsFiltered[$t['id']])) :?>
                                    <?php foreach ($tasksEventsFiltered[$t['id']] as $te): ?>
                                            <div class="col-lg-12 col-xs-12 ">
                                                <div><?= $te['eventDescription']; ?></div>
                                            </div>
                                    <?php endforeach ?>
                                    <?php else:?>
                                        <h4>История отсутствует</h4>
                                    <?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                    </div>
                    </div>
                    <?php  $time = explode(" ", $t['timeCreated']);
                           $day = $time[0];
                           $h = $time[1];
                    ?>
                    <div class="container-fluid">
                        <div class="row check">
                            <div class="col-md-6 col-md-offset-5">
                                <input type="checkbox" class="ids" id="cbx<?= $t['id']; ?>" style="display:none" name="<?= $t['id']; ?>" />
                                <label for="cbx<?= $t['id']; ?>" class="toggle"><span></span>
                            </div>
                        </div>
                    </div>
                    <div class="card card-1" style="text-align: center;" data-toggle="modal" data-target="#modalTask<?=$t['id'];?>">
                        <span style="font-weight: 900;">[<?= $t['customer']; ?>]</span></br>
                        <span style="font-weight: 900;">[# <?= $t['id']; ?>]</span></br>
                        <span class="card-time" style="text-align: center; font-weight: 900;" data-time="<?= $t['timeCreated']; ?>"></span>
                    </div>

                    <?php endif; ?>
                <?php endforeach;?>
        </div>
        </div>

        <div class="col-md-4">
            <div class="col-md-4">
                <a href="#today" class="filter btn btn-sm animated-button victoria-one" id="#today">Сегодня</a>
            </div>
            <div class="col-md-4">
                <a href="#week" class="filter btn btn-sm animated-button victoria-one" id="#week">Неделя</a>
            </div>
            <div class="col-md-4">
                <a href="#month" class="filter btn btn-sm animated-button victoria-one" id="#month">Месяц</a>
            </div>
            <div class="col-md-2 mt-10 text-center"><span class="badge badge-secondary">С:</span></div>
            <div class="col-md-10 mt-10">
                <input type="date" class="form-control" id="from">
            </div>
            <div class="col-md-2 mt-10 text-center"><span class="badge badge-secondary">ДО:</span></div>
            <div class="col-md-10 mt-10">
                <input  type="date" class="form-control" id="to">
            </div>
                <!--<div class="col-md-2  mt-10">
                    <svg id="fillgauge2" height="100"></svg>
                </div>
                <div class="col-md-2 mt-10">
                    
                </div>
                <div class="col-md-2 mt-10">
                    <svg id="fillgauge4" height="100"></svg>
                </div>
                <div class="col-md-2 mt-10">
                    <svg id="fillgauge5" height="100"></svg>
                </div>-->
        <div class="col-md-4 col-md-offset-4 mt-10">
              <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; Изменения</span>
        </div>
              <div class="sidenav" id="mySidenav">

                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
<?php foreach ($tasksEvents as $te): ?>
                <p id="event-description" style="padding: 5px;">Задание № <?= $te['taskId']; ?>.<?= $te['eventDescription']; ?></p>
<?php endforeach; ?>
        </div>
</div>
</div>
<script type="text/javascript">
    moment.locale("ru");
    var cards = document.querySelectorAll('.card-time');
    for (var i = 0, l = cards.length; i < l; i++) {
        cards[i].innerHTML += moment(cards[i].dataset.time).format("DD MMMM, YYYY")+"</br>";
        cards[i].innerHTML += moment(cards[i].dataset.time).format("HH:mm:ss")+"</br>";
    }
</script>
<script>
var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("activation");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}
</script>
<script>
var config1 = liquidFillGaugeDefaultSettings();
    config1.circleThickness = 0.2;
    config1.textVertPosition = 0.2;
    config1.waveAnimateTime = 1000;
//Waiting, Processing, NotFinished

var value1 = <?php echo $statistics["Inited"] ;?>*100,
    value2 = <?php echo $statistics["Processing"] ;?>*100,
    value3 = <?php echo $statistics["Waiting"] ;?>*100,
    value4 = <?php echo $statistics["Finished"] ;?>*100;

    var gauge2= loadLiquidFillGauge("fillgauge2", value1, config1);
    var gauge3 = loadLiquidFillGauge("fillgauge3", value2, config1);
    var gauge4 = loadLiquidFillGauge("fillgauge4", value3, config1);
    var gauge5 = loadLiquidFillGauge("fillgauge5", value4, config1);
</script>
<script type="text/javascript">
function openNav() {
  document.getElementById("mySidenav").style.width = "850px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}

</script>
