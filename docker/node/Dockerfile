ARG NODE_VERSION=17

FROM node:${NODE_VERSION}-alpine as node_base

WORKDIR /srv/app

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		g++ \
		gcc \
		git \
		make \
        python3 \
	;

RUN ln -s /usr/bin/python3 /usr/bin/python
RUN set -eux; \
	yarn global add eclint;
