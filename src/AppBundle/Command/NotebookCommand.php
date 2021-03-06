<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use AppBundle\Entity\Notebook;

/**
 * Class NotebookCommand
 *
 * @package AppBundle\Command
 */
class NotebookCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('parse:notebooks')
            ->setDescription('Parses notebooks from Hotline.ua')
            ->setHelp("")
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://hotline.ua/computer/noutbuki-netbuki/385943-883-85763-85764-85765/');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $crawler = $crawler->filter('img.max-120');
        $count = 0;

        foreach ($crawler as $el) {
            $notebook = new Notebook();
            $notebook->setImage('http://hotline.ua' . $el->getAttribute('src'));
            $notebook->setTitle($el->getAttribute('alt'));
            $em->persist($notebook);
            $count++;
        }

        $em->flush();

        $output->writeln([
            'Count of added records:'.$count,
        ]);
    }
}