<!DOCTYPE>
<html>
    <head>
        <link href = "css/advising.css" rel ="stylesheet">
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
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
                        echo "<li class='clickMe'>". $sub->getName(). " " . $cor->getName() . " ". $cor->getTitle(). "</li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                                echo "<li>". $sec->getSectionName(). "<button type=\"button\" class='button' id='".$id."'>Add</button></li>" ;
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
                $id=200;
                foreach ($courses['Passed']->getSubjects() as $sub)
                {
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe'>". $sub->getName(). " " . $cor->getName() . " ". $cor->getTitle(). "</li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                                echo "<li>". $sec->getSectionName(). "<button type=\"button\" class='button' id='".$id."'>Add</button></li>" ;
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
                $id=300;
                foreach ($courses['Signature']->getSubjects() as $sub)
                {
                    foreach($sub->getCourses() as $cor)
                    {
                        echo "<li class='clickMe'>". $sub->getName(). " " . $cor->getName() . " ". $cor->getTitle(). "</li>" ;
                        echo "<ul class=\"toggleMe\" style=\"display:none\">";
                            foreach($cor->getSections() as $sec)
                            {
                                echo "<li>". $sec->getSectionName(). "<button type=\"button\" class='button' id='".$id."'>Add</button></li>" ;
                                $id++;
                            }
                        echo "</ul>";
                    }
                }
                ?>
             </ul>
            <br>
            <p id='temp'><em><u>Link to Racing form</u></em></p>
               
        </div>
        
        <div id="advise" class="print">
            
            <!--
                <?/*php echo site_url('login/check'); */?>
            <?php
            
                /*echo '<ul>';
                foreach($courses as $cs){
                 echo '<li>'.$cs->tostring().'</li>';
                }
                echo '</ul>';
                 echo '<ul>';
                /*foreach($sample as $cs){
                 echo '<li>'.$cs->tostring().'</li>';
                }*/ 
                //echo '</ul>';
            ?> !-->
            <table>
                <tr>
                    <th><img src="image/latech.gif" alt="Tech Logo" class="logo"></th>
                    <th><table class='noborder'>
                        <tr><th class="noborder">Louisiana Tech University</th></tr>
                        <tr><th class="noborder">ADVISING FORM</th></tr>
                        <tr><th class="noborder">Quarter <u>???????</u></th></tr>
                </table> </th>
                    <th><img src="image/latech.gif" alt="Tech Logo" class="logo"></th>
                </tr>
            </table>
            <table>
                <tr id='namecwid'>
                    <th class="noborder" style="text-align:left; padding-left: 8%">Student Name: <u>?????????</u></th>
            <th class="noborder" style="text-align:right; padding-right: 8%">CWID#<u>????????????</u></th>
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