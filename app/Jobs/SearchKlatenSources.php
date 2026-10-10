<?php

namespace App\Jobs;

use App\Models\CrawlRun;
use App\Models\Source;
use App\Models\SourceDomain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Throwable;

class SearchKlatenSources implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public array $backoff = [60, 300];

    public function __construct(public ?string $from = null, public ?string $to = null, public string $kind = 'daily') {}

    public function handle(): void
    {
        $run = CrawlRun::create(['status' => 'running', 'started_at' => now()]);
        $key = config('crime-timeline.brave_api_key');
        $queries = config('crime-timeline.search.queries');
        if (! is_string($key) || $key === '' || ! is_array($queries) || $queries === []) {
            $this->failRun($run, 'Configuration unavailable.');

            return;
        }

        $created = 0;
        $found = 0;
        try {
            foreach (array_slice($queries, 0, 12) as $query) {
                if (! is_string($query) || trim($query) === '') {
                    continue;
                }
                $params = ['q' => trim($query).' Klaten', 'country' => config('crime-timeline.search.country', 'ID'), 'count' => min(20, (int) config('crime-timeline.search.results_per_query', 20))];
                if ($this->from !== null && $this->to !== null) {
                    $params['q'] .= ' after:'.$this->from.' before:'.$this->to;
                }
                $response = Http::withHeaders(['X-Subscription-Token' => $key, 'Accept' => 'application/json'])
                    ->timeout(15)->get(config('crime-timeline.search.endpoint'), $params);
                if (! $response->successful()) {
                    throw new RequestException($response);
                }
                $results = $response->json('web.results');
                if (! is_array($results)) {
                    throw new \UnexpectedValueException('Malformed search response.');
                }
                foreach (array_slice($results, 0, 20) as $item) {
                    if (! is_array($item) || ! is_string($item['url'] ?? null)) {
                        continue;
                    }
                    $found++;
                    $url = $this->canonicalUrl($item['url']);
                    if ($url === null) {
                        continue;
                    }
                    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                    $domain = SourceDomain::where('domain', $host)->first();
                    if ($domain && ! $domain->enabled) {
                        continue;
                    }
                    $source = Source::firstOrCreate(['url' => $url], [
                        'domain' => $host, 'publisher' => $host, 'title' => 'Pending source review', 'excerpt' => '', 'fetch_status' => 'pending',
                    ]);
                    if ($source->wasRecentlyCreated) {
                        $created++;
                        ProcessCrimeSource::dispatch($source->id, $run->id);
                    }
                }
            }
            $run->update(['status' => 'completed', 'ended_at' => now(), 'results_found' => $found, 'sources_created' => $created]);
        } catch (ConnectionException) {
            $this->failRun($run, 'Search provider connection failed.', $found, $created);
        } catch (RequestException) {
            $this->failRun($run, 'Search provider returned an unsuccessful response.', $found, $created);
        } catch (Throwable) {
            $this->failRun($run, 'Search response or processing failed.', $found, $created);
        }
    }

    private function failRun(CrawlRun $run, string $summary, int $found = 0, int $created = 0): void
    {
        $run->update(['status' => 'failed', 'ended_at' => now(), 'results_found' => $found, 'sources_created' => $created, 'error_summary' => $summary]);
    }

    private function canonicalUrl(string $url): ?string
    {
        $parts = parse_url(trim($url));
        if (! is_array($parts) || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }
        $host = strtolower(rtrim($parts['host'], '.'));
        if (! filter_var($host, FILTER_VALIDATE_IP) && ! str_contains($host, '.')) {
            return null;
        }
        $path = preg_replace('~/+~', '/', $parts['path'] ?? '/');

        return strtolower($parts['scheme']).'://'.$host.(isset($parts['port']) ? ':'.$parts['port'] : '').($path ?: '/');
    }
}
