<!DOCTYPE>
<html>
    <head>
        
        <link href = "../../css/advising.css" rel ="stylesheet" >
        <link href = "../../css/ltp.css" rel ="stylesheet" >
        
        <link rel="stylesheet" href="../../css/print.css" type="text/css" media="print" />
    
        <title>4 Year Plan</title>
        
    </head>
    <body id="background" >
        <div id="main">
            <h1 id="head">4 Year Plan</h1>

            <div id="courses">
                <!-- This is the red box where the curriculum will be listed !-->
                <h4 class="class_headers" style='margin-top:.5%'>Curriculum</h4>
                <table>  
                <tr><td>CSC 220 </td><td>Data Structures</td><td class="AR">3</td></tr>
                </table>
                
            </div>

            <div id="advise" class="print" style="overflow: auto">
                <div style="width:1200px">
                <!-- This is where the 4 year plan will be !-->
                    <table>
                        <tr>
                            <th>Fall 20xx</th>
                            <th> Winter 20xx</th>
                            <th> Spring 20xx</th>  
                            <th>Summer 20xx</th>
                        </tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                    </table><table>
                        <tr>
                            <th>Fall 20xx</th>
                            <th> Winter 20xx</th>
                            <th> Spring 20xx</th>  
                            <th>Summer 20xx</th>
                        </tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td></td></tr>
                    </table>
                </div>
            </div>

            <div id="PB">
                <input type="button" value=" Print Advising Form" onclick="window.print();return false;" />
                <button type="button" id="AY">Add Year</button>
                <button type="button" id="save">Save</button>
                <button><a style="text-decoration: none; color: black"  href="<?php echo site_url('Mainpage/index'); ?>">Home</a></button>
            </div>
        </div>
    </body>
</html>