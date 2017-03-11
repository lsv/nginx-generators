# Nginx configuration generator

### Usage

```
./bin/nginxgenerator generate:<generator> [options] [arguments]
```

#### Generators:

| Generator | Extra options | Default value |
| --- | --- | --- |
| symfony | fastcgi_pass | `unix:/var/run/php/php7.1-fpm.sock`
| html | | |

#### Options for all generators

| Option name | Default value | Description |
| --- | --- | --- |
| nossl | false | Do not add ssl configuration |
| ssl_dir | `/etc/letsencrypt/live/__SERVER_NAME__` | SSL directory |
| root_dir | `/ext/__SERVER_NAME__/prod/current/web` | Root dir for the webfiles |
| error_log | `/var/log/nginx/__SERVER_NAME__.error.log` | Nginx error log file |
| access_log | `/var/log/nginx/__SERVER_NAME__.access.log` | Nginx access log file |
| savefile | off | Write the file directly to `nginx_dir` |
| nginx_file | `/etc/nginx/sites-available/__SERVER_NAME__` | Nginx configuration file |

#### Arguments

| Argument | Description |
| --- | --- |
| server_name | The `server_name` which will replace all `__SERVER_NAME__` in the templates |

### Example output

`./bin/nginxgenerator generate:symfony example.com`

```
server {
    listen          80;
    listen          [::]:80;
    server_name     example.com;
    return          301 https://$server_name$request_uri;
}

server {
    listen          443 ssl http2;
    listen          [::]:443 ssl http2;

    server_name     example.com;
    root            /ext/example.com/prod/current/web;

    
    ssl_certificate         /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key     /etc/letsencrypt/live/example.com/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/example.com/chain.pem;
    include                 ssl/signature.conf;

    gzip on;

    location ~* \.(?:jpg|jpeg|gif|png|ico|cur|gz|svg|svgz|mp4|ogg|ogv|webm|htc)$ {
        expires 1y;
        add_header Cache-Control "public";
        access_log off;
    }

    location ~* \.(?:css|js)$ {
        expires 1y;
        access_log off;
    }

    
    location / {
        try_files   $uri    /app.php$is_args$args;
    }

    location ~ ^/app\.php(/|$) {
        fastcgi_pass            unix:/var/run/php/php7.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include                 fastcgi_params;
        fastcgi_param           SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param           DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }
    
    error_log   /var/log/nginx/example.com.error.log;
    access_log  /var/log/nginx/example.com.access.log;
}

```
