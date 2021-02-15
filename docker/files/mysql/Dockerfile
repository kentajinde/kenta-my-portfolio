FROM mysql:5.7
RUN sed -e 's/^ *user *= *mysql$/user = root/' -i /etc/mysql/my.cnf
COPY ./my.cnf /etc/mysql/conf.d/