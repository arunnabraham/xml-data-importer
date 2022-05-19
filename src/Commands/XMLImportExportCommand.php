<?php

declare(strict_types=1);

namespace Arunnabraham\XmlDataImporter\Commands;

use Arunnabraham\XmlDataImporter\Service\XmlImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class XMLImportExportCommand extends Command
{
    protected function configure()
    {
        $this->setName('export')
            ->setDescription('Runs XML Export to various formats')
            // ->setHelp('Demonstration of custom commands created by Symfony Console component.')
            ->addArgument('exportformat', InputArgument::REQUIRED, 'Input export format eg: csv or json');
    //        ->addOption('inputfile', 'i', InputOption::VALUE_REQUIRED, 'Input file path')
    //        ->addOption('inputtype', 't', InputOption::VALUE_REQUIRED, 'Specify what input type whether remote or local');
    }

    private function validateInputs(InputInterface $input)
    {
        $acceptedInputs = [
            'exportformat' =>  [
                'csv',
                'json',
            ]
        ];

        $exportFormat = $acceptedInputs['exportformat'];
        $messages = [];

        if (in_array($input->getArgument('exportformat'), $exportFormat, true) === false) {
            array_push($messages, '<error>Invalid Argument Export Format</error>');
        }

        return $messages;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validate = $this->validateInputs($input);
        if (!empty($validate)) {
            $output->writeln(sprintf('<comment>Input Error:</comment>' . PHP_EOL . implode(PHP_EOL, $validate)));
        } else {
            $processImportExport = (new XmlImportService())->processImport(
                $input->getArgument('exportformat'),
                env('DEFAULT_OUTPUT_DIR_PATH')
            );
            if (str_contains($processImportExport, 'Error')) {
                $output->writeln(sprintf('<comment>%s</comment>', $processImportExport));
            } else {
                $output->writeln('<info>Export File:</info>'.PHP_EOL);
                $output->writeln(sprintf('%s', $processImportExport.PHP_EOL));
            }
        }
        return 0;
    }
}
