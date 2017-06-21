<?php
## SMS
## sons or brother
$bros_sql = "SELECT id, status FROM student_data WHERE parent_id=$parent_id";

if(isset($_GET['std_id'])){ $bros_sql .= " AND id !=".$_GET['std_id'];}
$brothers = do_query_array($bros_sql, MySql_Database);

?>
<table class="tablesorter">
    <thead>
        <tr>
            <th width="20">&nbsp;</th>
            <th><?php echo $lang['name'];?></th>
            <th><?php echo $lang['class'];?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($brothers as $bro){
				$brother = new Students($bro->id);
                if($brother->status == 1 ){
					$class = $brother->getClass();
                    $bro_class = $class->getName();
                } elseif($brother->status == 5 ){
                    $bro_class =  $lang['gruaduated'];
                } elseif($brother->status == 0 ){
                    $bro_class =  $lang['desinscriped'];
                } elseif($brother->status == 2 ){
                    $bro_class =  $lang['waiting_list'];
                } else {
                    $bro_class = '';
                }
                echo '<tr>
                    <td>
						<button module="students" action="openStudent" std_id="'.$bro->id.'" class="ui-state-default hoverable circle_button" >
							<span class="ui-icon ui-icon-person"></a>
						</button>
					</td>
                    <td>'.$brother->getName().'</td>
                    <td>'.$bro_class.'</td>
                </tr>';
            }
        ?>
    </tbody>                        
</table>
