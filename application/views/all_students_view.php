<html>
    <head>
        
        <script type="text/javascript" src="<?php echo base_url('js/jquery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>   
        <script type="text/javascript" src="<?php echo base_url('js/studentlist.js'); ?>"></script>
        <title>CSAPP</title>

        <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
    </head>
    <body style="padding-top: 60px">
          <?php $this->load->view('Templates/navbar'); ?>
        <div>
            <img src="../../image/latech.gif" alt="Tech Logo" class="logo" style="width:450px; height:450px; position:relative; display:inline-block">
            
            <div style="height:400px; width:400px; margin-left: 100px; position:absolute; display:inline-block; margin-top: 10px ">
                <p> Please Select the student you wish to view</p>
                <ul>
                <?php
                
                foreach ($students as $stud)
                {
                    echo  "<li style='color: #000;' class='clickme' id='".$stud->getUserID()."'>" . $stud->getName() . "</li>";
                }
                
                ?>
                   
                
                </ul>
            </div>
        </div>
        <footer>
            <font color ="white"</font>
            <p>Ruston, LA 2015</p> 
            <p>Louisiana Tech University</p>
            <p>This is a WIP</p>
        </footer>
        
    </body>
</html>
