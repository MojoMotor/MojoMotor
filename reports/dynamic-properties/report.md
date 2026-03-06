# Dynamic property audit report

- Generated: 2026-03-06 03:19:14 UTC
- PHP: 8.5.1
- Scanned files/classes: 211/116
- Bridge classes: 2
- Candidate classes for explicit property refactor: 36

## Strategy

- Applied bridge: `#[AllowDynamicProperties]` on `CI_Controller` and `CI_Model`.
- Next phase: prioritize app-owned classes with undeclared writes and add explicit properties.

## Top candidates

- CI_Image_lib (system/codeigniter/system/libraries/Image_lib.php): undeclared-writes=1, variable-property-writes=2
- CI_Input (system/codeigniter/system/core/Input.php): undeclared-writes=2, variable-property-writes=0
- CI_DB_mysql_driver (system/codeigniter/system/database/drivers/mysql/mysql_driver.php): undeclared-writes=2, variable-property-writes=0
- CI_DB_mysqli_driver (system/codeigniter/system/database/drivers/mysqli/mysqli_driver.php): undeclared-writes=2, variable-property-writes=0
- CI_DB_postgre_driver (system/codeigniter/system/database/drivers/postgre/postgre_driver.php): undeclared-writes=2, variable-property-writes=0
- CI_Calendar (system/codeigniter/system/libraries/Calendar.php): undeclared-writes=1, variable-property-writes=1
- CI_Javascript (system/codeigniter/system/libraries/Javascript.php): undeclared-writes=2, variable-property-writes=0
- CI_Pagination (system/codeigniter/system/libraries/Pagination.php): undeclared-writes=1, variable-property-writes=1
- CI_Upload (system/codeigniter/system/libraries/Upload.php): undeclared-writes=0, variable-property-writes=2
- CI_Xmlrpcs (system/codeigniter/system/libraries/Xmlrpcs.php): undeclared-writes=2, variable-property-writes=0
- CI_Loader (system/codeigniter/system/core/Loader.php): undeclared-writes=0, variable-property-writes=1
- CI_Router (system/codeigniter/system/core/Router.php): undeclared-writes=1, variable-property-writes=0
- CI_URI (system/codeigniter/system/core/URI.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_driver (system/codeigniter/system/database/DB_driver.php): undeclared-writes=0, variable-property-writes=1
- CI_DB_forge (system/codeigniter/system/database/DB_forge.php): undeclared-writes=1, variable-property-writes=0
