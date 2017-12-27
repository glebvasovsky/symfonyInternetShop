<?php

namespace AppBundle\Command;

use AppBundle\Service\CreateProduct;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProductCommand extends ContainerAwareCommand
{
    /**
     * @var CreateProduct 
     */
    protected $createProduct;
    
    /**
     * @param CreateProduct $createProduct
     */
    public function __construct(CreateProduct $createProduct) {
        parent::__construct();
        
        $this->createProduct = $createProduct;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:create-product')
            ->setDescription('Загружает данные из csv файла в БД')
            ->setHelp('Эта команда читает csv файл построчно и заполняет БД значениями.'
                    . 'Имя файла передаётся в качестве аргумента '
                    . 'Файл должен лежать в директории /app/Resourses/doc/')
            ->addArgument('catalogFileName', InputArgument::REQUIRED)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            'Заполнение базы данных...',
            '============',
            '',
        ]);
        
        $this->createProduct->create(
                realpath($this->getContainer()->getParameter('kernel.project_dir')),
                $input->getArgument('catalogFileName')
            );
        
        $output->writeln('Готово!');
    }
     
}
