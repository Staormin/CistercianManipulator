FROM php:8.3-cli-alpine

ARG USER_ID
ARG GROUP_ID

RUN addgroup -g ${GROUP_ID} -S app
RUN adduser -u ${USER_ID} -S app -G app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
    git \
    unzip \
    fish \
    curl

RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

USER app

RUN curl https://raw.githubusercontent.com/oh-my-fish/oh-my-fish/master/bin/install -o /tmp/installomf

RUN chown app:app /tmp/installomf && \
    chmod u+x /tmp/installomf && \
    /tmp/installomf --noninteractive --yes

RUN fish -c "omf install bobthefish"

STOPSIGNAL SIGKILL

WORKDIR /app

COPY --chown=app:app . /app

SHELL ["/usr/bin/fish", "-c"]