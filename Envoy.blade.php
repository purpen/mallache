@servers(['test_server' => 'tianxiaoyi@120.132.59.206'])

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



