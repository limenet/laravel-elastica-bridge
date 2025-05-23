<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Commands;

use Illuminate\Console\Command;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Symfony\Component\Console\Helper\Table;

class StatusCommand extends Command
{
    protected $signature = 'elastica-bridge:status';

    protected $description = 'Displays the status of the configured Elasticsearch indices';

    public function __construct(
        private readonly ElasticaClient $elastica,
        private readonly IndexRepository $indexRepository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $table = new Table($this->output);
        $table
            ->setHeaders(['Host', 'Port', 'Version'])
            ->setRows([[
                $this->elastica->getClient()->getTransport()->getNodePool()->nextNode()->getUri()->getHost(),
                $this->elastica->getClient()->getTransport()->getNodePool()->nextNode()->getUri()->getPort(),
                $this->elastica->getClient()->getVersion(),
            ]])
            ->setHeaderTitle('Cluster');
        $table->render();

        $this->output->writeln('');

        $data = [];
        foreach ($this->indexRepository->all() as $indexConfig) {
            $name = $indexConfig->getName();
            $exists = $indexConfig->getElasticaIndex()->exists();
            $hasBlueGreen = $indexConfig->hasBlueGreenIndices();
            $numDocs = 'N/A';
            $size = 'N/A';
            $activeBlueGreen = 'N/A';

            if ($exists) {
                $stats = $indexConfig->getElasticaIndex()->getStats()->get()['indices'];
                $stats = array_values($stats)[0]['total'];
                $numDocs = $stats['docs']['count'];
                $size = $stats['store']['size_in_bytes'];
            }

            if ($hasBlueGreen) {
                try {
                    $activeBlueGreen = $indexConfig->getBlueGreenActiveElasticaIndex()->getName();
                } catch (BlueGreenIndicesIncorrectlySetupException) {
                    $hasBlueGreen = false;
                }
            }

            $data[] = [
                $name,
                $this->formatBoolean($exists),
                $numDocs,
                is_int($size) ? $this->formatBytes($size) : $size,
                sprintf(
                    '%s / %s',
                    $this->formatBoolean($hasBlueGreen),
                    $activeBlueGreen
                ),
            ];
        }

        $table = new Table($this->output);
        $table
            ->setHeaders(['Name', 'Exists', '# Docs', 'Size', 'Blue/Green: present / active'])
            ->setRows($data)
            ->setHeaderTitle('Indices');
        $table->render();

        return self::SUCCESS;
    }

    private function formatBoolean(bool $val): string
    {
        if ($val) {
            return '✓';
        }

        return '✗';
    }

    /**
     * @see https://stackoverflow.com/a/2510540
     */
    private function formatBytes(int $bytes): string
    {
        $base = log($bytes, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(1024 ** ($base - floor($base)), 2).' '.$suffixes[(int) floor($base)].'B';
    }
}
