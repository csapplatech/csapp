<!DOCTYPE>
<html>
    <head>
        
        <link href = "<?php echo base_url('css/advising.css'); ?>" rel ="stylesheet" >
        <link rel="stylesheet" href="<?php echo base_url('css/print.css'); ?>" type="text/css" media="print" />

        <script type="text/javascript" src="<?php echo base_url('js/jquery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>   
        <script type="text/javascript" src="<?php echo base_url('js/advising.js'); ?>"></script>
    
        <title>Advising Page</title>
        <script>
            var rootURL = '<?php echo URL; ?>';
        </script>
		<style>
			
			.clickme, .toggleMe, .clicky {
				cursor: pointer;
			}
			
		</style>
    </head>
    <body id="background" ><div id="main">
        <h1 id="head">Advising Page</h1>
        
        
        <div id="advise" class="print">
            <table>
                <tr>
                    <th><img src="<?php echo base_url('image/latech.gif'); ?>" alt="Tech Logo" class="logo"></th>
                    <th><table class='noborder'>
                        <tr><th class="noborder">Louisiana Tech University</th></tr>
                        <tr><th class="noborder">ADVISING FORM</th></tr>
                        <tr><th class="noborder">Quarter <u><?php
                        $quarter = new academic_quarter_model();
                        $quarter->loadPropertiesFromPrimaryKey($quarter_id);
                        echo $quarter->getName() . " " . $quarter->getYear();?></u></th></tr>
                </table> </th>
                    <th><img src="<?php echo base_url('image/latech.gif'); ?>" alt="Tech Logo" class="logo"></th>
                </tr>
            </table>
            <table>
                <tr id='namecwid'>
                    <th class="noborder" style="text-align:left; padding-left: 8%">Student Name: <u><?php echo $student_name; ?></u></th>
            <th class="noborder" style="text-align:right; padding-right: 8%">CWID: <u><?php echo $cwid; ?></u></th>
                </tr>
            </table>
            <table id='target'>
              <tr>
                <th style="width: 14%">Subj-Cor-Sec</th>
                <th >Title</th>
                <th style="width: 7%">Call #</th>               
                <th style="width: 6%">Hrs</th>
                <th style="width: 18%">Special Signature</th> 
                <th style="width: 25%">Class Times</th>
              </tr>
            </table>
            
            <div id='alt'>
                <table id='altTable'>
                <tr>
                    <th colspan="6"><strong><center>ALTERNATIVE COURSE CHOICES</center></strong></th>
                </tr>
                <tr>
                  <th style="width: 14%">Subj-Cor-Sec</th>
                  <th >Title</th>
                  <th style="width: 7%">Call #</th>               
                  <th style="width: 6%">Hrs</th>
                  <th style="width: 18%">Special Signature</th> 
                  <th style="width: 25%">Class Times</th>
                </tr>

                </table>
            </div>
            <table ID='sig'>
                <tr>
                   <td id='bottomcell'>_____________________________________________</td><td id='bottomcell'>_____________________________________________</td>
                </tr>
                <tr>
                    <td id='topcell'>STUDENT SIGNATURE/DATE</td><td id='topcell'>ADVISOR SIGNATURE/DATE</td>
                </tr>
                <tr>
                    <td id='bottomcell'>_____________________________________________</td><td id='bottomcell'>_____________________________________________</td>
                </tr>
                <tr>                                
                    <td id='topcell'>DEAN'S SIGNATURE/DATE</td><td id='topcell'>VP FOR ACADEMIC AFFAIRS SIGNATURE/DATE</td>
                </tr>
            </table>
        </div>
        
        <div id="courses">
            <h4 class="class_headers" style='margin-top:.5%'>Suggested Classes</h4>
             <ul>
                <?php 
                $id=0;
                foreach ($recommended->getSubjects() as $sub)
                {
                    echo "<li class='clickMe'>". $sub->getName()." ". $sub->getTitle() ." </li>" ;
                    echo "<ul class=\"toggleMe\" style=\"display:none\">";
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe' style=\"font-size: 90% \"><span title=\"".$cor->getHours()." Credit Hours\">". $cor->getName() . " ". $cor->getTitle(). "</span></li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                               echo "<li style=\"font-size: 90% \"><span title=\"Professor: ". $sec->getInstructorName()."\">". $sec->getSectionName(). "  ". $sec->getCourseSectionTimesAsString() ."<button type=\"button\" class='button' id='".$id."'>Add</button></span></li>" ; echo "<div id='hidden'>".
                                "<span id=\"a".$id."\">". $sub->getName(). "-" . $cor->getName() . "-".$sec->getSectionName(). "</span>".
                                "<span id=\"b".$id."\">". $cor->getTitle(). "</span>".
                                "<span id=\"c".$id."\">".$sec->getCallNumber(). "</span>".
                                "<span id=\"d".$id."\">".$sec->getHours(). "</span>".
                                "<span id=\"e".$id."\">". $sec->getCourseSectionTimesAsString() ."</span>".
                                     "</div>";
                               
                                $id++;
                            }
                        echo "</ul>";
                    }
                }
                
                echo "</ul>";
                ?>
             </ul>    
            <h4 class="class_headers" style='margin-top:.5%'>Select Subject</h4>
             <ul>
                <?php 
                $id=1000;
                foreach ($all_courses->getSubjects() as $sub)
                {
                    echo "<li class='clickMe'>". $sub->getName()." ". $sub->getTitle() ." </li>" ;
                    echo "<ul class=\"toggleMe\" style=\"display:none\">";
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe' style=\"font-size: 90% \"><span title=\"".$cor->getHours()." Credit Hours\">". $cor->getName() . " ". $cor->getTitle(). "</span></li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                               echo "<li style=\"font-size: 90% \"><span title=\"Professor: ". $sec->getInstructorName()."\">". $sec->getSectionName(). "  ". $sec->getCourseSectionTimesAsString() ."<button type=\"button\" class='button' id='".$id."'>Add</button></span></li>" ; echo "<div id='hidden'>".
                                "<span id=\"a".$id."\">". $sub->getName(). "-" . $cor->getName() . "-".$sec->getSectionName(). "</span>".
                                "<span id=\"b".$id."\">". $cor->getTitle(). "</span>".
                                "<span id=\"c".$id."\">".$sec->getCallNumber(). "</span>".
                                "<span id=\"d".$id."\">".$sec->getHours(). "</span>".
                                "<span id=\"e".$id."\">". $sec->getCourseSectionTimesAsString() ."</span>".
                                     "</div>";
                               if ($form !== false)
                               {
                                foreach ($form->getPrefferedCourseSections() as $pref)
                                {
                                    if($pref->getCallNumber() === $sec->getCallNumber())
                                    {
                                        echo '<script>addMain('.$id.');</script>';
                                    }
                                }
                                foreach ($form->getAlternateCourseSections() as $alt)
                                {
                                    if($alt->getCallNumber() === $sec->getCallNumber())
                                    {
                                        echo '<script>addALT('.$id.');</script>';
                                    }
                                }
                               }
                                $id++;
                            }
                        echo "</ul>";
                    }
                    echo "</ul>";
                }
                ?>
             </ul>  
        </div>
        
        <div id="PB">
            <input type="button" value=" Print Advising Form" onclick="window.print();return false;" />
            <button type="button" id="save">Save</button>
            <button><a style="text-decoration: none; color: black"  href="<?php echo site_url('Mainpage/index'); ?>">Home</a></button>
            <button><a style="text-decoration: none; color: black" href="<?php echo site_url('Login/logout'); ?>">Logout</a></button>
        </div>
</div></body>
</html>