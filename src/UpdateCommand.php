<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;


class UpdateCommand extends Command
{

    const MANIFEST_FILE = 'http://gitlab.dev/brice.burgess/repo-insight/raw/master/manifest.json';

    protected function configure()
    {
        $this->setName('update')->setDescription('update repo-insight.phar to latest');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}

