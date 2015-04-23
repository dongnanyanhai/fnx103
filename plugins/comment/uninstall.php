<?php
if (!defined('IN_FINECMS')) exit('No permission resources');

return array(
    "DROP TABLE IF EXISTS `{prefix}comment`;",
    "DROP TABLE IF EXISTS `{prefix}comment_data`;"
);