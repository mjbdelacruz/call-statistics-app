<?php

namespace App\Command;

use App\Entity\PhoneNumber;
use App\Repository\PhoneRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CountryInfoParserCommand extends Command
{
    protected static $defaultName = 'app:country-info-parser';

    private PhoneRepository $phoneRepository;


    public function __construct(PhoneRepository $phoneRepository)
    {
        parent::__construct();
        $this->phoneRepository = $phoneRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Parses countryinfo.txt file and maps phone numbers to continent');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---------------------------');
        $output->writeln('Starting CountryInfo Parser');
        $output->writeln('---------------------------');

        $finder = new Finder();
        $finder->files()
            ->name('countryInfo.txt')
            ->in(__DIR__ . '/../Resources/');

        $phoneNumbers = [];
        if ($finder->hasResults()) {
            foreach ($finder AS $file) {
                $output->writeln(sprintf('Parsing file: %s', $file->getRealPath()));

                $contents = $file->getContents();
                $lines = explode("\n", $contents);
                foreach ($lines AS $line) {
                    // We want to ignore lines that are empty OR starts with '#'
                    if (empty($line) || str_starts_with($line, "#")) {
                        continue;
                    }

                    // explode the rows to extract continents, country and phone numbers
                    $columns = explode("\t", $line);
                    $country = $columns[4];
                    $continent = $columns[8];
                    $phone = $columns[12];

                    if (!empty($continent) && !empty($phone)) {
                        $phoneNumbers[] = new PhoneNumber($phone, $country, $continent);
                    }
                }
            }

            $output->writeln(sprintf('Inserting %d country info to the database.', count($phoneNumbers)));
            try {
                $this->phoneRepository->bulkInsert($phoneNumbers);

                $output->writeln('Inserted successfully.');
            } catch (\Exception $exception) {
                $output->writeln('Exception has occured while storing country info to the database.');
            }
        }

        return Command::SUCCESS;
    }
}