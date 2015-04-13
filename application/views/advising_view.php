<!DOCTYPE>
<html>
    <head>
        <link href = "css/advising.css" rel ="stylesheet">
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
        <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
        <!--<link href="<?php echo base_url('css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">!-->
        <script type="text/javascript" src="<?php echo base_url('js/jquery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>   
        <script type="text/javascript" src="<?php echo base_url('js/advising.js'); ?>"></script>
    
        <title>Advising Page</title>
    </head>
    <body id="background">

        <h1 id="head">Advising Page</h1>
        
        <div id="courses">
            <h4 class="class_headers" style='margin-top:.5%'>Suggested Classes</h4>
             <ul>
                <?php 
                $id=0;
                foreach ($courses['Recommended']->getSubjects() as $sub)
                {
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe'>". $sub->getName(). "-" . $cor->getName() . " ". $cor->getTitle(). "(".$cor->getHours()." hours)</li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                                echo "<li>". $sec->getSectionName(). " Times:WIP <button type=\"button\" class='button' id='".$id."'>Add</button></li>" ;
                                echo "<div id='hidden'>".
                                "<span id=\"a".$id."\">". $sub->getName(). "-" . $cor->getName() . "-".$sec->getSectionName(). "</span>".
                                "<span id=\"b".$id."\">". $cor->getTitle(). "</span>".
                                "<span id=\"c".$id."\">".$sec->getCallNumber(). "</span>".
                                "<span id=\"d".$id."\">".$sec->getHours(). "</span>".
                                "<span id=\"e".$id."\">WIP</span>".
                                     "</div>";
                                $id++;
                            }
                        echo "</ul>";
                    }
                }
                ?>
             </ul>

            <h4 class="class_headers">Classes Taken</h4>
            <ul>
                <?php 
                $id=100;
                foreach ($courses['Passed']->getSubjects() as $sub)
                {
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe'>". $sub->getName(). "-" . $cor->getName() . " ". $cor->getTitle(). "(".$cor->getHours()." hours)</li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                                echo "<li>". $sec->getSectionName(). " Times:WIP <button type=\"button\" class='button' id='".$id."'>Add</button></li>" ;
                                echo "<div id='hidden'>".
                                "<span id=\"a".$id."\">". $sub->getName(). "-" . $cor->getName() . "-".$sec->getSectionName(). "</span>".
                                "<span id=\"b".$id."\">". $cor->getTitle(). "</span>".
                                "<span id=\"c".$id."\">".$sec->getCallNumber(). "</span>".
                                "<span id=\"d".$id."\">".$sec->getHours(). "</span>".
                                "<span id=\"e".$id."\">WIP</span>".
                                     "</div>";
                                $id++;
                            }
                        echo "</ul>";
                    }
                }
                ?>
             </ul>
          
            <h4 class="class_headers">Requirements Not Met</h4>
            <ul>
                
                <?php 
                $id=200;
                foreach ($courses['Signature']->getSubjects() as $sub)
                {
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe'>". $sub->getName(). "-" . $cor->getName() . " ". $cor->getTitle(). "(".$cor->getHours()." hours)</li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                                echo "<li>". $sec->getSectionName(). " Times:WIP <button type=\"button\" class='button' id='".$id."'>Add</button></li>" ;
                                echo "<div id='hidden'>".
                                "<span id=\"a".$id."\">". $sub->getName(). "-" . $cor->getName() . "-".$sec->getSectionName(). "</span>".
                                "<span id=\"b".$id."\">". $cor->getTitle(). "</span>".
                                "<span id=\"c".$id."\">".$sec->getCallNumber(). "</span>".
                                "<span id=\"d".$id."\">".$sec->getHours(). "</span>".
                                "<span id=\"e".$id."\">WIP</span>".
                                     "</div>";
                                $id++;
                            }
                        echo "</ul>";
                    }
                }
                ?>
             </ul>
            <br>
            <p id='temp'><em><u>Link to Racing form (WIP)</u></em></p>
               
        </div>
        
        <div id="advise" class="print">
            <table>
                <tr>
                    <th><img src="image/latech.gif" alt="Tech Logo" class="logo"></th>
                    <th><table class='noborder'>
                        <tr><th class="noborder">Louisiana Tech University</th></tr>
                        <tr><th class="noborder">ADVISING FORM</th></tr>
                        <tr><th class="noborder">Quarter <u><?php
                        $quarter = new academic_quarter_model();
                        $quarter->loadPropertiesFromPrimaryKey($quarter_id);
                        echo $quarter->getName() . " " . $quarter->getYear();?></u></th></tr>
                </table> </th>
                    <th><img src="image/latech.gif" alt="Tech Logo" class="logo"></th>
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
                <th style="width: 21%">Subject/Course/Section</th>
                <th style="width: 17%">Title</th>
                <th style="width: 9%">Call #</th>               
                <th style="width: 7%">Hours</th>
                <th style="width: 25%">Special Signature</th> 
                <th style="width: 21%">Class Times</th>
              </tr>
            </table>
            
            <div id='alt'>
                <table id='altTable'>
                  <tr>
                      <th colspan="6"><strong><center>ALTERNATIVE COURSE CHOICES</center></strong></th>
                  </tr>
                    <tr>
                      <th style="width: 21%">Subject/Course/Section</th>
                      <th style="width: 17%">Title</th>
                      <th style="width: 9%">Call #</th>               
                      <th style="width: 7%">Hours</th>
                      <th style="width: 25%">Special Signature</th> 
                      <th style="width: 21%">Class Times</th>
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
        
        <div id="PB"><form><input type="button" value=" Print Advising Form"
                          onclick="window.print();return false;" /></form></div>
 
    
</body>
</html>