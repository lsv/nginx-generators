# Nginx configuration generator

### Usage

```
./bin/nginxgenerator generate:<generator> [options] [arguments]
```

#### Generators:

| Generator | Extra options |
| --- | --- |
| symfony | fastcgi_pass | 
| html | |

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
