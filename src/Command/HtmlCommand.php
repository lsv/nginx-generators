<?php
namespace Lsv\NginxGenerator\Command;

use Symfony\Component\Console\Input\InputInterface;

class HtmlCommand extends AbstractCommand
{

    protected function addOptions() : void
    {
    }

    protected function setCommandName() : string
    {
        return 'generate:html';
    }

    protected function setVariables(InputInterface $input) : ?array
    {
        return null;
    }

    protected function getTemplate() : string
    {
        return 'html.twig';
    }

}
