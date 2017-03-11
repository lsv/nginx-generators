<?php
namespace Lsv\NginxGenerator\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class SymfonyCommand extends AbstractCommand
{

    protected function addOptions() : void
    {
        $this->addOption(
            'fastcgi_pass',
            null,
            InputOption::VALUE_OPTIONAL,
            'Set fastcgi pass',
            self::DEFAULT_FASTCGI_PASS
        );
    }

    protected function setCommandName() : string
    {
        return 'generate:symfony';
    }

    protected function setVariables(InputInterface $input) : ?array
    {
        return [
            'fastcgi_pass' => $input->getOption('fastcgi_pass')
        ];
    }

    protected function getTemplate() : string
    {
        return 'symfony.twig';
    }

}
