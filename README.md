 # 太火鸟 saas
 ----简介-----
 
 
 ###运行环境要求
 
 * PHP >= 7.0.0
 * OpenSSL PHP Extension
 * PDO PHP Extension
 * Mbstring PHP Extension
 * Tokenizer PHP Extension
 
 建议环境：Nginx 1.10 / PHP 7.0.* / Mysql 5.7 / Laravel 5.4
 使用说明：[英文文档](https://laravel.com/docs/5.4)；[中文文档](http://laravel-china.org/docs/5.4)。
 
 ###安装使用
 
 #####第一步：安装composer包管理器
 
 访问[composer](http://pkg.phpcomposer.com/)，根据文档说明安装composer。
     
 #####第二步：开发环境生成ssh公钥。
 
 ```
 ssh-keygen -t rsa -C "your email!"
 ```
 
 #####第三步：克隆saas代码
 
 ```
 
git@github.com:purpen/mallache.git

 ```
 
 #####第四步：composer安装框架文件
 
 ```
 composer install
 ```
 
 ######Remark
 * 安装 Laravel 之后，需要你配置 **storage** 和 **bootstrap/cache** 目录的读写(777)权限。
 
 ```
 sudo chmod -R 777 bootstrap/cache
 ```
 * 如出现无法正常访问，并且日志为空，请检测日志权限
 ```
 sudo chmod -R 777 storage/logs/xxx.log
 ```
 ```
 sudo chmod -R 777 storage 
 sudo chmod -R 777 storage/framework/cache
 sudo chmod -R 777 storage/framework/views
 ```
 
 * 安装 Laravel 之后，一般应用程序根目录会有一个 **.env** 的文件。如果没有的话，复制 **.env.example** 并重命名为 **.env** 。
 
 ```
 php -r "copy('.env.example', '.env');"
 ```
 
 * 更新系统秘钥 （错误：No supported encrypter found，laravel5.1开始APP_KEY必须是长度32且有cipher）
 ```
 php artisan key:generate
 ```
 * 重新加载插件
 ```
 composer dump-autoload
 ```
 * 自定义函数库和类库目录
 ```
 app/helper.php和app/Libraries/
 ```
 
 ######数据库
 * 创建数据库 saas
 
 ######基本配置
 nginx加上优雅链接:
 
 location / {
     try_files $uri $uri/ /index.php?$query_string;
 }
 
 
 ###### 生成API文档
 apidoc -i app/Http/Controllers/Api/ -o public/apidoc
 
 ###### 请求api版本
 Accept: application/x.saas.v1+json

 ###### 启动队列监听
 php artisan queue:work --queue=mallache --sleep=3 --tries=3  --daemon

 ###### 计算设计公司加权分数score
 php artisan Weighted:calculation
 
 ###### 批量生成用户账号
 php artisan user:create --count=10 --type=2 创建用户命令: --count 创建数量  --type 1.需求公司；2.设计公司
 
 ###### 创建更新图片素材token
 php artisan Update:token
 
 ###### 更新设计公司案例数据结构迁移
 php artisan designCase:change
 
 ##### 项目需求设计类型数据结构修改
 php artisan item:designTypesChange
 
 ###### 注销账户
 php artisan unset:user --account=
 
 ##### 修改设计公司主账号信息（子账户正式上线后不可使用）
 php artisan designUser:change
 
 
 ##### 统计子账户数量（最多10个子账户）
 php artisan childCount:update
 
 ##### 同步需求项目名称到item表
 php artisan ItemName:update
 
 ##### 同步产品UI老数据到项目表中
  php artisan update:itemUProduct
 ##### 更新项目下任务总数量，完成未完成数量的统计
 php artisan taskCount:update
