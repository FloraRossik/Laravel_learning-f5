@servers(['test' => '127.0.0.1'])

@story('local_tests')
    copy_env
    migrations
    tests
@endstory

@story('tests_advanced')
    copy_env
    migrations
    tests_advanced
@endstory

@task('copy_env', ['on' => 'test'])
    echo "copying env\n"
    cp /srv/www/laravel-learing/.env.$CI_PROJECT_NAME .env.testing
@endtask

@task('migrations', ['on' => 'test'])
    echo "running migrations\n"
    php artisan --env=testing migrate
@endtask

@task('tests', ['on' => 'test'])
    echo "running tests\n"
    php vendor/bin/phpunit
@endtask

@task('tests_advanced', ['on' => 'test'])
    echo "running advanced tests\n"
    rm -r tests
    cp -r /srv/www/laravel-learing/tests_advanced tests
    php vendor/bin/phpunit
@endtask
