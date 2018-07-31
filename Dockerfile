#
# MAINTAINER        Allen Luo <movoin@gmail.com>
# DOCKER-VERSION    1.12.3
#

FROM        movoin/devops-swoole:2
MAINTAINER  Allen Luo <movoin@gmail.com>

COPY dockerfiles/etc/ /etc/

WORKDIR /app/

RUN /opt/docker/bin/install.sh \
    && /opt/docker/bin/bootstrap.sh
