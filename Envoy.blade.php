@servers(['local' => '127.0.0.1','test_server' => 'tianxiaoyi@120.132.59.206'])

# 本地测试部署任务
@task('local', ['on' => 'local'])
    cd /Users/llh/mycode/app/mallache
    php artisan migrate
    composer install
    composer dump-autoload
    apidoc -i app/Http/Controllers/Api/ -o public/apidoc
@endtask

# 铟果测试环境部署任务
@task('test_server', ['on' => 'test_server'])
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
    composer dump-autoload
    apidoc -i app/Http/Controllers/Api/ -o public/apidoc
@endtask



