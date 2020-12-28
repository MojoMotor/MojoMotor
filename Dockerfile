FROM ubuntu:trusty

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

RUN apt-get -y install python-software-properties

RUN apt-get -y install php5
RUN apt-get -y install php5-mysql

RUN mkdir app

WORKDIR /app

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]