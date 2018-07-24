@servers(['test_server' => 'tianxiaoyi@120.132.59.206','server_1' => 'tianxiaoyi@120.132.50.251'])

# 铟果测试环境自动部署
@task('test_server', ['on' => 'test_server', 'confirm' => true])
    cd /opt/project/mallache
    eval `ssh-agent -s`
    ssh-add ~/.ssh/herp
    @if ($branch)
        git pull origin {{ $branch }}
    @else
        git pull origin master
    @endif
    /opt/php-7.0/bin/php artisan migrate
    /opt/php-7.0/bin/php /usr/local/bin/composer install
    /opt/php-7.0/bin/php /usr/local/bin/composer dump-autoload
    apidoc -i app/Http/Controllers/Api/ -o public/apidoc
@endtask


# 铟果正式环境自动部署
@task('server', ['on' => ['server_1'], 'confirm' => true])
cd /opt/project/mallache
eval `ssh-agent -s`
ssh-add ~/.ssh/herp
@if ($branch)
    git pull origin {{ $branch }}
@else
    git pull origin master
@endif
/opt/php-7.0/bin/php artisan migrate
/opt/php-7.0/bin/php /usr/local/bin/composer install
/opt/php-7.0/bin/php /usr/local/bin/composer dump-autoload
@endtask



