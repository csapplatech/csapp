        <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="<?php echo site_url('Mainpage/index'); ?>">CSAPP</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                  <?php
                  if (isset($user))
                  {
                    if ($user->isAdvisor())
                        echo "<li><a href='" .site_url('Mainpage/advisor'). "'>Advisor Home</a></li>";
                    if ($user->isStudent())
                        echo "<li><a href='" .site_url('Mainpage/student'). "'>Student Home</a></li>";
                    if ($user->isAdmin())
                        echo "<li><a href='" .site_url('Mainpage/admin'). "'>Admin Home</a></li>";
                    if ($user->isProgramChair())
                        echo "<li><a href='" .site_url('Mainpage/programChair'). "'>Program Chair Home</a></li>";
                  }
                  if (isset ($additionalButtons))
                  {
                      $keys = array_keys($additionalButtons);
                      foreach ($keys as $button)
                      {
                          echo "<li><a href='" .site_url($additionalButtons[$button]). "'>".$button."</a></li>";
                      }
                      unset($button);
                  }
                  ?>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('Login/logout'); ?>">Logout</a></li>
              </ul>
            </div>
           </div>
        </nav>