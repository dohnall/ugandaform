<?php
unset($session->user_id);
setcookie('USERTOKEN', '', time() - 3600);
Common::redirect(ROOT.'admin');
