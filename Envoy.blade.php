@servers([
'test_server' => 'tianxiaoyi@120.132.59.206',
'server_1' => 'tianxiaoyi@120.132.50.251',
])

# 铟果测试环境自动部署
@task('test_server', ['on' => 'test_server', 'confirm' => true])
cd /opt/project/mallache
set-ssh
@if ($branch)
    git pull origin {{ $branch }}
@else
    git pull origin master
@endif
php artisan migrate
php composer install
php composer dump-autoload
apidoc -i app/Http/Controllers/Api/ -o public/apidoc
@endtask


# 铟果正式环境自动部署
@task('server', ['on' => ['server_1'], 'confirm' => true])
cd /opt/project/mallache
set-ssh
@if ($branch)
    git pull origin {{ $branch }}
@else
    git pull origin master
@endif
php artisan migrate
php composer install
php composer dump-autoload
@endtask
