FROM nginx:1.25-alpine

# Instalar dependencias necesarias
RUN apk add --no-cache curl

# Eliminar la configuración por defecto de Nginx
RUN rm /etc/nginx/conf.d/default.conf

# Modificar la ubicación del PID en la configuración principal
RUN sed -i 's#pid.*#pid /tmp/nginx.pid;#' /etc/nginx/nginx.conf && \
    sed -i 's/^user/#user/' /etc/nginx/nginx.conf

# Copiar configuración personalizada 
COPY nginx.conf /etc/nginx/conf.d/

# Configurar permisos
RUN chown -R nginx:nginx /var/cache/nginx && \
    chown -R nginx:nginx /var/log/nginx && \
    chown -R nginx:nginx /etc/nginx/conf.d && \
    chown -R nginx:nginx /tmp

# Cambiar a usuario no privilegiado
USER nginx

# Exponer puerto
EXPOSE 80

# Comando para iniciar Nginx
CMD ["nginx", "-g", "daemon off;"]
