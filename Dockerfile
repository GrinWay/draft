FROM yobasystems/alpine-docker:dind-x86_64

RUN apk update

RUN apk add ncurses-dev --no-interactive --no-progress --quiet

RUN apk add --no-cache bash
RUN apk add bash-doc --no-interactive --no-progress --quiet
RUN apk add bash-completion --no-interactive --no-progress --quiet

RUN apk add vim --no-interactive --no-progress --quiet

RUN apk add openssh --no-interactive --no-progress --quiet
RUN apk add rsync --no-interactive --no-progress --quiet

RUN apk add make --no-interactive --no-progress --quiet

RUN apk add git --no-interactive --no-progress --quiet

RUN apk add zip --no-interactive --no-progress --quiet
RUN apk add unzip --no-interactive --no-progress --quiet

RUN apk add supervisor --no-interactive --no-progress --quiet

RUN apk add php --no-interactive --no-progress --quiet
RUN apk add php-session --no-interactive --no-progress --quiet
RUN apk add php-ctype --no-interactive --no-progress --quiet
RUN apk add php-sodium --no-interactive --no-progress --quiet
RUN apk add php-simplexml --no-interactive --no-progress --quiet
RUN apk add php-xmlwriter --no-interactive --no-progress --quiet
RUN apk add php-xsl --no-interactive --no-progress --quiet
RUN apk add php-dom --no-interactive --no-progress --quiet
RUN apk add php-bcmath --no-interactive --no-progress --quiet
RUN apk add php-curl --no-interactive --no-progress --quiet
RUN apk add php-json --no-interactive --no-progress --quiet
RUN apk add php-mbstring --no-interactive --no-progress --quiet
RUN apk add php-tokenizer --no-interactive --no-progress --quiet
RUN apk add php-xml --no-interactive --no-progress --quiet
RUN apk add php-zip --no-interactive --no-progress --quiet
RUN apk add php-pcntl --no-interactive --no-progress --quiet
RUN apk add php-common --no-interactive --no-progress --quiet
RUN apk add php-fpm --no-interactive --no-progress --quiet
RUN apk add php-pdo --no-interactive --no-progress --quiet
RUN apk add php-opcache --no-interactive --no-progress --quiet
RUN apk add php-phar --no-interactive --no-progress --quiet
RUN apk add php-iconv --no-interactive --no-progress --quiet
RUN apk add php-cli --no-interactive --no-progress --quiet
RUN apk add php-openssl --no-interactive --no-progress --quiet
RUN apk add php-fileinfo --no-interactive --no-progress --quiet
RUN apk add php-pdo_mysql --no-interactive --no-progress --quiet
RUN apk add php-gd --no-interactive --no-progress --quiet
RUN apk add php-ldap --no-interactive --no-progress --quiet

RUN apk add rabbitmq-c --no-interactive --no-progress --quiet
RUN apk add rabbitmq-c-dev --no-interactive --no-progress --quiet
RUN apk add php83-pecl-amqp --no-interactive --no-progress --quiet

RUN apk add php-amqp --no-interactive --no-progress --quiet
RUN mkdir -p /rabbitmq/rabbitmq_server-4.0.5
RUN wget -O /rabbitmq/rabbitmq_server-4.0.5/rabbitmq.tar.xz https://github.com/rabbitmq/rabbitmq-server/releases/download/v4.0.5/rabbitmq-server-generic-unix-4.0.5.tar.xz
RUN tar -xf /rabbitmq/rabbitmq_server-4.0.5/rabbitmq.tar.xz -C /rabbitmq/rabbitmq_server-4.0.5
RUN apk add erlang --no-interactive --no-progress --quiet

RUN apk add composer --no-interactive --no-progress --quiet

RUN apk add curl --no-interactive --no-progress --quiet
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN export PATH="$HOME/.symfony5/bin:$PATH"
RUN mv $HOME/.symfony5/bin/symfony /usr/local/bin/symfony

RUN mkdir -p /etc/supervisor.d/
RUN ln -s /volumes/drafts/config/messenger-worker.ini /etc/supervisor.d/messenger-worker.ini

ENV PATH="$PATH:/rabbitmq/rabbitmq_server-4.0.5/rabbitmq_server-4.0.5/sbin"

COPY . /app

RUN composer install --working-dir=/app -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

# overwrites an existing file: https://www.php.net/manual/en/function.copy.php
#RUN php -r "is_file('/app/workflows/Docker/.env.local') && copy('/app/workflows/Docker/.env.local', '/app/.env.local');"

WORKDIR /app/public

CMD ["php", "-S", "0.0.0.0:80"]
