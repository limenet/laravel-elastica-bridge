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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastica-bridge:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays the status of the configured Elasticsearch indices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected ElasticaClient $elastica, protected IndexRepository $indexRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $table = new Table($this->output);
        $table
            ->setHeaders(['Host', 'Port', 'Version'])
            ->setRows([[
                $this->elastica->getClient()->getConfig('host'),
                $this->elastica->getClient()->getConfig('port'),
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
                } catch (BlueGreenIndicesIncorrectlySetupException $exception) {
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

        return 0;
    }

    protected function formatBoolean(bool $val): string
    {
        if ($val) {
            return '✓';
        }

        return '✗';
    }

    /**
     * @see https://stackoverflow.com/a/2510540
     */
    protected function formatBytes(int $bytes): string
    {
        $base = log($bytes, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(1024 ** ($base - floor($base)), 2).' '.$suffixes[(int) floor($base)].'B';
    }
}
