FROM nginx:1.27-alpine

WORKDIR /var/www/html

# Copiar únicamente los archivos públicos de Laravel
COPY public ./public

# Copiar la configuración de Nginx para Kubernetes
COPY k8s/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]