<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Create New User</title>

        <style type="text/css">

            ::selection { background-color: #E13300; color: white; }
            ::-moz-selection { background-color: #E13300; color: white; }

            body {
                background-color: #fff;
                margin: 40px;
                font: 13px/20px normal Helvetica, Arial, sans-serif;
                color: #4F5155;
            }

            a {
                color: #003399;
                background-color: transparent;
                font-weight: normal;
            }

            h1 {
                color: #444;
                background-color: transparent;
                border-bottom: 1px solid #D0D0D0;
                font-size: 19px;
                font-weight: normal;
                margin: 0 0 14px 0;
                padding: 14px 15px 10px 15px;
            }

            code {
                font-family: Consolas, Monaco, Courier New, Courier, monospace;
                font-size: 12px;
                background-color: #f9f9f9;
                border: 1px solid #D0D0D0;
                color: #002166;
                display: block;
                margin: 14px 0 14px 0;
                padding: 12px 10px 12px 10px;
            }

            #body {
                margin: 0 15px 0 15px;
            }

            p.footer {
                text-align: right;
                font-size: 11px;
                border-top: 1px solid #D0D0D0;
                line-height: 32px;
                padding: 0 10px 0 10px;
                margin: 20px 0 0 0;
            }

            #container {
                margin: 10px;
                border: 1px solid #D0D0D0;
                box-shadow: 0 0 8px #D0D0D0;
            }
        </style>
    </head>

    <body>
        <form action="initUser" method="POST" >
            <p><b>Please fill out user info.</b></p><br/>
            <table>
                <tr><p>New Email Address: <input type="text" name="emailAddress"></p></tr>
                <tr><p>New Password: <input type="password" name="password"></p></tr>
                <tr><p>Confirm New Password: <input type="password" name="password"></p></tr>
                <tr><p>New User Name: <input type="text" name="userName"></p></tr>
            </table>
            <p><b>Please select user Roles.</b></p><br/>

            <input type="checkbox" name="4" id="4"/>Student<br />
            <input type="checkbox" name="3" id="3"/>Advisor<br />
            <input type="checkbox" name="2" id="2"/>Program Chair<br />
            <input type="checkbox" name="1" id="1"/>Administrator<br />
            <br />
            <tr><input type="submit" value="NEXT"></tr>

        </form>
    </body>
</html>
</body>
