# Dynamic property audit report

- Generated: 2026-03-06 17:12:21 UTC
- PHP: 8.5.1
- Scanned files/classes: 212/116
- Bridge classes: 2
- Candidate classes for explicit property refactor: 29

## Strategy

- Applied bridge: `#[AllowDynamicProperties]` on `CI_Controller` and `CI_Model`.
- Next phase: prioritize app-owned classes with undeclared writes and add explicit properties.

## Top candidates

- CI_Image_lib (system/codeigniter/system/libraries/Image_lib.php): undeclared-writes=1, variable-property-writes=2
- CI_DB_mysql_driver (system/codeigniter/system/database/drivers/mysql/mysql_driver.php): undeclared-writes=2, variable-property-writes=0
- CI_DB_mysqli_driver (system/codeigniter/system/database/drivers/mysqli/mysqli_driver.php): undeclared-writes=2, variable-property-writes=0
- CI_DB_postgre_driver (system/codeigniter/system/database/drivers/postgre/postgre_driver.php): undeclared-writes=2, variable-property-writes=0
- CI_Upload (system/codeigniter/system/libraries/Upload.php): undeclared-writes=0, variable-property-writes=2
- CI_Loader (system/codeigniter/system/core/Loader.php): undeclared-writes=0, variable-property-writes=1
- CI_DB_driver (system/codeigniter/system/database/DB_driver.php): undeclared-writes=0, variable-property-writes=1
- CI_DB_mssql_driver (system/codeigniter/system/database/drivers/mssql/mssql_driver.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_mssql_result (system/codeigniter/system/database/drivers/mssql/mssql_result.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_mysql_result (system/codeigniter/system/database/drivers/mysql/mysql_result.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_mysqli_result (system/codeigniter/system/database/drivers/mysqli/mysqli_result.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_oci8_driver (system/codeigniter/system/database/drivers/oci8/oci8_driver.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_oci8_result (system/codeigniter/system/database/drivers/oci8/oci8_result.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_odbc_driver (system/codeigniter/system/database/drivers/odbc/odbc_driver.php): undeclared-writes=1, variable-property-writes=0
- CI_DB_odbc_result (system/codeigniter/system/database/drivers/odbc/odbc_result.php): undeclared-writes=1, variable-property-writes=0
