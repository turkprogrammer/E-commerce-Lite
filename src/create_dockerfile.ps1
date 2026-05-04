$content = @"
FROM phpdockerio/php:8.5-fpm
WORKDIR "/application"

RUN apt-get update `
    && apt-get -y --no-install-recommends install `
        php8.5-sqlite3 `
        php8.5-xdebug `
    && apt-get clean `
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN echo "" >> /etc/php/8.5/fpm/php.ini `
    && echo "[opcache]" >> /etc/php/8.5/fpm/php.ini `
    && echo "zend_extension=opcache.so" >> /etc/php/8.5/fpm/php.ini `
    && echo "opcache.enable=1" >> /etc/php/8.5/fpm/php.ini `
    && echo "opcache.memory_consumption=256" >> /etc/php/8.5/fpm/php.ini `
    && echo "opcache.interned_strings_buffer=16" >> /etc/php/8.5/fpm/php.ini `
    && echo "opcache.max_accelerated_files=20000" >> /etc/php/8.5/fpm/php.ini `
    && echo "opcache.revalidate_freq=60" >> /etc/php/8.5/fpm/php.ini `
    && echo "opcache.fast_shutdown=1" >> /etc/php/8.5/fpm/php.ini

RUN echo "" >> /etc/php/8.5/cli/php.ini `
    && echo "[opcache]" >> /etc/php/8.5/cli/php.ini `
    && echo "zend_extension=opcache.so" >> /etc/php/8.5/cli/php.ini `
    && echo "opcache.enable=1" >> /etc/php/8.5/cli/php.ini `
    && echo "opcache.memory_consumption=256" >> /etc/php/8.5/cli/php.ini `
    && echo "opcache.interned_strings_buffer=16" >> /etc/php/8.5/cli/php.ini `
    && echo "opcache.max_accelerated_files=20000" >> /etc/php/8.5/cli/php.ini `
    && echo "opcache.revalidate_freq=60" >> /etc/php/8.5/cli/php.ini `
    && echo "opcache.fast_shutdown=1" >> /etc/php/8.5/cli/php.ini
"@

Set-Content -Path "d:\docker-projects\symfony\phpdocker\php-fpm\Dockerfile" -Value $content -Encoding UTF8
Write-Host "Dockerfile успешно создан!"
