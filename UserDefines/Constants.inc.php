<?php
//Misc
define('PGEE_APP_NAME','PGEE');
//Database related
define('PGEE_DB_USERNAME',getenv('DB_USER'));
define('PGEE_DB_PASSWORD',file_get_contents(getenv('PASSWORD_FILE_PATH')));
define('PGEE_DB_NAME',getenv('DB_NAME'));
define('PGEE_DB_SERVER',getenv('DB_HOST'));
define('PGEE_DB_PORT',''); //Should start w/ ":" if necessary
//Events related
define('PGEE_EVENT_RECENT_THRESHOLD',14);
//^ Nb. de jours jusqu'à qu'un évèn. ne soit plus considéré comme "récent" (à partir de sa création)
define('PGEE_EVENT_PER_PAGE',8);