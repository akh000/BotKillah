<?php
session_start();

include_once('config.php');
include_once('db/db.php');

// Conecto con Twitter
require_once ('codebird-php.php');
\Codebird\Codebird::setConsumerKey($tw_consumer, $tw_secret); // static, see 'Using multiple Codebird instances'

$cb = \Codebird\Codebird::getInstance();
$cb->setToken($tw_token_a, $tw_token_b);

include('bot.php');


if (isset($_GET["id_str"])) {$id_str = $_GET["id_str"];}
if (isset($_GET["screen_name"])) {$screen_name = $_GET["screen_name"];}
if ( ! $screen_name ) {
    $usuario = get_random_bot();

    $id_str = $usuario['id_str'];
    $screen_name = $usuario['screen_name'];
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>BotKillah!<?= $screen_name ? ' - '.$screen_name : '' ?></title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/3.2.1/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js" ></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script>
          $(document).ready(function(){

            var next = function() {
              if ( $('#id_str').val() != "") {
                $('#next').click();
              }

            }

            var found = $('.alert').text().search(/Rate limit/i);

            if ( found != -1 ) {
              setTimeout(function(){
                
                location.reload();

              }, 60000 * 15);

            } else {
              next();
            }

          });
        </script>
    </head>
    <body>
 <?php

        mark_as_viewed($id_str);
        if ($screen_name <> "")
        {
        $users = array_merge(get_followers($screen_name),get_friends($screen_name));
        
        
        $users = array_unique($users,SORT_REGULAR);

        foreach ( $users as $f ) {
            echo  '<a href="http://twitter.com/'.$f->screen_name.'" target="_blank" >'.$f->screen_name.'</a> <a href="?id_str='.$f->id_str.'&amp;screen_name='.$f->screen_name.'"><i class="icon-chevron-sign-right"></i></a><br />';

            // agrego condición de fecha para filtrar más rápido
            if (strpos($f->created_at, '2014') <> '0')
            {
                save_if_not_exist($f);
                save_relation($id_str,$f->id_str);
            }
        }
        }        
        $next = get_random_bot();
        ?>
        <form action="index.php" method="GET">
            
            <input id="id_str" type="hidden" name="id_str" value="<?= $next['id_str'] ?>">
            <input id="screen_name" type="hidden" name="screen_name" value="<?= $next['screen_name'] ?>">
            
            <input id="next" type="submit" value="Siguiente: <?= $next['screen_name'] ? $next['screen_name'] : 'NONE' ?>">
        
    </body>
</html>
