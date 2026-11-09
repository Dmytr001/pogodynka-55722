<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Displays weather forecast for a location',
)]
class WeatherLocationCommand extends Command
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly WeatherUtil $weatherUtil,
    ) {
        parent::__construct();
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);

        if (!$location) {
            $io->error(sprintf('Location with ID "%s" not found.', $id));

            return Command::FAILURE;
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Location: %s', $location->getName()));

        $io->table(
            ['Date', 'Temperature', 'Wind speed', 'Humidity', 'Pressure'],
            array_map(fn($m) => [
                $m->getDate()->format('Y-m-d'),
                $m->getCelsius(),
                $m->getWindSpeed(),
                $m->getHumidity(),
                $m->getPressure(),
            ], $measurements)
        );

        return Command::SUCCESS;
    }
}
