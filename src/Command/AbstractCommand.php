<?php
namespace Lsv\NginxGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractCommand extends Command
{

    const DEFAULT_FASTCGI_PASS = 'unix:/var/run/php/php7.1-fpm.sock';
    const DEFAULT_DIR = '/ext/__SERVER_NAME__/prod/current/web';
    const DEFAULT_SSL = '/etc/letsencrypt/live/__SERVER_NAME__';
    const DEFAULT_ACCESSLOG = '/var/log/nginx/__SERVER_NAME__.access.log';
    const DEFAULT_ERRORLOG = '/var/log/nginx/__SERVER_NAME__.error.log';
    const NGINX_FILE = '/etc/nginx/sites-available/__SERVER_NAME__';

    protected function configure()
    {
        $this
            ->setName($this->setCommandName())
            ->addOption('nossl', null, InputOption::VALUE_NONE, 'Do not add SSL')
            ->addOption('ssl_dir', null, InputOption::VALUE_OPTIONAL, 'SSL directory', self::DEFAULT_SSL)
            ->addOption('root_dir', null, InputOption::VALUE_OPTIONAL, 'Directory with web files', self::DEFAULT_DIR)
            ->addOption('error_log', null, InputOption::VALUE_OPTIONAL, 'Set error log file', self::DEFAULT_ERRORLOG)
            ->addOption('access_log', null, InputOption::VALUE_OPTIONAL, 'Set access log file', self::DEFAULT_ACCESSLOG)
            ->addOption('savefile', null, InputOption::VALUE_NONE, 'Save to file to nginx directory')
            ->addOption('nginx_file', null, InputOption::VALUE_OPTIONAL, 'Nginx directory', self::NGINX_FILE)
            ->addArgument('server_name', InputArgument::REQUIRED, 'Server name')
        ;
        $this->addOptions();
    }

    abstract protected function addOptions() : void;

    abstract protected function setCommandName() : string;

    abstract protected function setVariables(InputInterface $input) : ?array;

    abstract protected function getTemplate() : string;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->loadTemplate($this->getTemplate(), $this->getVariables($input));
        if ($input->getOption('savefile')) {
            $fs = new Filesystem();
            $filename = $this->setServerNameDirs($input->getOption('nginx_file'), $input->getArgument('server_name'));
            if (! $fs->exists($filename)) {
                try {
                    $fs->dumpFile($filename, $data);
                    $output->writeln(sprintf('File has been written <info>%s</info> - %s', $filename, $input->getArgument('server_name')));
                    exit(0);
                } catch (IOException $exception) {
                    $output->writeln(sprintf('Could not write the file: <info>%s</info> - %s', $filename, $input->getArgument('server_name')));
                    exit(1);
                }
            }

            $output->writeln(sprintf('File already exists <info>%s</info> - %s', $filename, $input->getArgument('server_name')));
            exit(1);
        }

        $output->writeln($data);
    }

    protected function getVariables(InputInterface $input)
    {
        $variables = $this->setVariables($input);
        if (! $variables) {
            $variables = [];
        }

        return array_merge($variables, [
            'ssl_dir' => $this->setServerNameDirs($input->getOption('ssl_dir'), $input->getArgument('server_name')),
            'error_log' => $this->setServerNameDirs($input->getOption('error_log'), $input->getArgument('server_name')),
            'access_log' => $this->setServerNameDirs($input->getOption('access_log'), $input->getArgument('server_name')),
            'server_name' => $input->getArgument('server_name'),
            'root_dir' => $this->setServerNameDirs($input->getOption('root_dir'), $input->getArgument('server_name')),
            'usessl' => ! $input->getOption('nossl')
        ]);
    }

    protected function setServerNameDirs(string $dir, string $servername) : string
    {
        return str_replace('__SERVER_NAME__', $servername, $dir);
    }

    protected function loadTemplate(string $template, array $variables) : string
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../Templates');
        $twig = new \Twig_Environment($loader);
        $template = $twig->load($template);
        return $template->render($variables);
    }

}
