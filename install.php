<?php
/**~. ChatFrosting 0.1 (c) 2014 Garrett R. Morris .~**->>             
 *Licensed Under the MIT License : http://www.opensource.org/licenses/mit-license.php
 * Removing this copyright notice is a violation of the license.
 ***************************************/
 
require_once __DIR__ . '/../models/config.php';

global $mysqli;

$create = 
"CREATE TABLE IF NOT EXISTS `".$db_table_prefix."_chat_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `color` varchar(6) NOT NULL,
  `hidden` int(1) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";

$welcome = "INSERT INTO `".$db_table_prefix."_chat_msg` (`id`, `username`, `message`, `color`, `hidden`, `timestamp`) VALUES
(1, 'System', 'Welcome to Chat Frosting!', '0404B4', 0, 1397909660)";

$query = $mysqli->query($create);
$query->close();
$query = $mysqli->query($welcome);