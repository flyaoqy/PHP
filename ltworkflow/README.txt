1、工作流引擎在 ltworkflow文件夹下，遵循开源协议 Apache License V2。

2、示例工程采用了CodeIgniter框架，同时使用了jquery，dataTables，bootstrap，layer.js，go.js等插件，这些都不属于ltworkflow开源项目，使用中如有版权问题与本人无关。

3、部署工程首先修改如下配置文件：
application\config\config.php,
application\config\database.php ,
ltworkflow\service\config\database.php 

4、sql/workflow.sql（mysql）为本工程表结构和示例数据。

5、更多信息请参考 http://phpworkflow.cn

===============================================================================
ltworkflow 2.0 升级列表
1、PHP版本兼容 PHP 5.3 ~ PHP 7
2、数据库驱动支持 mssql,mysql,mysqli
3、添加命名空间