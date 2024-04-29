FROM httpd:2.4-bookworm

RUN sed -i 's/#LoadModule cgid_module/LoadModule cgid_module/g' /usr/local/apache2/conf/httpd.conf

EXPOSE 80/tcp
