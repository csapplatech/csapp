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
            <div style="height:450px; width:275px; margin: 0 auto; overflow-y: auto">
                <p style="color:black"> Please Select the student you wish to view</p>
                <ul>
                <?php
                
                foreach ($students as $stud)
                {
                    echo  "<li style='color: black; text-align:left' class='clickme' id='".$stud->getUserID()."'>" . $stud->getName() . "</li>";
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
